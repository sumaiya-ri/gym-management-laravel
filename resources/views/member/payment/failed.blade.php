@extends('layouts.member')

@section('title', 'Payment Failed')
@section('subtitle', 'Your transaction was declined by the gateway')

@section('content')
<div class="max-w-2xl mx-auto space-y-8">
    <div class="card p-8 text-center space-y-6 flex flex-col items-center">
        <!-- Failure Icon -->
        <div class="w-20 h-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center border-4 border-red-100 animate-pulse">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
        </div>

        <div class="space-y-2">
            <h2 class="text-2xl font-black text-gray-900 tracking-tight">Payment Declined</h2>
            <p class="text-gray-500 text-sm font-medium">Your bank or card issuer declined the transaction. No funds were debited, and your class spot has not been secured.</p>
        </div>

        <div class="w-full bg-red-50/50 border border-red-100 rounded-2xl p-6 text-left space-y-3">
            <h4 class="text-xs font-bold text-red-800 uppercase tracking-widest">Troubleshooting tips:</h4>
            <ul class="list-disc list-inside text-xs text-red-700 space-y-1">
                <li>Verify your cardholder name, card number, expiry, and CVV are correct.</li>
                <li>Ensure the card has sufficient balance or credit limit.</li>
                <li>Try a different card or contact your card issuer.</li>
                <li>If simulating a successful transaction, make sure CVV is **not** set to **999**.</li>
            </ul>
        </div>

        <div class="pt-4 flex flex-col sm:flex-row gap-4 w-full justify-center">
            <a href="{{ route('member.payment.checkout', $timeslot->id) }}" class="btn-purple text-center bg-gradient-to-r from-red-600 to-red-700 shadow-red-200">
                Try Again
            </a>
            <a href="{{ route('member.classes') }}" class="inline-flex justify-center items-center border-2 border-gray-200 hover:border-red-600 hover:text-red-600 transition-colors text-gray-600 font-bold px-6 py-3.5 rounded-xl text-sm">
                Browse Classes
            </a>
        </div>
    </div>
</div>
@endsection
