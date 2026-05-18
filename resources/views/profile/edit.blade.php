@extends('layouts.bootstrap')

@section('title', __('Profile').' — '.config('app.name'))

@section('content')
    <div class="mx-auto" style="max-width: 44rem;">
        <p class="tl-kicker mb-1">{{ __('Account') }}</p>
        <h1 class="tl-section-title h2 mb-4">{{ __('Profile') }}</h1>
        <p class="text-secondary small mb-4">{{ __('Manage your name, email, password, and account security.') }}</p>

        @include('profile.partials.update-profile-information-form')

        @include('profile.partials.update-password-form')

        @include('profile.partials.delete-user-form')
    </div>
@endsection
