<?php
namespace App\Dummy\UserProviders;

use App\Dummy\Users\DummyAuthenticable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

class DummyUserProvider implements UserProvider {

    private $dummyAuthenticable;

    public function __construct() {
        $this->dummyAuthenticable = new DummyAuthenticable(
            1,
            "dummy",
            "password",
            "123"
        );
    }

    public function retrieveById($identifier) {
        $this->dummyAuthenticable->id = $identifier;
        return $this->dummyAuthenticable;
    }

    public function retrieveByToken($identifier, $token) {
        $this->dummyAuthenticable->id = $identifier;
        $this->dummyAuthenticable->remember_token = $token;
        return $this->dummyAuthenticable;
    }

    public function updateRememberToken(Authenticatable $user, $token) {
        $this->dummyAuthenticable->remember_token = $user->getRememberToken();
    }

    public function retrieveByCredentials(array $credentials) {
        $this->dummyAuthenticable->name = $credentials['name'];
        $this->dummyAuthenticable->password = $credentials['password'];
        return $this->dummyAuthenticable;
    }

    public function validateCredentials(Authenticatable $user, array $credentials) {
        return true;
    }
}
