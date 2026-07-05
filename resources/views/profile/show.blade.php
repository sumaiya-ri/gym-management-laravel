@extends(auth()->user()->role === 'super_admin' ? 'layouts.super-admin' : 'layouts.' . auth()->user()->role)

@section('title', 'My Profile')
@section('subtitle', 'Manage your account settings and security credentials')

@section('content')
<div class="max-w-7xl mx-auto space-y-10">
    @if (Laravel\Fortify\Features::canUpdateProfileInformation())
        <div class="card p-8 md:p-10 bg-white">
            @livewire('profile.update-profile-information-form')
        </div>
    @endif

    @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
        <div class="card p-8 md:p-10 bg-white shadow-sm border border-gray-100 rounded-[1.25rem]">
            @livewire('profile.update-password-form')
        </div>
    @endif

    @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
        <div class="card p-8 md:p-10 bg-white shadow-sm border border-gray-100 rounded-[1.25rem]">
            @livewire('profile.two-factor-authentication-form')
        </div>
    @endif

    <div class="card p-8 md:p-10 bg-white shadow-sm border border-gray-100 rounded-[1.25rem]">
        @livewire('profile.logout-other-browser-sessions-form')
    </div>

    @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
        <div class="card p-8 md:p-10 bg-white shadow-sm border border-gray-100 rounded-[1.25rem]">
            @livewire('profile.delete-user-form')
        </div>
    @endif
</div>
@endsection
