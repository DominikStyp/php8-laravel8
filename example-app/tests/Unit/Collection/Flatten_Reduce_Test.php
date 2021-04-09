<?php

namespace Tests\Unit\Collection;

use PHPUnit\Framework\TestCase;

class Flatten_Reduce_Test extends TestCase
{

    public function testCollapse(){
        $collection = collect([
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9],
        ]);

        $collapsed = $collection->collapse();

        // [1, 2, 3, 4, 5, 6, 7, 8, 9]
        $r = $collapsed->all();
        $this->assertCount(9, $r);
    }


    public function testFlatMap(){
        $users = collect([
            [
              'id' => 1,
              'name' => 'John'
            ],
            [
              'id' => 2,
              'name' => 'Ann'
            ]
        ]);

        /**
         * flatMap is essentially $collection->map($callback)->collapse()
         */
        $jsonedUsers = $users->flatMap(function($subArray){
            return [ json_encode($subArray) ];
        });

        foreach($jsonedUsers as $usrStr){
            $usrObj = json_decode($usrStr);
            $this->assertGreaterThan(0, $usrObj->id);
            $this->assertIsString($usrObj->name);
        }
    }

    public function testFlatten(){
        $c = collect([
            'one' => 1,
            'two' => [
                'three' => [
                    'four'
                ]
            ],
            'x' => 'four'
        ]);
        $f = $c->flatten();
        /*
         * [
                0 => 1
                1 => "four"
                2 => "four"
            ]
         */
        $this->assertCount(3, $f);
    }

}
