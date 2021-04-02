@inject('str', 'Statamic\Support\Str')
@extends('statamic::outside')
@section('title', __('Log in'))

@section("content")

@include('statamic::partials.outside-logo')

<div class="card auth-card mx-auto">
  <div>
    <form method="POST">
      {!! csrf_field() !!}

      @if (isset($error))
        <p class="two-fa-error rounded-lg p-1 mb-2">{{ $error }}</p>
      @endif

      <div class="mb-4">
        <label class="mb-1">{{ __("twofa::auth.label") }}</label>
        <input type="number" class="two-fa-input input-text" name="code" id="code" pattern="\d{6}" maxlength="6" minlength="6" step="1" required>
      </div>
      <div class="flex justify-between items-center">
        <button type="submit" class="btn btn-primary">{{ __("twofa::auth.button") }}</button>
      </div>
    </form>
  </div>
</div>

@endsection
