@extends('layouts.app')

@section('left-nav')
    @parent
    <div class="dropdown-menu dropdown-menu-left" aria-labelledby="flashDropdown">
        <a class="dropdown-item" href="{{ route('get_flash') }}">
            {{ __('Test flash var') }}
        </a>
    </div>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @component('components.info')
                        {{ __('You are logged in !') }}
                    @endcomponent

                    @component('components.alert', ['title' => 'Alert title'])
                        Alert content
                    @endcomponent
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
