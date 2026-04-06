<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalkulator Gaji - WaveProject.id</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#000000">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .brutal-border { border: 4px solid #000; }
        .brutal-shadow { box-shadow: 8px 8px 0px 0px #000; }
        .brutal-shadow-sm { box-shadow: 4px 4px 0px 0px #000; }
        .hand-drawn { font-family: 'Space Grotesk', sans-serif; }
    </style>
</head>
<body class="bg-[#F4EBD0] min-h-screen flex items-center justify-center p-4">

    <div class="bg-white w-full max-w-lg p-8 brutal-border brutal-shadow rounded-sm relative overflow-hidden">
        
        <!-- Header -->
        <header class="text-center mb-10">
            <h1 class="text-4xl font-black uppercase tracking-tighter hand-drawn leading-none">Kalkulator Gaji</h1>
            <p class="text-xs font-bold mt-2 bg-yellow-300 inline-block px-2 brutal-border">PERIODE: {{ strtoupper($report->periode) }}</p>
        </header>

        <!-- Main Buttons -->
        <div class="grid grid-cols-2 gap-4 mb-10">
            <button onclick="toggleModal('modalExpense')" class="bg-blue-400 p-3 brutal-border brutal-shadow-sm hover:translate-x-1 hover:translate-y-1 hover:shadow-none transition-all font-bold text-xs uppercase text-white">
                Input Pengeluaran<br>Bulanan
            </button>
            <a href="{{ route('kalkulator.history') }}" class="bg-purple-400 p-3 brutal-border brutal-shadow-sm hover:translate-x-1 hover:translate-y-1 hover:shadow-none transition-all font-bold text-xs uppercase text-white text-center block leading-tight">
                History Laporan<br>Bulanan
            </a>
        </div>

        <hr class="border-2 border-black mb-8 border-dashed">

        <!-- Gaji Input -->
        <div class="mb-8">
            <label class="block text-xs font-black uppercase mb-2 tracking-widest">Input Gaji (Pokok)</label>
            <form action="{{ route('salary.update') }}" method="POST" class="relative">
                @csrf
                <input type="hidden" name="report_id" value="{{ $report->id }}">
                <input type="number" name="gaji_pokok" value="{{ (int)$report->gaji_pokok }}" 
                    class="w-full brutal-border p-5 text-3xl font-black text-center focus:outline-none focus:bg-yellow-50 transition-colors"
                    onchange="this.form.submit()" placeholder="Rp 0">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xl font-bold opacity-30">Rp</span>
            </form>
        </div>

        <!-- Details Table -->
        <div class="brutal-border p-6 mb-8 bg-gray-50 bg-[radial-gradient(#000_1px,transparent_1px)] [background-size:20px_20px] [background-position:10px_10px]">
            <h2 class="text-[10px] font-black text-center border-b-2 border-black pb-2 mb-6 uppercase tracking-widest bg-white">
                Tabel Perhitungan Gaji VS Pengeluaran
            </h2>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center bg-white p-2 brutal-border border-2">
                    <span class="text-xs font-bold uppercase">Total Wajib:</span>
                    <span class="text-lg font-black text-red-500">Rp {{ number_format($totalWajib, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center bg-white p-2 brutal-border border-2">
                    <span class="text-xs font-bold uppercase">Total Lainnya:</span>
                    <span class="text-lg font-black text-orange-500">Rp {{ number_format($totalLainnya, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Sisa Bersih -->
        <div class="brutal-border p-6 text-center bg-black text-white relative">
            <span class="block text-[10px] font-bold uppercase tracking-[0.3em] mb-2 opacity-70">Sisa Bersih</span>
            <span class="text-4xl font-black text-green-400 hand-drawn min-w-full">
                Rp {{ number_format($report->sisa_bersih, 0, ',', '.') }}
            </span>
            @if($report->sisa_bersih < 0)
                <div class="absolute -top-4 -right-4 bg-red-600 text-white text-[10px] brutal-border p-1 animate-bounce uppercase font-bold">Defisit!</div>
            @endif
        </div>

        <!-- Footer -->
        <div class="mt-12 text-center text-[8px] font-bold uppercase tracking-widest text-gray-400">
            <p>Powered by waveproject.id &middot; 2026</p>
        </div>
    </div>

    <!-- Modal Expense -->
    <div id="modalExpense" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white brutal-border brutal-shadow w-full max-w-sm p-8">
            <h3 class="text-xl font-black uppercase mb-6 text-center">Tambah Pengeluaran</h3>
            <form action="{{ route('expense.add') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="report_id" value="{{ $report->id }}">
                <div>
                    <label class="text-[10px] font-black uppercase">Nama Item</label>
                    <input type="text" name="nama_item" class="w-full brutal-border border-2 p-2 focus:bg-blue-50 outline-none" required>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="text-[10px] font-black uppercase">Nominal</label>
                        <input type="number" name="nominal" class="w-full brutal-border border-2 p-2 focus:bg-blue-50 outline-none" required>
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase">Kategori</label>
                        <select name="kategori" class="w-full brutal-border border-2 p-2 focus:bg-blue-50 outline-none font-bold">
                            <option value="wajib">WAJIB</option>
                            <option value="lainnya">LAINNYA</option>
                        </select>
                    </div>
                </div>
                <div class="flex gap-2 pt-4">
                    <button type="button" onclick="toggleModal('modalExpense')" class="flex-1 brutal-border border-2 p-2 font-bold uppercase text-xs">Batal</button>
                    <button type="submit" class="flex-1 bg-green-400 brutal-border border-2 p-2 font-bold uppercase text-xs">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleModal(id) {
            const modal = document.getElementById(id);
            modal.classList.toggle('hidden');
        }

        // PWA Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('SW Registered!', reg))
                    .catch(err => console.log('SW Registration Failed', err));
            });
        }
    </script>
</body>
</html>
