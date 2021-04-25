<?php

namespace Tests\Unit\Collection;

use PHPUnit\Framework\TestCase;

class NthInDisplayColumnsTest extends TestCase
{

    public function test_nth()
    {
        $c = collect(range(1,9));
        // we start at 1 and we get every item 2 steps ahead
        // so 1, 3, 5, 7, 9
        $oddNumbers = $c->nth(2);
        // 2, 4, 6, 8
        $evenNumbers = $c->nth(2, 1);
        $this->assertCount(5, $oddNumbers);
        $this->assertCount(4, $evenNumbers);

    }
}
