<?php

namespace App\Http\Controllers;

use App\Models\GajiReport;
use App\Models\Expense;
use Illuminate\Http\Request;

class GajiController extends Controller
{
    public function index()
    {
        $periode = now()->translatedFormat('F Y');
        $report = GajiReport::firstOrCreate(
            ['periode' => $periode],
            ['gaji_pokok' => 0, 'total_pengeluaran' => 0, 'sisa_bersih' => 0]
        );

        $expenses = $report->expenses;
        $totalWajib = $expenses->where('kategori', 'wajib')->sum('nominal');
        $totalLainnya = $expenses->where('kategori', 'lainnya')->sum('nominal');

        return view('kalkulator.index', compact('report', 'totalWajib', 'totalLainnya', 'expenses'));
    }

    public function history()
    {
        $reports = GajiReport::with('expenses')->orderBy('created_at', 'desc')->get();
        return view('kalkulator.history', compact('reports'));
    }

    public function updateSalary(Request $request)
    {
        $report = GajiReport::findOrFail($request->report_id);
        $report->update(['gaji_pokok' => $request->gaji_pokok]);
        $this->recalculate($report);

        return back()->with('success', 'Gaji diperbarui!');
    }

    public function addExpense(Request $request)
    {
        $request->validate([
            'nama_item' => 'required',
            'nominal' => 'required|numeric',
            'kategori' => 'required'
        ]);

        $report = GajiReport::findOrFail($request->report_id);
        $report->expenses()->create($request->only('nama_item', 'nominal', 'kategori'));
        $this->recalculate($report);

        return back()->with('success', 'Pengeluaran ditambahkan!');
    }

    private function recalculate($report)
    {
        $total = $report->expenses()->sum('nominal');
        $sisa = $report->gaji_pokok - $total;
        $report->update([
            'total_pengeluaran' => $total,
            'sisa_bersih' => $sisa
        ]);
    }
}
