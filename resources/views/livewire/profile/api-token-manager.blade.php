<div>
    <!-- Create API Token -->
    <x-form-section submit="createToken">
        <x-slot name="title">
            <span class="text-slate-900 font-extrabold">{{ __('Create API Token') }}</span>
        </x-slot>

        <x-slot name="description">
            <span class="text-slate-500 font-medium">{{ __('API tokens allow third-party services to authenticate with our application on your behalf. Create a token with role-restricted scopes and lifecycle expiration.') }}</span>
        </x-slot>

        <x-slot name="form">
            <!-- Token Name -->
            <div class="col-span-6 sm:col-span-4">
                <x-label for="name" value="{{ __('Token Name') }}" class="text-slate-700 font-bold" />
                <x-input id="name" type="text" class="mt-1 block w-full border-slate-200 focus:border-purple-500 focus:ring focus:ring-purple-200/50 rounded-xl" wire:model="name" autofocus />
                <x-input-error for="name" class="mt-2" />
            </div>

            <!-- Token Expiration -->
            <div class="col-span-6 sm:col-span-4 mt-4">
                <x-label for="expiresInDays" value="{{ __('Token Expiration') }}" class="text-slate-700 font-bold" />
                <select id="expiresInDays" class="mt-1 block w-full border-slate-200 focus:border-purple-500 focus:ring focus:ring-purple-200/50 rounded-xl shadow-sm text-sm" wire:model="expiresInDays">
                    <option value="7">7 Days</option>
                    <option value="30">30 Days (Recommended)</option>
                    <option value="90">90 Days</option>
                    <option value="never">Never (No Expiration)</option>
                </select>
                <x-input-error for="expiresInDays" class="mt-2" />
            </div>

            <!-- Token Abilities -->
            @if (count($availableAbilities) > 0)
                <div class="col-span-6 mt-4">
                    <x-label value="{{ __('Token Permissions') }}" class="text-slate-700 font-bold mb-2" />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 bg-slate-50 p-4 rounded-xl border border-slate-100">
                        @foreach ($availableAbilities as $ability)
                            <label class="flex items-center space-x-3 cursor-pointer select-none">
                                <x-checkbox wire:model="selectedAbilities.{{ $ability }}" class="text-purple-600 focus:ring-purple-200/50 border-slate-300 rounded" />
                                <div class="text-sm font-semibold text-slate-700">
                                    <code class="px-1.5 py-0.5 bg-slate-200/60 rounded text-purple-700 text-xs font-mono">{{ $ability }}</code>
                                    <span class="text-xs text-slate-400 font-medium ml-1">
                                        @switch($ability)
                                            @case('read:analytics') (View admin performance reports) @break
                                            @case('manage:gyms') (Configure gym settings and isolation) @break
                                            @case('manage:subscriptions') (Edit subscriptions) @break
                                            @case('manage:platform') (Global SaaS controls) @break
                                            @case('manage:bookings') (Manage and cancel classes) @break
                                            @case('manage:trainers') (Add or update instructors) @break
                                            @case('manage:services') (Edit fitness programs) @break
                                            @case('manage:members') (View client accounts) @break
                                            @case('manage:workouts') (Create class schedules) @break
                                            @case('view:classes') (Browse scheduled workouts) @break
                                            @case('create:bookings') (Register for new timeslots) @break
                                            @case('view:own-bookings') (List booked classes) @break
                                            @default @break
                                        @endswitch
                                    </span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif
        </x-slot>

        <x-slot name="actions">
            <x-action-message class="me-3 text-emerald-600 font-semibold" on="created">
                {{ __('Created.') }}
            </x-action-message>

            <button type="submit" class="btn-purple font-semibold shadow-md">
                {{ __('Create Token') }}
            </button>
        </x-slot>
    </x-form-section>

    <!-- Token List -->
    @if ($tokens->isNotEmpty())
        <x-section-border />

        <div class="mt-10 sm:mt-0">
            <x-action-section>
                <x-slot name="title">
                    <span class="text-slate-900 font-extrabold">{{ __('Active API Tokens') }}</span>
                </x-slot>

                <x-slot name="description">
                    <span class="text-slate-500 font-medium">{{ __('You may revoke any of your active tokens if they are no longer needed. Any applications using a revoked token will be locked out immediately.') }}</span>
                </x-slot>

                <x-slot name="content">
                    @if (session()->has('status'))
                        <div class="mb-4 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-xl text-sm font-semibold flex items-center">
                            <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2 animate-ping"></span>
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto rounded-2xl border border-slate-100 bg-white">
                        <table class="w-full text-left text-xs font-semibold text-slate-600 divide-y divide-slate-100">
                            <thead>
                                <tr class="bg-slate-50/50 text-[10px] text-slate-400 uppercase tracking-widest">
                                    <th class="px-6 py-4">Name</th>
                                    <th class="px-6 py-4">Token Identifier</th>
                                    <th class="px-6 py-4">Permissions</th>
                                    <th class="px-6 py-4">Timeline Details</th>
                                    <th class="px-6 py-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($tokens as $token)
                                    <tr class="hover:bg-slate-50/40 transition-colors">
                                        <td class="px-6 py-4 font-bold text-slate-900 text-sm">
                                            {{ $token->name }}
                                        </td>
                                        <td class="px-6 py-4 font-mono text-xs text-slate-600">
                                            <code>{{ $token->masked_token ?? 'N/A' }}</code>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap gap-1.5">
                                                @foreach ($token->abilities as $ability)
                                                    <span class="px-2 py-0.5 bg-purple-50 text-purple-700 text-[10px] font-mono rounded border border-purple-100">
                                                        {{ $ability }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-slate-500">
                                            <div class="space-y-1">
                                                <p>Created: <span class="font-bold text-slate-700">{{ $token->created_at->diffForHumans() }}</span></p>
                                                <p>Last Used: <span class="font-bold text-slate-700">{{ $token->last_used_at ? $token->last_used_at->diffForHumans() : 'Never' }}</span></p>
                                                <p>Expires: 
                                                    @if ($token->expires_at)
                                                        @if ($token->expires_at->isPast())
                                                            <span class="font-bold text-rose-500">Expired ({{ $token->expires_at->diffForHumans() }})</span>
                                                        @else
                                                            <span class="font-bold text-purple-600">{{ $token->expires_at->diffForHumans() }}</span>
                                                        @endif
                                                    @else
                                                        <span class="font-bold text-slate-400">Never</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <button class="text-rose-600 hover:text-rose-800 font-extrabold hover:underline" wire:click="confirmTokenRevocation({{ $token->id }})">
                                                Revoke
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-slot>
            </x-action-section>
        </div>
    @endif

    <!-- Token Display Modal -->
    <x-dialog-modal wire:model="displayToken">
        <x-slot name="title">
            <div class="flex items-center space-x-3 pb-3 border-b border-slate-100">
                <div class="bg-gradient-to-tr from-purple-500 to-indigo-600 p-2 rounded-xl text-white shadow-md">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <span class="text-slate-900 font-extrabold text-lg">{{ __('API Token Created') }}</span>
            </div>
        </x-slot>

        <x-slot name="content">
            <div class="space-y-6 pt-4">
                <!-- Warning Banner -->
                <div class="p-4 bg-rose-50 border border-rose-100 rounded-2xl text-rose-800 text-sm font-semibold flex items-start shadow-sm">
                    <div class="bg-rose-500 text-white p-1.5 rounded-lg mr-3 flex-shrink-0 mt-0.5 animate-pulse">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-extrabold text-rose-955 mb-0.5">Security Warning</p>
                        <p class="text-xs text-rose-700/90 font-medium">This token will only be shown once. Please copy and store it securely.</p>
                    </div>
                </div>

                <!-- Monospace Token Display Box -->
                <div class="space-y-2">
                    <label class="text-xs font-extrabold text-slate-500 uppercase tracking-wider block">Your API Token</label>
                    <div class="flex items-center space-x-3 bg-slate-950 p-4 rounded-2xl border border-slate-900 shadow-inner group">
                        <input type="text" id="plainTextToken" readonly value="{{ $displayToken }}" class="bg-transparent border-none focus:ring-0 p-0 w-full text-emerald-400 font-mono text-sm select-all select-none" />
                        
                        <button onclick="copyTokenToClipboard()" class="text-slate-500 hover:text-white hover:scale-105 active:scale-95 transition-all p-2 bg-slate-900 rounded-xl border border-slate-800/80 shadow" title="Copy to clipboard">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m-2 4h.01M9 16h6m-6-3h6"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <script>
                function copyTokenToClipboard() {
                    var copyText = document.getElementById("plainTextToken");
                    copyText.select();
                    navigator.clipboard.writeText(copyText.value);
                    
                    var copyTextBtn = document.getElementById("copy-text-btn");
                    var copyIconBtn = document.getElementById("copy-icon-btn");
                    var copyBtnMain = document.getElementById("copy-btn-main");

                    // Visual feedback
                    copyTextBtn.innerText = "Copied!";
                    copyIconBtn.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />';
                    copyBtnMain.classList.remove('btn-purple');
                    copyBtnMain.classList.add('bg-emerald-600', 'text-white', 'hover:bg-emerald-700');

                    setTimeout(() => {
                        copyTextBtn.innerText = "Copy Token";
                        copyIconBtn.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m-2 4h.01M9 16h6m-6-3h6" />';
                        copyBtnMain.classList.add('btn-purple');
                        copyBtnMain.classList.remove('bg-emerald-600', 'text-white', 'hover:bg-emerald-700');
                    }, 2000);
                }
            </script>
        </x-slot>

        <x-slot name="footer">
            <button onclick="copyTokenToClipboard()" id="copy-btn-main" class="btn-purple font-semibold shadow-md mr-3 flex items-center space-x-2">
                <svg id="copy-icon-btn" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m-2 4h.01M9 16h6m-6-3h6"/>
                </svg>
                <span id="copy-text-btn">Copy Token</span>
            </button>
            <x-secondary-button wire:click="closeTokenModal" class="rounded-xl">
                {{ __('Done') }}
            </x-secondary-button>
        </x-slot>
    </x-dialog-modal>

    <!-- Token Revocation Confirmation Modal -->
    <x-confirmation-modal wire:model="tokenIdBeingRemoved">
        <x-slot name="title">
            <span class="text-slate-900 font-extrabold">{{ __('Revoke API Token') }}</span>
        </x-slot>

        <x-slot name="content">
            <span class="text-slate-500 font-medium">{{ __('Are you sure you want to revoke this API token? Any applications currently authenticating with it will lose access immediately.') }}</span>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="cancelRevocation" class="rounded-xl">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-danger-button class="ms-3 rounded-xl" wire:click="revokeToken">
                {{ __('Revoke Token') }}
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal>
</div>
