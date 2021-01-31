<?php


namespace App\ConcreteClasses;


class ToExtend {

    protected $x = "original x";
    protected $z;

    public function __construct($z = 'original z') {
        $this->z = $z;
    }

    public function test(): string {
        return "test";
    }
    public final function original(): string {
        return "original";
    }

}
