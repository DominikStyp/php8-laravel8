<?php


namespace App\Console;


class InvokableObj {
    public function __invoke() {
       echo "Hello from InvokableObj";
    }

}
