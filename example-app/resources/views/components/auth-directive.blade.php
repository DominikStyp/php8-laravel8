@auth('dummy-guard')
    <div class="alert alert-success" role="alert">
        Yeah! Dummy guard mode activated!!! (Logged in)
    </div>
@endauth
@guest('dummy-guard')
    <div class="alert alert-success" role="alert">
        Yeah! Dummy guard says you are not logged in (Logged out)
    </div>
@endguest
