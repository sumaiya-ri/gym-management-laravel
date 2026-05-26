@extends(
    auth()->user()->role === 'super_admin' ? 'layouts.super-admin' : (
    auth()->user()->role === 'admin' ? 'layouts.admin' : (
    auth()->user()->role === 'trainer' ? 'layouts.trainer' : 'layouts.member'
)))

@section('title', 'API Token Management')
@section('subtitle', 'Manage secure programmatic access tokens with role-based scopes')

@section('content')
<div class="space-y-6">
    @livewire('profile.api-token-manager')
</div>
@endsection
