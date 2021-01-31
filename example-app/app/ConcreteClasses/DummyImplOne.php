<?php


namespace App\ConcreteClasses;


use App\Contracts\DummyContract;

class DummyImplOne implements DummyContract {

    private $version;

    public function __construct(string $version) {
        $this->version = $version;
    }

    public function dummy(): string {
        return "this is dummy one";
    }
}
