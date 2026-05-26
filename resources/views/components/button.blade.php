<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-8 py-3 bg-gradient-to-r from-pink-500 to-violet-500 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2 disabled:opacity-50 transition ease-in-out duration-150 shadow-lg shadow-pink-100']) }}>
    {{ $slot }}
</button>
