@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-50/50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        {{-- Page Title --}}
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Account Settings</h1>
            <p class="text-sm text-gray-500 font-medium">Manage your identity and security preferences.</p>
        </div>

        {{-- Profile Info Section --}}
        <div class="p-8 bg-white rounded-3xl border border-gray-100 shadow-sm transition-all hover:shadow-md">
            @include('profile.partials.update-profile-information-form')
        </div>

        {{-- Security Section --}}
        <div class="p-8 bg-white rounded-3xl border border-gray-100 shadow-sm transition-all hover:shadow-md">
            @include('profile.partials.update-password-form')
        </div>

        {{-- Danger Zone --}}
        <div class="p-8 bg-rose-50/50 rounded-3xl border border-rose-100 shadow-sm">
            @include('profile.partials.delete-user-form')
        </div>
        
    </div>
</div>
@endsection