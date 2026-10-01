@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 to-blue-50 flex items-center justify-center px-4">
    <div class="max-w-md w-full text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-100 rounded-full mb-6">
            <svg class="w-10 h-10 text-blue-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Pembayaran Sedang Diproses</h1>
        <p class="text-gray-600 mt-2">Jangan tutup halaman ini sampai pembayaran selesai</p>
        
        <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
            <p class="text-gray-600 text-sm mb-2">Nomor Transaksi</p>
            <p class="font-mono font-bold text-gray-900 mb-6">{{ $transaction->transaction_number }}</p>
            
            <p class="text-gray-600 text-sm mb-2">Total Pembayaran</p>
            <p class="text-3xl font-bold text-blue-600">Rp {{ number_format($transaction->total, 0, ',', '.') }}</p>
        </div>

        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-sm text-blue-800">
                Sistem akan otomatis memeriksa status pembayaran setiap 5 detik.
            </p>
        </div>

        <a href="{{ route('ecommerce.transactions.show', $transaction) }}" class="mt-6 inline-block text-blue-600 hover:text-blue-800 font-medium">
            ← Kembali ke Detail Transaksi
        </a>
    </div>
</div>

<script>
// Auto refresh setiap 5 detik
setInterval(function() {
    fetch('{{ route("ecommerce.transactions.check-status", $transaction) }}')
        .then(r => r.json())
        .then(data => {
            if (data.status === 'paid' || data.status === 'completed') {
                window.location.href = '{{ route("ecommerce.payment.finish") }}?order_id={{ $transaction->transaction_number }}';
            }
        });
}, 5000);
</script>
@endsection
