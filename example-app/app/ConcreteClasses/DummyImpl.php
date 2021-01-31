<?php


namespace App\ConcreteClasses;


use App\Contracts\DummyContract;

class DummyImpl implements DummyContract {

    private $version;

    public function __construct(string $version) {
        $this->version = $version;
    }

    public function dummy(): string {
        return "hello from DummyImpl ver: {$this->version}";
    }
}
