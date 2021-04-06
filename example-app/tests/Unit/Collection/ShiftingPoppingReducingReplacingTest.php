<?php

namespace Tests\Unit\Collection;

use PHPUnit\Framework\TestCase;

class ShiftingPoppingReducingReplacingTest extends TestCase
{


    /**
     * Removing, appending, prepending elements
     */
    public function testPopPrependPush(){
        /**
         * WARNING pop() prepend() and push() operate on the ORIGINAL collection by reference
         */
        $c = collect([1,2,3]);
        // pop
        $el = $c->pop();
        $this->assertEquals(3, $el);
        $this->assertCount(2, $c);
        // prepend
        $c->prepend(5);
        $this->assertEquals(5, $c->first());
        $this->assertCount(3, $c);
        // push
        $c->push(6);
        $this->assertEquals(6, $c->last());
        $this->assertCount(4, $c);
    }

    public function testShift(){
        $c = collect([10,2,3]);
        $shifted = $c->shift();
        $this->assertEquals(10, $shifted);
        $this->assertCount(2, $c);
    }



    public function testReduce(){
        $c = collect([1,2,4,8,16,32,64,128]);
        // reduce() iterate on collection,
        // remembers returned state to $carry
        // returns final $carry result in the end
        $sum = $c->reduce(function ($carry, $item){
            return $carry + $item;
        });
        $this->assertEquals(255, $sum);
        $sameCollection = $c->reduce(function ($carry, $item){
            return collect($carry)->push($item);
        });
        $this->assertEquals($c->count(), $sameCollection->count());
        $this->assertEquals($c->get(1), $sameCollection->get(1));
        $this->assertEquals($c->last(), $sameCollection->last());
    }

    public function testReduceWithKeys(){
        $collection = collect([
            'usd' => 1400,
            'gbp' => 1200,
            'eur' => 1000,
        ]);

        $ratio = [
            'usd' => 1,
            'gbp' => 1.37,
            'eur' => 1.22,
        ];
        $balanceSum = $collection->reduceWithKeys(function ($carry, $value, $key) use ($ratio) {
            return $carry + ($value * $ratio[$key]);
        });
        $this->assertEquals(4264, $balanceSum);
    }

    public function testReplace(){
        /**
         * replace() method will also overwrite items in the collection that have matching numeric keys
         */
        $c = collect(['one', 'two' => 'two', 'three']);
        $replaced = $c->replace([
            'two' => 'two_1',
            2 => 'three_1'
        ]);
        $this->assertEquals('one', $replaced[0]);
        $this->assertEquals('two_1', $replaced['two']);
        $this->assertEquals('three_1', $replaced[2]);
    }

    public function testReplaceRecursive(){
        $config1 = [
            'db' => [
                'user' => 'u1',
                'pass' => 'p1',
                'modules' => [
                    'm1', 'm2'
                ]
            ]
        ];
        $config2 = [
            'db' => [
                'pass' => '',
                'modules' => [
                    'm3'
                ]
            ]
        ];
        $c = collect($config1);
        /**
         * WARNING! array elements are replaces according to their keys: 0, 1..etc.
         * Arrays are not FULLY REPLACED like you may expect
         */
        $replaced = $c->replaceRecursive($config2);
        $this->assertEquals('u1', $replaced['db']['user']);
        $this->assertEquals('', $replaced['db']['pass']);
        $this->assertCount(2, $replaced['db']['modules']);
        $this->assertEquals('m3', $replaced['db']['modules'][0]);
        $this->assertEquals('m2', $replaced['db']['modules'][1]);
    }
}
