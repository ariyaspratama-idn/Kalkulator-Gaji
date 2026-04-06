<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Laporan - Kalkulator Gaji</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .brutal-border { border: 4px solid #000; }
        .brutal-shadow { box-shadow: 8px 8px 0px 0px #000; }
        .brutal-shadow-sm { box-shadow: 4px 4px 0px 0px #000; }
        .hand-drawn { font-family: 'Space Grotesk', sans-serif; }
    </style>
</head>
<body class="bg-[#F4EBD0] min-h-screen flex items-center justify-center p-4">

    <div class="bg-white w-full max-w-2xl p-8 brutal-border brutal-shadow rounded-sm relative">
        
        <!-- Header -->
        <header class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-black uppercase tracking-tighter hand-drawn leading-none">Riwayat Laporan</h1>
                <p class="text-[10px] font-bold mt-1 uppercase opacity-50">Pengelolaan Keuangan Bulanan</p>
            </div>
            <a href="{{ route('kalkulator.index') }}" class="bg-yellow-300 p-2 brutal-border brutal-shadow-sm hover:translate-x-1 hover:translate-y-1 hover:shadow-none transition-all font-bold text-xs uppercase">
                Kembali
            </a>
        </header>

        <!-- History List -->
        <div class="space-y-6">
            @forelse($reports as $report)
            <div class="brutal-border p-5 bg-white hover:bg-gray-50 transition-colors">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-xl font-black uppercase hand-drawn">{{ $report->periode }}</h3>
                        <p class="text-[10px] font-bold text-gray-500 uppercase">Dibuat pada: {{ $report->created_at->format('d/m/Y') }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-black bg-black text-white px-2 py-1 uppercase brutal-border border-1">Sisa Bersih</span>
                        <p class="text-xl font-black {{ $report->sisa_bersih < 0 ? 'text-red-500' : 'text-green-500' }}">
                            Rp {{ number_format($report->sisa_bersih, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2 border-t-2 border-black pt-4 border-dashed">
                    <div class="text-center">
                        <span class="block text-[8px] font-black uppercase opacity-50">Gaji Pokok</span>
                        <span class="text-xs font-bold">Rp {{ number_format($report->gaji_pokok, 0, ',', '.') }}</span>
                    </div>
                    <div class="text-center border-x-2 border-black border-dashed">
                        <span class="block text-[8px] font-black uppercase opacity-50">Total Pengeluaran</span>
                        <span class="text-xs font-bold">Rp {{ number_format($report->total_pengeluaran, 0, ',', '.') }}</span>
                    </div>
                    <div class="text-center">
                        <span class="block text-[8px] font-black uppercase opacity-50">Items</span>
                        <span class="text-xs font-bold">{{ $report->expenses->count() }} Transaksi</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center p-10 brutal-border border-dashed opacity-50">
                <p class="font-bold uppercase text-xs">Belum ada riwayat laporan.</p>
            </div>
            @endforelse
        </div>

        <!-- Footer -->
        <div class="mt-12 text-center text-[8px] font-bold uppercase tracking-widest text-gray-400">
            <p>Powered by waveproject.id &middot; 2026</p>
        </div>
    </div>

</body>
</html>
