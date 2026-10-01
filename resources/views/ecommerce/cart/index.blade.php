@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Keranjang Belanja</h1>
            <p class="text-gray-600 mt-2">Kelola items dan lanjutkan ke checkout</p>
        </div>

        <div class="grid grid-cols-3 gap-8">
            <!-- Cart Items (Left) -->
            <div class="col-span-2">
                @forelse($items as $item)
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-4 hover:shadow-md transition">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $item->service->name }}</h3>
                                <p class="text-gray-600 text-sm mt-1">{{ $item->service->description }}</p>
                                @if($item->notes)
                                    <p class="text-blue-600 text-sm mt-2"><strong>Catatan:</strong> {{ $item->notes }}</p>
                                @endif
                            </div>
                            <button onclick="removeItem({{ $item->id }})" class="text-red-600 hover:text-red-800 transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>

                        <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-200">
                            <!-- Quantity -->
                            <div class="flex items-center gap-2">
                                <span class="text-gray-600">Qty:</span>
                                <div class="flex items-center border border-gray-300 rounded-lg">
                                    <button onclick="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})" class="px-3 py-1 text-gray-600 hover:bg-gray-100">-</button>
                                    <span class="px-4 py-1 font-semibold">{{ $item->quantity }}</span>
                                    <button onclick="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})" class="px-3 py-1 text-gray-600 hover:bg-gray-100">+</button>
                                </div>
                            </div>

                            <!-- Price -->
                            <div class="text-right">
                                <p class="text-gray-600 text-sm">Rp {{ number_format($item->unit_price, 0, ',', '.') }}/item</p>
                                <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Keranjang kosong</h3>
                        <p class="mt-2 text-gray-600">Mulai belanja dengan menambahkan service</p>
                        <a href="{{ route('customer.services') }}" class="mt-6 inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            Lihat Service
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Cart Summary (Right) -->
            <div class="col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Ringkasan Belanja</h2>

                    <!-- Promo Code -->
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <label class="block text-sm font-medium text-gray-900 mb-2">Kode Promo</label>
                        <div class="flex gap-2">
                            <input type="text" id="promo-code" placeholder="Masukkan kode" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <button onclick="applyPromo()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                Terapkan
                            </button>
                        </div>
                    </div>

                    <!-- Breakdown -->
                    <div class="space-y-3 mb-6 pb-6 border-b border-gray-200">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($cart->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Pajak (10%)</span>
                            <span>Rp {{ number_format($cart->tax, 0, ',', '.') }}</span>
                        </div>
                        @if($cart->discount_amount > 0)
                            <div class="flex justify-between text-green-600 font-medium">
                                <span>Diskon {{ strtoupper($cart->discount_code) }}</span>
                                <span>-Rp {{ number_format($cart->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Total -->
                    <div class="flex justify-between items-center mb-6">
                        <span class="text-lg font-bold text-gray-900">Total</span>
                        <span class="text-2xl font-bold text-blue-600">Rp {{ number_format($cart->total, 0, ',', '.') }}</span>
                    </div>

                    <!-- Buttons -->
                    <a href="{{ route('ecommerce.checkout.show') }}" class="w-full block text-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                        Lanjutkan ke Checkout
                    </a>
                    <a href="{{ route('customer.services') }}" class="w-full block text-center px-6 py-3 mt-3 border border-gray-300 text-gray-900 font-medium rounded-lg hover:bg-gray-50 transition">
                        Lanjut Belanja
                    </a>
                    <button onclick="clearCart()" class="w-full px-6 py-2 mt-3 text-red-600 font-medium hover:text-red-700 transition">
                        Hapus Semua
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateQuantity(itemId, quantity) {
    if (quantity < 1) return;
    
    fetch(`/ecommerce/cart/update/${itemId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ quantity })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) location.reload();
        else alert(data.message || 'Error');
    });
}

function removeItem(itemId) {
    if (!confirm('Hapus item ini dari keranjang?')) return;
    
    fetch(`/ecommerce/cart/remove/${itemId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) location.reload();
        else alert(data.message || 'Error');
    });
}

function applyPromo() {
    const code = document.getElementById('promo-code').value.trim();
    if (!code) {
        alert('Masukkan kode promo');
        return;
    }
    
    fetch('/ecommerce/cart/apply-promo', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ promo_code: code })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Kode promo berhasil diterapkan!');
            location.reload();
        } else {
            alert(data.message || 'Error');
        }
    });
}

function clearCart() {
    if (!confirm('Hapus semua items dari keranjang?')) return;
    
    fetch('/ecommerce/cart/clear', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(r => r.json())
    .then(data => {
        location.reload();
    });
}
</script>
@endsection
