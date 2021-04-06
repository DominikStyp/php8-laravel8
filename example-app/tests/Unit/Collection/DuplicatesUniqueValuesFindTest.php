<?php

namespace Tests\Unit\Collection;

use PHPUnit\Framework\TestCase;

class DuplicatesUniqueValuesFindTest extends TestCase
{

    public function testDuplicates() {
        $c = collect(['a', 'a', 'b', 'c', 'd', 'd', 'd']);

        // WARNING! This does not COUNT duplicates
        // it just gives  keys where duplicates are in ORIGINAL collection
        $duplicates = $c->duplicates();

        $this->assertCount(3, $duplicates);
        $this->assertEquals('a', $duplicates[1]);
        $this->assertEquals('d', $duplicates[5]);
        $this->assertEquals('d', $duplicates[6]);
        // duplicates with COUNT values now returns counted duplicates as expected
        /*
            array:2 [
              "a" => 1
              "d" => 2
            ]
         */
        $countedDuplicates = $c->duplicates()->countBy();

        $this->assertEquals(1,$countedDuplicates['a']);
        $this->assertEquals(2,$countedDuplicates['d']);
        $this->assertCount(2, $countedDuplicates);
    }

    public function testDuplicatesByKey() {
        $c = collect([
            ['a' => 'a'],
            ['a' => 'a'],
            ['b' => 'bb'],
            ['d' => 'dd', 'a' => 'aa'],
            ['x' => 'z', 'd' => 'ddd'],
            ['y' => 'yy', 'z' => 'zzz'],
        ]);

        $duplicates = $c->duplicates('a');
        /**
         * [
        1 => "a"
        4 => null
        5 => null
        ]

         */
        $this->assertCount(3, $duplicates);
    }

    public function testUnique(){
        $c = collect([1,1,1,2,2,3,4,5]);

        $this->assertCount(5, $c->unique());
    }

}
