@extends('layouts.app')

@push('head')
    <style>
    input, label {
        display:block;
    }
    label {
        font-weight: bold;
        padding-top:15px;
    }
    </style>
@endpush

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

                {{-- @component('components.foreach-example') @endcomponent --}}
                {{-- @component('components.form-things') @endcomponent --}}

                {{-- following include is aliased in the AppServiceProvider --}}
                <?php $arr = [
                    '<div>Cheers!</div>',
                    ['<div>Sub-cheers 1!</div>', '<div>Sub-cheers 2!</div>']
                ] ?>

                @myforelse

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
                        <div>
                            Alert content
                            <b style="color:#f6993f">Slot content is not escaped by htmlspecialchars()</b>
                        </div>
                    @endcomponent

                    @component('components.test-json') @endcomponent

                    <div>
                        Escaped var: {{ $escaped }}
                    </div>
                     <div>
                         Unescaped var: {!! $unescaped !!}
                     </div>
                    <div>
                        Blade extensions show how to get email of user 1: @user_email(1)
                    </div>

                    @include('includes.custom_if')

                    <label for="txtArea">jsonized text</label>
                    <textarea id="txtArea" rows="5" cols="30">@jsonize <p>some <b>bolded</b> text</p> @endjsonize</textarea>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
