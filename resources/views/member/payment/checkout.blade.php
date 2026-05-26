@extends('layouts.member')

@section('title', 'Payment Checkout')
@section('subtitle', 'Complete your booking payment securely')

@section('content')
<div class="max-w-5xl mx-auto space-y-8">
    <div class="flex items-center space-x-4">
        <a href="{{ route('member.classes') }}" class="text-sm font-bold text-purple-600 hover:text-purple-700 flex items-center transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Classes
        </a>
    </div>

    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-100 text-red-700 rounded-2xl flex items-center shadow-sm">
            <div class="bg-red-500 p-1.5 rounded-lg mr-3">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
            </div>
            <span class="font-semibold text-sm">{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Booking summary -->
        <div class="card p-8 space-y-6 lg:col-span-1 h-fit">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50 pb-2">Class Details</h3>
            
            <div class="space-y-4">
                <div>
                    <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Class</span>
                    <span class="text-lg font-black text-gray-900">{{ $timeslot->service->name }}</span>
                </div>

                <div>
                    <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Trainer</span>
                    <span class="text-sm font-semibold text-gray-700">{{ $timeslot->trainer->name }}</span>
                </div>

                <div>
                    <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Schedule</span>
                    <span class="text-sm font-semibold text-gray-700">
                        {{ \Carbon\Carbon::parse($timeslot->date)->format('l, M d, Y') }}
                    </span>
                    <span class="block text-xs text-purple-600 font-bold mt-0.5">
                        {{ \Carbon\Carbon::parse($timeslot->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($timeslot->end_time)->format('h:i A') }}
                    </span>
                </div>

                <div class="pt-4 border-t border-gray-50 flex justify-between items-center">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Available Spots</span>
                    <livewire:member.live-seat-availability :timeslotId="$timeslot->id" :displayType="'badge'" />
                </div>

                <div class="pt-4 border-t border-gray-50 flex justify-between items-center">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Price</span>
                    <span class="text-2xl font-black text-purple-600">${{ number_format($price, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Payment input form -->
        <div class="card p-8 lg:col-span-2 space-y-6">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50 pb-2">Simulated Credit Card Payment</h3>

            <!-- Simulator Tip Box -->
            <div class="p-4 bg-purple-50 border border-purple-100 rounded-2xl flex items-start">
                <div class="bg-purple-500 p-1.5 rounded-lg mr-3 mt-0.5">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="font-bold text-purple-900 text-xs">Simulated Payment Gateway (IPG)</p>
                    <p class="text-[11px] text-purple-700 mt-1 leading-relaxed">
                        To test a **successful** booking: Enter any valid 16-digit card and any CVV (e.g. 123).<br>
                        To test a **failed/declined** booking: Enter CVV **999**.
                    </p>
                </div>
            </div>

            <form action="{{ route('member.payment.process', $timeslot->id) }}" method="POST">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label for="cardholder_name" class="block text-sm font-bold text-gray-700 uppercase tracking-widest mb-2">Cardholder Name</label>
                        <input type="text" name="cardholder_name" id="cardholder_name" value="{{ old('cardholder_name', auth()->user()->name) }}" class="w-full border-gray-200 rounded-xl focus:ring-purple-500 focus:border-purple-500 shadow-sm p-4" placeholder="e.g. John Doe" required>
                        @error('cardholder_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="card_number" class="block text-sm font-bold text-gray-700 uppercase tracking-widest mb-2">Card Number</label>
                        <input type="text" name="card_number" id="card_number" value="{{ old('card_number') }}" class="w-full border-gray-200 rounded-xl focus:ring-purple-500 focus:border-purple-500 shadow-sm p-4" placeholder="4111222233334444" minlength="16" maxlength="16" required>
                        @error('card_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label for="expiry" class="block text-sm font-bold text-gray-700 uppercase tracking-widest mb-2">Expiry Date (MM/YY)</label>
                            <input type="text" name="expiry" id="expiry" value="{{ old('expiry') }}" class="w-full border-gray-200 rounded-xl focus:ring-purple-500 focus:border-purple-500 shadow-sm p-4" placeholder="12/28" maxlength="5" required>
                            @error('expiry') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="cvv" class="block text-sm font-bold text-gray-700 uppercase tracking-widest mb-2">CVV</label>
                            <input type="text" name="cvv" id="cvv" value="{{ old('cvv') }}" class="w-full border-gray-200 rounded-xl focus:ring-purple-500 focus:border-purple-500 shadow-sm p-4" placeholder="123" minlength="3" maxlength="4" required>
                            @error('cvv') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="pt-4 flex flex-col space-y-4">
                        <livewire:member.live-seat-availability :timeslotId="$timeslot->id" :displayType="'button'" :price="$price" />
                        <a href="{{ route('member.classes') }}" class="block text-center text-sm font-bold text-gray-400 uppercase tracking-widest hover:text-gray-600 transition-colors">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
