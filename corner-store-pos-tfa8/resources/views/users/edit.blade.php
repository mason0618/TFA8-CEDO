@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
    <section class="page-heading">
        <p class="eyebrow">User Accounts</p>
        <h1>Edit User</h1>
        <p class="lead">Update the details for {{ $user->full_name }}.</p>
    </section>

    <section class="form-panel">
        @include('partials.form-errors')
        <form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf
            @method('PUT')
            @include('users._form')
            <div class="form-field avatar-field">
                <label for="avatar">Profile picture <span class="optional">(optional)</span></label>
                <img class="avatar avatar-preview" width="96" height="96" src="{{ $user->avatar ? asset('storage/'.$user->avatar) : asset('images/avatar-placeholder.svg') }}" alt="Current profile picture for {{ $user->full_name }}">
                <input id="avatar" name="avatar" type="file" accept="image/jpeg,image/png,image/webp,image/gif,image/bmp" aria-describedby="avatar-help @error('avatar') avatar-error @enderror" @error('avatar') aria-invalid="true" @enderror>
                <p id="avatar-help" class="form-help">JPG, PNG, WebP, GIF, or BMP. Maximum 2 MB and 4096 pixels on each side. Leave this empty to keep the current picture.</p>
                @error('avatar') <p id="avatar-error" class="field-error">{{ $message }} Please select your image again.</p> @enderror
            </div>
            <div class="form-actions">
                <button class="button" type="submit">Update user</button>
                <a class="button button-light" href="{{ route('users.index') }}">Cancel</a>
            </div>
        </form>
    </section>
@endsection
