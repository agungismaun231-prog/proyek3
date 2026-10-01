@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 to-blue-50 flex items-center justify-center px-4">
    <div class="max-w-md w-full">
        <!-- Success Icon -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-4">
                <svg class="w-10 h-10 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">Pembayaran Berhasil!</h1>
            <p class="text-gray-600 mt-2">Pesanan Anda sedang diproses</p>
        </div>

        <!-- Transaction Details -->
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

                <div class="pb-4 border-b border-gray-200">
                    <p class="text-gray-600 text-sm">Status</p>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <span class="w-2 h-2 bg-green-600 rounded-full mr-2"></span>
                            {{ ucfirst(str_replace('_', ' ', $transaction->status)) }}
                        </span>
                    </div>
                </div>

                <div>
                    <p class="text-gray-600 text-sm">Metode Pembayaran</p>
                    <p class="text-gray-900 font-medium capitalize mt-1">{{ str_replace('_', ' ', $transaction->payment_method) }}</p>
                </div>
            </div>
        </div>

        <!-- Items Summary -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h3 class="font-bold text-gray-900 mb-4">Pesanan Anda</h3>
            <div class="space-y-3 mb-4 pb-4 border-b border-gray-200">
                @foreach($items as $item)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">{{ $item->service->name }} × {{ $item->quantity }}</span>
                        <span class="font-semibold text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Subtotal</span>
                    <span class="text-gray-900">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Pajak</span>
                    <span class="text-gray-900">Rp {{ number_format($transaction->tax, 0, ',', '.') }}</span>
                </div>
                @if($transaction->discount_amount > 0)
                    <div class="flex justify-between text-sm text-green-600">
                        <span>Diskon</span>
                        <span>-Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="space-y-3">
            <a href="{{ route('ecommerce.transactions.show', $transaction) }}" class="block w-full text-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                Lihat Detail Transaksi
            </a>
            <a href="{{ route('customer.dashboard') }}" class="block w-full text-center px-6 py-3 border border-gray-300 text-gray-900 font-medium rounded-lg hover:bg-gray-50 transition">
                Kembali ke Dashboard
            </a>
        </div>

        <!-- Info Message -->
        <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-sm text-blue-800">
                📧 Email konfirmasi telah dikirim ke <strong>{{ Auth::user()->email }}</strong>. Jika Anda tidak menerimanya, cek folder spam.
            </p>
        </div>
    </div>
</div>
@endsection
