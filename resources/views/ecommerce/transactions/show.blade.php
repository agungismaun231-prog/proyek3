@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Flash Messages & Notifications -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Payment Success Card (when status is paid) -->
        @if($transaction->status === 'paid' || $transaction->status === 'completed')
            <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-600 rounded">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800">Pembayaran Berhasil!</h3>
                        <p class="mt-2 text-sm text-green-700">
                            Terima kasih! Pembayaran Anda telah diterima dan diproses. 
                            Anda akan menerima email konfirmasi dalam beberapa saat.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Header -->
        <div class="mb-8 flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Detail Transaksi</h1>
                <p class="text-gray-600 mt-1">{{ $transaction->transaction_number }}</p>
            </div>
            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold
                @if($transaction->status === 'paid' || $transaction->status === 'completed') bg-green-100 text-green-800
                @elseif($transaction->status === 'pending') bg-yellow-100 text-yellow-800
                @elseif($transaction->status === 'failed' || $transaction->status === 'cancelled') bg-red-100 text-red-800
                @else bg-blue-100 text-blue-800
                @endif
            ">
                {{ ucfirst(str_replace('_', ' ', $transaction->status)) }}
            </span>
        </div>

        <div class="grid grid-cols-3 gap-6 mb-8">
            <!-- Transaction Info -->
            <div class="col-span-2 space-y-6">
                <!-- Items -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Item Pesanan</h2>
                    <div class="space-y-4">
                        @foreach($items as $item)
                            <div class="flex justify-between items-start py-4 border-b border-gray-200 last:border-b-0">
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ $item->service->name }}</h3>
                                    <p class="text-gray-600 text-sm mt-1">{{ $item->description }}</p>
                                    <p class="text-gray-600 text-sm">Rp {{ number_format($item->unit_price, 0, ',', '.') }} × {{ $item->quantity }}</p>
                                </div>
                                <p class="font-bold text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Payment Summary -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Ringkasan Pembayaran</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-semibold text-gray-900">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Pajak (10%)</span>
                            <span class="font-semibold text-gray-900">Rp {{ number_format($transaction->tax, 0, ',', '.') }}</span>
                        </div>
                        @if($transaction->discount_amount > 0)
                            <div class="flex justify-between text-green-600">
                                <span>Diskon ({{ strtoupper($transaction->promo_code) }})</span>
                                <span>-Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between py-3 border-t border-gray-200 font-bold">
                            <span>Total</span>
                            <span class="text-2xl text-blue-600">Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Payment History -->
                @if($paymentHistory->count() > 0)
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Riwayat Pembayaran</h2>
                        <div class="space-y-3">
                            @foreach($paymentHistory as $payment)
                                <div class="flex justify-between items-center py-3 border-b border-gray-200 last:border-b-0">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $payment->payment_method }}</p>
                                        <p class="text-gray-600 text-sm">{{ $payment->created_at->format('d M Y H:i') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-gray-900">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                            {{ $payment->status === 'success' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}
                                        ">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-span-1">
                <!-- Payment Status Card -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6 sticky top-8">
                    <h3 class="font-bold text-gray-900 mb-4">Status Pembayaran</h3>
                    
                    <div class="mb-6">
                        <div class="flex justify-between mb-2">
                            <span class="text-sm text-gray-600">Sudah Dibayar</span>
                            <span class="font-bold text-gray-900">Rp {{ number_format($transaction->amount_paid, 0, ',', '.') }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full transition-all" style="width: {{ ($transaction->amount_paid / $transaction->total) * 100 }}%"></div>
                        </div>
                        <div class="flex justify-between mt-2">
                            <span class="text-sm text-gray-600">Sisa</span>
                            <span class="font-bold text-gray-900">Rp {{ number_format($transaction->getRemainingAmount(), 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- E-Wallet Payment Methods -->
                    @if($transaction->status === 'pending' || $transaction->status === 'awaiting_payment')
                        <div class="mb-6">
                            <p class="text-sm font-semibold text-gray-900 mb-3">Pilih E-Wallet:</p>
                            <div class="grid grid-cols-2 gap-2">
                                <button onclick="payWithEWallet()" class="flex flex-col items-center justify-center p-3 border-2 border-gray-300 rounded-lg hover:border-blue-600 hover:bg-blue-50 transition group">
                                    <span class="text-2xl mb-1">💳</span>
                                    <span class="text-xs font-medium text-gray-700 group-hover:text-blue-600 text-center">E-Wallet</span>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-3 text-center">Termasuk: GCash, OVO, Dana, LINKAJA</p>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="space-y-2">
                        @if(!$transaction->isPaidFull() && $transaction->status !== 'cancelled' && $transaction->status !== 'failed')
                            <button onclick="cancelTransaction()" class="w-full px-4 py-2 border border-red-600 text-red-600 font-medium rounded-lg hover:bg-red-50 transition">
                                Batalkan
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Transaction Info -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="font-bold text-gray-900 mb-4">Informasi</h3>
                    
                    <div class="space-y-4 text-sm">
                        <div>
                            <p class="text-gray-600">Metode Pembayaran</p>
                            <p class="font-semibold text-gray-900 capitalize">{{ str_replace('_', ' ', $transaction->payment_method) }}</p>
                        </div>
                        
                        <div>
                            <p class="text-gray-600">Tanggal Pesanan</p>
                            <p class="font-semibold text-gray-900">{{ $transaction->created_at->format('d M Y H:i') }}</p>
                        </div>
                        
                        @if($transaction->payment_date)
                            <div>
                                <p class="text-gray-600">Tanggal Pembayaran</p>
                                <p class="font-semibold text-gray-900">{{ $transaction->payment_date->format('d M Y H:i') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function payWithEWallet() {
    // Show loading indicator
    const button = event.target.closest('button');
    button.disabled = true;
    button.innerHTML = '<span class="text-xs">Memproses...</span>';

    fetch('{{ route("ecommerce.transactions.pay", $transaction) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.payment_url;
        } else {
            alert(data.message || 'Error');
            button.disabled = false;
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
        button.disabled = false;
        location.reload();
    });
}

function cancelTransaction() {
    const reason = prompt('Alasan pembatalan:');
    if (!reason) return;
    
    fetch('{{ route("ecommerce.transactions.cancel", $transaction) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ reason })
    })
    .then(r => r.json())
    .then(data => {
        location.reload();
    });
}
</script>
@endsection
