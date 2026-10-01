@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-yellow-50 to-orange-50 flex items-center justify-center px-4">
    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-yellow-100 rounded-full mb-4">
                <svg class="w-10 h-10 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">Pembayaran Tertunda</h1>
            <p class="text-gray-600 mt-2">Kami sedang memproses pembayaran Anda</p>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="space-y-4">
                <div class="pb-4 border-b border-gray-200">
                    <p class="text-gray-600 text-sm">Nomor Transaksi</p>
                    <p class="text-lg font-mono font-bold text-gray-900">{{ $transaction->transaction_number }}</p>
                </div>
                
                <div class="pb-4 border-b border-gray-200">
                    <p class="text-gray-600 text-sm">Total Pembayaran</p>
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($transaction->total, 0, ',', '.') }}</p>
                </div>

                <div>
                    <p class="text-gray-600 text-sm">Status</p>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 mt-1">
                        <span class="w-2 h-2 bg-yellow-600 rounded-full mr-2 animate-pulse"></span>
                        Menunggu Konfirmasi
                    </span>
                </div>
            </div>
        </div>

        <div class="space-y-3">
            <button onclick="checkStatus()" class="block w-full text-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                Periksa Status Sekarang
            </button>
            <a href="{{ route('ecommerce.transactions.show', $transaction) }}" class="block w-full text-center px-6 py-3 border border-gray-300 text-gray-900 font-medium rounded-lg hover:bg-gray-50 transition">
                Kembali ke Transaksi
            </a>
        </div>

        <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-sm text-blue-800">
                ⏱️ Pembayaran bank transfer bisa memakan waktu hingga 1-2 jam untuk dikonfirmasi.
            </p>
        </div>
    </div>
</div>

<script>
function checkStatus() {
    fetch('{{ route("ecommerce.transactions.check-status", $transaction) }}')
        .then(r => r.json())
        .then(data => {
            if (data.status === 'paid' || data.status === 'completed') {
                window.location.href = '{{ route("ecommerce.payment.finish") }}?order_id={{ $transaction->transaction_number }}';
            } else {
                alert('Status masih tertunda. Silakan coba beberapa saat lagi.');
            }
        });
}
</script>
@endsection
