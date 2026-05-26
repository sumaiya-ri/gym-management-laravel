<div wire:poll.5s>
    @if($displayType === 'badge')
        <span class="text-xs font-bold {{ $isFull ? 'text-red-500' : 'text-emerald-500' }}">
            {{ $isFull ? 'Fully Booked' : $spots . ' spots left' }}
        </span>
    @elseif($displayType === 'button')
        @if($isFull)
            <button type="button" disabled class="w-full py-4 bg-gray-50 text-gray-400 font-black text-[10px] uppercase tracking-[0.2em] rounded-2xl cursor-not-allowed text-center block border border-gray-100">
                Fully Booked
            </button>
        @else
            @if(request()->routeIs('member.payment.checkout'))
                <!-- Submit button for the payment checkout form -->
                <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-bold py-4 rounded-xl hover:from-purple-700 hover:to-indigo-700 transition-colors shadow-lg shadow-purple-200 flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    Pay & Confirm Booking
                </button>
            @else
                <!-- Class booking link on search card -->
                <a href="{{ route('member.payment.checkout', $timeslotId) }}" class="w-full py-4 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-black text-[10px] uppercase tracking-[0.2em] rounded-2xl shadow-xl shadow-purple-100 hover:scale-[1.02] active:scale-95 transition-all text-center block">
                    Book Class
                </a>
            @endif
        @endif
    @endif
</div>
