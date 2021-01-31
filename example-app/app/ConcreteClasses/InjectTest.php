<?php


namespace App\ConcreteClasses;


use App\Contracts\DummyContract;

class InjectTest {

    /**
     * @var DummyContract
     */
    private $contract;

    public function __construct(DummyContract $contract) {
        $this->contract = $contract;
    }

    public function getDummy(): string {
        return $this->contract->dummy();
    }

    public function getObject(): DummyContract {
        return $this->contract;
    }
}
