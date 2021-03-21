<?php


namespace App\Dummy\Guards;


use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class DummyGuard implements Guard {

    private $user;
    private $userProvider;

    public function __construct(UserProvider $userProvider) {
        $this->userProvider = $userProvider;
        $this->user = $userProvider->retrieveById(1);
        // we can pass the data to the guard from Laravel Echo via custom headers like 'token'
        Log::info("Dummy guard used! Request headers:", ['token' => Request::header('token')]);
    }

    public function check() {
        return true;
    }

    public function guest() {
        return $this->user;
    }

    public function user() {
        return $this->user;
    }

    public function id() {
        return 1;
    }

    public function validate(array $credentials = []) {
        return true;
    }

    public function setUser(Authenticatable $user) {
        $this->user = $user;
    }
}
