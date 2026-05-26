<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-pink-50/50">
    <div class="mb-8">
        {{ $logo }}
    </div>

    <div class="w-full sm:max-w-md px-8 py-8 bg-white shadow-2xl shadow-pink-100/50 overflow-hidden sm:rounded-[2.5rem] border border-white">
        {{ $slot }}
    </div>
</div>
