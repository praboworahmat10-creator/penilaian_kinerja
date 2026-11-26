<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PerformanceController extends Controller
{
    public function dashboard(): View
    {
        $period = now()->format('Y-m');
        $totalEmployees = DB::table('pegawai')->count();
        $currentPeriodCount = DB::table('penilaian_kinerja')
            ->where('periode', $period)
            ->count();
        $assessedEmployeeCount = DB::table('penilaian_kinerja')
            ->where('periode', $period)
            ->distinct()
            ->count('pegawai_id');
        $pendingCount = max(0, $totalEmployees - $assessedEmployeeCount);
        $averageScore = DB::table('penilaian_kinerja')
            ->where('periode', $period)
            ->avg('total');
        $recentAssessments = DB::table('penilaian_kinerja as penilaian')
            ->join('pegawai', 'pegawai.id', '=', 'penilaian.pegawai_id')
            ->select('penilaian.id', 'penilaian.periode', 'penilaian.total', 'pegawai.nama', 'pegawai.jabatan')
            ->orderByDesc('penilaian.periode')
            ->orderByDesc('penilaian.updated_at')
            ->limit(5)
            ->get()
            ->map(function (object $assessment): object {
                $assessment->predikat = $this->predicate((float) $assessment->total);

                return $assessment;
            });

        return view('dashboard', [
            'period' => $period,
            'periodLabel' => now()->locale('id')->translatedFormat('F Y'),
            'totalEmployees' => $totalEmployees,
            'currentPeriodCount' => $currentPeriodCount,
            'averageScore' => $averageScore === null ? null : (float) $averageScore,
            'pendingCount' => $pendingCount,
            'recentAssessments' => $recentAssessments,
        ]);
    }

    public function employees(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);
        $query = DB::table('pegawai')->orderBy('nama');
        $search = trim($filters['search'] ?? '');

        if ($search !== '') {
            $query->where(function ($query) use ($search): void {
                $query->where('nama', 'like', '%'.$search.'%')
                    ->orWhere('jabatan', 'like', '%'.$search.'%');
            });
        }

        $employees = $query->get();

        return view('performance.employees.index', compact('employees'));
    }

    public function createEmployee(): View
    {
        return view('performance.employees.form');
    }

    public function storeEmployee(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'jabatan' => ['required', 'string', 'max:100'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
        ]);

        $data['created_at'] = now();
        $data['updated_at'] = now();
        DB::table('pegawai')->insert($data);

        return redirect()->route('employees.index')->with('success', 'Data pegawai berhasil ditambahkan.');
    }

    public function editEmployee(int $id): View
    {
        $employee = DB::table('pegawai')->where('id', $id)->first();
        abort_if($employee === null, 404);

        return view('performance.employees.form', compact('employee'));
    }

    public function updateEmployee(Request $request, int $id): RedirectResponse
    {
        abort_unless(DB::table('pegawai')->where('id', $id)->exists(), 404);

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'jabatan' => ['required', 'string', 'max:100'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
        ]);

        $data['updated_at'] = now();
        DB::table('pegawai')->where('id', $id)->update($data);

        return redirect()->route('employees.index')->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function destroyEmployee(int $id): RedirectResponse
    {
        abort_unless(DB::table('pegawai')->where('id', $id)->exists(), 404);

        if (DB::table('penilaian_kinerja')->where('pegawai_id', $id)->exists()) {
            return back()->withErrors([
                'pegawai' => 'Pegawai yang sudah memiliki riwayat penilaian tidak dapat dihapus.',
            ]);
        }

        DB::table('pegawai')->where('id', $id)->delete();

        return redirect()->route('employees.index')->with('success', 'Data pegawai berhasil dihapus.');
    }

    public function categories(): View
    {
        $categories = DB::table('kategori_kinerja')->orderBy('urutan')->get();

        return view('performance.categories', compact('categories'));
    }

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'periode' => ['nullable', 'date_format:Y-m'],
        ]);
        $query = DB::table('penilaian_kinerja as penilaian')
            ->join('pegawai', 'pegawai.id', '=', 'penilaian.pegawai_id')
            ->select(
                'penilaian.id',
                'penilaian.periode',
                'penilaian.total',
                'penilaian.penilai',
                'penilaian.created_at',
                'pegawai.nama',
                'pegawai.jabatan'
            );
        $search = trim($filters['search'] ?? '');

        if ($search !== '') {
            $query->where(function ($query) use ($search): void {
                $query->where('pegawai.nama', 'like', '%'.$search.'%')
                    ->orWhere('pegawai.jabatan', 'like', '%'.$search.'%');
            });
        }

        if (!empty($filters['periode'])) {
            $query->where('penilaian.periode', $filters['periode']);
        }

        $assessments = $query->orderByDesc('penilaian.periode')
            ->orderBy('pegawai.nama')
            ->get();
        $assessments->transform(function (object $assessment): object {
                $assessment->predikat = $this->predicate((float) $assessment->total);

                return $assessment;
            });

        return view('performance.index', compact('assessments'));
    }

    public function create(): View
    {
        $employees = DB::table('pegawai')->orderBy('nama')->get();
        $categories = DB::table('kategori_kinerja')->orderBy('urutan')->get();

        return view('performance.create', compact('employees', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $categories = DB::table('kategori_kinerja')->orderBy('urutan')->get();
        abort_if($categories->isEmpty(), 500, 'Kategori penilaian belum tersedia.');

        $rules = [
            'pegawai_id' => ['required', 'integer', 'exists:pegawai,id'],
            'periode' => ['required', 'date_format:Y-m'],
            'catatan' => ['nullable', 'string', 'max:2000'],
            'nilai' => ['required', 'array'],
        ];

        foreach ($categories as $category) {
            $rules['nilai.'.$category->id] = ['required', 'integer', 'between:1,4'];
        }

        $data = $request->validate($rules);
        $categoryIds = $categories->pluck('id')->map(fn ($id): string => (string) $id)->all();
        $submittedIds = array_map('strval', array_keys($data['nilai']));
        if (array_diff($categoryIds, $submittedIds) !== [] || array_diff($submittedIds, $categoryIds) !== []) {
            return back()->withErrors(['nilai' => 'Nilai untuk seluruh kategori harus diisi.'])->withInput();
        }

        $weightTotal = (int) $categories->sum('bobot');
        abort_if($weightTotal !== 100, 500, 'Total bobot kategori kinerja harus 100%.');

        $weightedScore = 0;
        foreach ($categories as $category) {
            $weightedScore += (int) $data['nilai'][$category->id] * (int) $category->bobot;
        }
        $total = round($weightedScore / $weightTotal, 2);

        DB::transaction(function () use ($data, $categories, $total): void {
            $assessment = DB::table('penilaian_kinerja')
                ->where('pegawai_id', $data['pegawai_id'])
                ->where('periode', $data['periode'])
                ->first();

            $timestamp = now();
            $attributes = [
                'penilai' => session('username'),
                'catatan' => $data['catatan'] ?? null,
                'total' => $total,
                'updated_at' => $timestamp,
            ];

            if ($assessment === null) {
                $attributes['pegawai_id'] = $data['pegawai_id'];
                $attributes['periode'] = $data['periode'];
                $attributes['created_at'] = $timestamp;
                $assessmentId = DB::table('penilaian_kinerja')->insertGetId($attributes);
            } else {
                DB::table('penilaian_kinerja')->where('id', $assessment->id)->update($attributes);
                $assessmentId = $assessment->id;
            }

            foreach ($categories as $category) {
                $detail = DB::table('detail_penilaian_kinerja')
                    ->where('penilaian_id', $assessmentId)
                    ->where('kategori_id', $category->id)
                    ->first();

                if ($detail === null) {
                    DB::table('detail_penilaian_kinerja')->insert([
                        'penilaian_id' => $assessmentId,
                        'kategori_id' => $category->id,
                        'nilai' => $data['nilai'][$category->id],
                        'updated_at' => $timestamp,
                        'created_at' => $timestamp,
                    ]);
                } else {
                    DB::table('detail_penilaian_kinerja')->where('id', $detail->id)->update([
                        'nilai' => $data['nilai'][$category->id],
                        'updated_at' => $timestamp,
                    ]);
                }
            }
        });

        return redirect()->route('performance.index')
            ->with('success', 'Penilaian kinerja berhasil disimpan.');
    }

    public function show(int $id): View
    {
        $assessment = DB::table('penilaian_kinerja as penilaian')
            ->join('pegawai', 'pegawai.id', '=', 'penilaian.pegawai_id')
            ->where('penilaian.id', $id)
            ->select('penilaian.*', 'pegawai.nama', 'pegawai.jabatan')
            ->first();
        abort_if($assessment === null, 404);
        $details = DB::table('detail_penilaian_kinerja as detail')
            ->join('kategori_kinerja as kategori', 'kategori.id', '=', 'detail.kategori_id')
            ->where('detail.penilaian_id', $id)
            ->select('kategori.nama', 'kategori.bobot', 'kategori.indikator', 'detail.nilai')
            ->orderBy('kategori.urutan')
            ->get()
            ->map(function (object $detail): object {
                $detail->kontribusi = round(((int) $detail->nilai * (int) $detail->bobot) / 100, 2);

                return $detail;
            });
        $assessment->predikat = $this->predicate((float) $assessment->total);

        return view('performance.show', compact('assessment', 'details'));
    }

    public function destroy(int $id): RedirectResponse
    {
        abort_unless(DB::table('penilaian_kinerja')->where('id', $id)->exists(), 404);
        DB::table('penilaian_kinerja')->where('id', $id)->delete();

        return redirect()->route('performance.index')->with('success', 'Riwayat penilaian berhasil dihapus.');
    }

    private function predicate(float $score): string
    {
        return match (true) {
            $score >= 3.5 => 'Sangat Baik',
            $score >= 2.75 => 'Baik',
            $score >= 2 => 'Cukup',
            default => 'Perlu Pembinaan',
        };
    }
}
