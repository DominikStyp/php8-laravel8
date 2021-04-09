<?php

namespace Tests\Unit\Collection;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;

class CrossJoin_Join_Intersect_Zip_Test extends TestCase
{

    /**
     * Returns Cartesian matrix
     */
    public function testCrossJoinTwoArrays()
    {
        $c = collect([1,2]);
        /*
            [
                [1, 'a'],
                [1, 'b'],
                [2, 'a'],
                [2, 'b'],
            ]
        */
        $res = $c->crossJoin(['a','b']);
        $this->assertCount(4, $res);
    }

    public function testCrossJoinTHREEArrays()
    {
        /*
            [
                [1, 'a', 'I'],
                [1, 'a', 'II'],
                [1, 'b', 'I'],
                [1, 'b', 'II'],
                [2, 'a', 'I'],
                [2, 'a', 'II'],
                [2, 'b', 'I'],
                [2, 'b', 'II'],
            ]
        */
        $c = collect([1,2]);

        $matrix = $c->crossJoin(['a', 'b'], ['I', 'II']);
        $this->assertCount(8, $matrix);
    }

    // merge/join collection
    public function testZip(){
        $c = collect(['one', 'two', 'three']);
        $a = [1, 2];

        /**
         * Difference between crossJoin and this is merging only ONE values combination
         * so 'one', 1  ... without 'one', 2 etc.
         */
        $zipped = $c->zip($a);
        $this->assertEquals('one', $zipped[0][0]);
        $this->assertEquals(1, $zipped[0][1]);

        $this->assertEquals('two', $zipped[1][0]);
        $this->assertEquals(2, $zipped[1][1]);

        $this->assertEquals('three', $zipped[2][0]);
        $this->assertEquals(null, $zipped[2][1]);
    }

    /**
     * search the common part in the 2 collections
     */
    public function testIntersect() {
        $roles = ['admin', 'mod', 'subscriber', 'new', 'inactive'];

        $cnt = collect(['user'])->intersect($roles)->count();

        $this->assertEquals(0, $cnt);

        $commonRoles = collect(['admin','some_role', 'other_role', 'new'])
            ->intersect($roles);

        $this->assertTrue( $commonRoles->contains('admin') );
        $this->assertTrue( $commonRoles->contains('new') );
        $this->assertEquals(2, $commonRoles->count());
    }

    /**
     * search the common part by keys
     */
    public function testIntersectByKeys() {
        $c = collect([
            'id' => 1,
            'name' => 'Ann',
            'surname' => 'White',
            'health_insurance' => true
        ]);

        // USAGE EXAMPLE: we can force to remove keys from the initial collection
        //                that ARE NOT PRESENT in the second collection
        //                yet we retain values from original collection
        $intersect = $c->intersectByKeys([
            'id' => 0,
            'name' => '[placeholder]',
            'surname' => '[placeholder]',
            'status' => null
        ]);

        // value from the first collection
        $this->assertEquals( 'Ann', $intersect->get('name') );
        // value from the first collection
        $this->assertEquals( 'White', $intersect->get('surname') );
        // not common key so removed
        $this->assertEmpty( $intersect->get('status') );
        $this->assertEmpty( $intersect->get('health_insurance') );

    }

}
