@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-red-50 to-yellow-50 flex items-center justify-center px-4">
    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-red-100 rounded-full mb-4">
                <svg class="w-10 h-10 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">Pembayaran Gagal</h1>
            <p class="text-gray-600 mt-2">Pesanan Anda masih tertunda</p>
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
                    <p class="text-gray-600 text-sm">Alasan</p>
                    <p class="text-gray-900 font-medium mt-1">Pembayaran ditolak atau dibatalkan. Silakan coba lagi.</p>
                </div>
            </div>
        </div>

        <div class="space-y-3">
            <a href="{{ route('ecommerce.transactions.show', $transaction) }}" class="block w-full text-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                Coba Pembayaran Lagi
            </a>
            <a href="{{ route('customer.dashboard') }}" class="block w-full text-center px-6 py-3 border border-gray-300 text-gray-900 font-medium rounded-lg hover:bg-gray-50 transition">
                Kembali ke Dashboard
            </a>
        </div>

        <div class="mt-8 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <p class="text-sm text-yellow-800">
                💡 Jika masalah terus berlanjut, silakan hubungi customer support kami.
            </p>
        </div>
    </div>
</div>
@endsection
