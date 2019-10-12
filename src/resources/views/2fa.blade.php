@inject('str', 'Statamic\Support\Str')
@extends('statamic::outside')
@section('body_class', 'rad-mode')
@section('title', __('2FA'))

@section('content')
<div class="logo pt-7">
    @svg('statamic-wordmark')
</div>

<div class="card auth-card mx-auto">
  <div>
    <form method="POST">
      {!! csrf_field() !!}

      @if (isset($error))
        <p class="two-fa-error rounded-lg p-1 mb-2">{{ $error }}</p>
      @endif

      <div class="mb-4">
        <label class="mb-1">{{ __('2FA Code') }}</label>
        <input type="password" class="input-text input-text" name="code" id="code">
      </div>
      <div class="flex justify-between items-center">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
      </div>
    </form>
  </div>
</div>

@endsection
