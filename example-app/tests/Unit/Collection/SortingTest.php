<?php

namespace Tests\Unit\Collection;

use PHPUnit\Framework\TestCase;

class SortingTest extends TestCase
{


    public function testSortAndSortDescSimple(){
        $c = collect([7,10,3,4,2]);

        // sort
        $this->assertEquals(10, $c->sort()->last());
        $this->assertEquals(2, $c->sort()->first());

        // sortDesc
        $this->assertEquals(10, $c->sortDesc()->first());
        $this->assertEquals(2, $c->sortDesc()->last());
    }

    public function testSortAdvanced(){
        $c = collect([
            ['id' => 5, 'name' => 'name5'],
            ['id' => 2, 'name' => 'name2'],
            ['id' => 7, 'name' => 'name7'],
            ['id' => 3, 'name' => 'name3']
        ]);

        // callback is passed to usort()
        // https://www.php.net/manual/en/function.usort.php
        $sorted = $c->sort(function ($a, $b){
            /**
             *  echo 1 <=> 2, PHP_EOL; // -1
            echo 1 <=> 1, PHP_EOL; // 0
            echo 2 <=> 1, PHP_EOL; // 1
             */
            return $a['id'] <=> $b['id'];
        })->values(); // reset keys on the end

        $this->assertEquals(2, $sorted[0]['id']);
        $this->assertEquals(3, $sorted[1]['id']);
        $this->assertEquals(7, $sorted->last()['id']);
    }

    public function testSortByAndSortByDesc(){
        $c = collect([
            ['id' => 5, 'name' => 'name5'],
            ['id' => 2, 'name' => 'name2'],
            ['id' => 7, 'name' => 'name7'],
            ['id' => 3, 'name' => 'name3']
        ]);

        $sorted = $c->sortBy('id', SORT_NATURAL)->values();

        $this->assertEquals(2, $sorted[0]['id']);
        $this->assertEquals(3, $sorted[1]['id']);
        $this->assertEquals(7, $sorted->last()['id']);

        $desc = $c->sortByDesc('id', SORT_NATURAL)->values();

        $this->assertEquals(7, $desc[0]['id']);
        $this->assertEquals(5, $desc[1]['id']);
        $this->assertEquals(2, $desc->last()['id']);
    }

    public function testSortKeys(){
        $c = collect([
            'c' => 3,
            'a' => 1,
            'b' => 2,
            'e' => 5,
            'd' => 4,

        ]);
        // sortKeys
        $sorted = $c->sortKeys();

        $this->assertEquals(1, $sorted->first());
        $this->assertEquals('c', $sorted->keys()[2]);
        $this->assertEquals(5, $sorted->last());

        // sortKeysDesc
        $desc = $c->sortKeysDesc();

        $this->assertEquals(5, $desc->first());
        $this->assertEquals('d', $desc->keys()[1]);
        $this->assertEquals(1, $desc->last());
    }

}
