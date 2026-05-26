@extends('layouts.member')

@section('title', 'Booking Confirmed')
@section('subtitle', 'Your class slot has been secured successfully')

@section('content')
<div class="max-w-2xl mx-auto space-y-8">
    <div class="card p-8 text-center space-y-6 flex flex-col items-center">
        <!-- Success Icon -->
        <div class="w-20 h-20 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center border-4 border-emerald-100 animate-bounce">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
        </div>

        <div class="space-y-2">
            <h2 class="text-2xl font-black text-gray-900 tracking-tight">Payment Successful!</h2>
            <p class="text-gray-500 text-sm font-medium">Your slot is locked in. A confirmation email has been dispatched via the queue.</p>
        </div>

        <!-- Details list -->
        <div class="w-full bg-purple-50/50 border border-purple-100 rounded-2xl p-6 text-left space-y-4">
            <div class="flex justify-between items-center text-xs border-b border-purple-100/50 pb-3">
                <span class="font-bold text-gray-400 uppercase tracking-widest">Transaction ID</span>
                <span class="font-mono font-bold text-purple-700 bg-white px-2 py-0.5 rounded border border-purple-200">{{ $booking->payment_transaction_id }}</span>
            </div>

            <div class="flex justify-between items-center text-xs border-b border-purple-100/50 pb-3">
                <span class="font-bold text-gray-400 uppercase tracking-widest">Amount Paid</span>
                <span class="font-bold text-gray-900">${{ number_format($booking->payment_amount, 2) }}</span>
            </div>

            <div class="flex justify-between items-center text-xs border-b border-purple-100/50 pb-3">
                <span class="font-bold text-gray-400 uppercase tracking-widest">Class</span>
                <span class="font-bold text-gray-900">{{ $booking->timeslot->service->name }}</span>
            </div>

            <div class="flex justify-between items-center text-xs border-b border-purple-100/50 pb-3">
                <span class="font-bold text-gray-400 uppercase tracking-widest">Trainer</span>
                <span class="font-bold text-gray-900">{{ $booking->timeslot->trainer->name }}</span>
            </div>

            <div class="flex justify-between items-center text-xs border-b border-purple-100/50 pb-3">
                <span class="font-bold text-gray-400 uppercase tracking-widest">Date</span>
                <span class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($booking->timeslot->date)->format('F d, Y') }}</span>
            </div>

            <div class="flex justify-between items-center text-xs pb-1">
                <span class="font-bold text-gray-400 uppercase tracking-widest">Time</span>
                <span class="font-bold text-purple-600">
                    {{ \Carbon\Carbon::parse($booking->timeslot->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($booking->timeslot->end_time)->format('h:i A') }}
                </span>
            </div>
        </div>

        <div class="pt-4 flex flex-col sm:flex-row gap-4 w-full justify-center">
            <a href="{{ route('member.bookings') }}" class="btn-purple text-center">
                View My Bookings
            </a>
            <a href="{{ route('member.classes') }}" class="inline-flex justify-center items-center border-2 border-gray-200 hover:border-purple-600 hover:text-purple-600 transition-colors text-gray-600 font-bold px-6 py-3.5 rounded-xl text-sm">
                Browse More Classes
            </a>
        </div>
    </div>
</div>
@endsection
