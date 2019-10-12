@inject('str', 'Statamic\Support\Str')
@extends('statamic::outside')
@section('body_class', 'rad-mode')
@section('title', __('2FA Locked'))

@section('content')
<div class="logo pt-7">
    @svg('statamic-wordmark')
</div>

<div class="card auth-card mx-auto">
  <div>
    @if (isset($error))
      <p class="two-fa-error rounded-lg p-1 mb-2">{{ $error }}</p>
      <p><a href="/">Go home</a></p>
    @endif
  </div>
</div>
@endsection
