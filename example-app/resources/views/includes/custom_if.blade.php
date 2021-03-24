{{-- custom if --}}
@env('local')
    <div class="alert alert-success" role="alert">This is local environment</div>
@elseenv('production')
    <div class="alert alert-warning" role="alert">This is production</div>
@else
    <div>No environment specified</div>
@endenv
{{-- end custom if --}}
