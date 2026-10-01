@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Checkout</h1>
            <p class="text-gray-600 mt-2">Periksa pesanan Anda sebelum melakukan pembayaran</p>
        </div>

        <form action="{{ route('ecommerce.checkout.process') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Order Review -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Rincian Pesanan</h2>
                
                <div class="space-y-4">
                    @foreach($items as $item)
                        <div class="flex justify-between items-center py-4 border-b border-gray-200 last:border-b-0">
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $item->service->name }}</h3>
                                <p class="text-gray-600 text-sm">Rp {{ number_format($item->unit_price, 0, ',', '.') }} × {{ $item->quantity }}</p>
                            </div>
                            <p class="text-lg font-semibold text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>

                <!-- Summary -->
                <div class="mt-6 pt-6 border-t border-gray-200 space-y-3">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($cart->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Pajak (10%)</span>
                        <span>Rp {{ number_format($cart->tax, 0, ',', '.') }}</span>
                    </div>
                    @if($cart->discount_amount > 0)
                        <div class="flex justify-between text-green-600">
                            <span>Diskon ({{ strtoupper($cart->discount_code) }})</span>
                            <span>-Rp {{ number_format($cart->discount_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                        <span class="font-bold text-gray-900">Total Pembayaran</span>
                        <span class="text-2xl font-bold text-blue-600">Rp {{ number_format($cart->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Additional Notes -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Catatan Tambahan</h2>
                <textarea name="notes" rows="4" placeholder="Tambahkan catatan atau permintaan khusus..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes') }}</textarea>
            </div>

            <!-- Set Default Payment Method -->
            <input type="hidden" name="payment_method" value="e_wallet">

            <!-- Terms & Actions -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <label class="flex items-start mb-6 cursor-pointer">
                    <input type="checkbox" name="agree_terms" required class="w-4 h-4 text-blue-600 mt-1">
                    <span class="ml-3 text-gray-700">
                        Saya setuju dengan <a href="#" class="text-blue-600 hover:underline">Syarat dan Ketentuan</a> serta 
                        <a href="#" class="text-blue-600 hover:underline">Kebijakan Privasi</a>
                    </span>
                </label>
                @error('agree_terms')
                    <p class="text-red-600 text-sm mb-4">{{ $message }}</p>
                @enderror

                <div class="flex gap-4">
                    <a href="{{ route('ecommerce.cart.index') }}" class="flex-1 text-center px-6 py-3 border border-gray-300 text-gray-900 font-medium rounded-lg hover:bg-gray-50 transition">
                        Kembali ke Keranjang
                    </a>
                    <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                        Lanjut ke Pembayaran
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
