<?php
namespace App\Dummy\Users;

use Illuminate\Contracts\Auth\Authenticatable;

class DummyAuthenticable implements Authenticatable {

    public $id;
    public $name;
    public $password;
    public $remember_token;

    public function __construct(
        int $id,
        string $name,
        string $password,
        string $remember_token
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->password = $password;
        $this->remember_token = $remember_token;
    }

    public function getAuthIdentifierName() {
        return "id";
    }

    public function getAuthIdentifier() {
        return $this->id;
    }

    public function getAuthPassword() {
        return $this->password;
    }

    public function getRememberToken() {
        return $this->remember_token;
    }

    public function setRememberToken($value) {
        $this->remember_token = $value;
    }

    public function getRememberTokenName() {
        return "remember_token";
    }
}
