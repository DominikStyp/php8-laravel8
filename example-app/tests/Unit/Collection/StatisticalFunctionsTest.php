<?php

namespace Tests\Unit\Collection;

use PHPUnit\Framework\TestCase;

class StatisticalFunctionsTest extends TestCase
{

    public function testMax(){
        $c = collect([
            ['cnt' => 99],
            ['cnt' => 10],
            ['cnt' => '100'],
        ]);

        $this->assertEquals(100, $c->max('cnt'));
    }

    /**
     * Value appearing the most in the collection
     * https://pl.wikipedia.org/wiki/Dominanta_(statystyka)
     *
     */
    public function testMode()
    {
        $c = collect([1,2,3,3,2,1,3,1,2,5,4,3]); // should be 3

        $modeArr = $c->mode();

        $this->assertEquals(3, $modeArr[0]);
    }

    /**
     * The median is the value separating the higher half from the lower half of a data sample
     * https://pl.wikipedia.org/wiki/Mediana
     */

    public function testMedian()
    {
        $c = collect([1,3,4,5,7,9,10]); // should be 5

        $this->assertEquals(5, $c->median());
    }

}
