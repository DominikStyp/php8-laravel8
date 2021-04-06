<?php

namespace Tests\Unit\Collection;

use PHPUnit\Framework\TestCase;

class SearchingByValAndWhereConditionTest extends TestCase
{


    // filter by value equals
    public function testFirstWhere(){
        $person = collect([
            ['name' => "Joe", 'id' => 1],
            ['name' => "Donald", 'id' => 2],
            ['name' => "Barrack", 'id' => 3]
        ])
            ->firstWhere('name', 'Barrack');

        $this->assertEquals('Barrack', $person['name']);
    }

    /**
     * vs contains it returns the key of the first found value
     */
    public function testSearch(){
        $c = collect(['one', 'nine', 'ten', 'eleven', 'twelve', 'ten']);
        // returns first key found by the given value
        $this->assertEquals(2, $c->search('ten'));
    }

    /**
     * vs search it returns only boolean value
     */
    public function testContains() {
        $c = collect([1,2,3,4,6,7]);

        $this->assertFalse($c->contains(5));

        $res = $c->contains(function ($val, $key){
            return $val > 7;
        });

        $this->assertFalse($res);
    }


    // search by value
    public function testHas() {
        /**
         * WARNING! If key does not have value, has() returns false
         */
        $this->assertFalse(
            collect(['x', 'y', 'z'])->has('z')
        );
        $this->assertTrue(
            collect(['x' => 1, 'y' => 2, 'z' => 3])->has('z')
        );
    }

    /**
     * Search elemements by key value, vs search it returns multiple elements if found
     */
    public function testWhere(){
        $c = collect([
            ['name' => "Joe", 'id' => 1],
            ['name' => "Donald", 'id' => 2],
            ['name' => "Barrack", 'id' => 3],
            ['name' => "Joe", 'id' => 4],
        ]);

        $filtered = $c->where('name', 'Joe');

        $this->assertCount(2, $filtered);
    }

    // filter by value in range
    public function testWhereBetweenAndNotBetween(){
        $c = collect([
            ['name' => "Joe", 'id' => 1],
            ['name' => "Donald", 'id' => 2],
            ['name' => "Barrack", 'id' => 3],
            ['name' => "Joe", 'id' => 4],
            ['name' => "Stan", 'id' => 5],
        ]);

        $filtered = $c->whereBetween('id', [2,4]);

        $this->assertCount(3, $filtered);
        $this->assertEquals("Donald", $filtered->first()["name"]);
        $this->assertEquals("Joe", $filtered->last()["name"]);

        $filteredInverse = $c->whereNotBetween('id', [2,4]);

        $this->assertCount(2, $filteredInverse);
        $this->assertEquals("Joe", $filteredInverse->first()["name"]);
        $this->assertEquals("Stan", $filteredInverse->last()["name"]);
    }


    public function testWhereInAndNotIn(){
        $c = collect([
            ['name' => "Joe", 'id' => 1],
            ['name' => "Donald", 'id' => 2],
            ['name' => "Barrack", 'id' => 3],
            ['name' => "Joe1", 'id' => 4],
            ['name' => "Stan", 'id' => 5],
        ]);

        $filtered = $c->whereIn('id', [2,5]);

        $this->assertCount(2, $filtered);
        $this->assertEquals("Donald", $filtered->first()["name"]);
        $this->assertEquals("Stan", $filtered->last()["name"]);

        $filtered = $c->whereNotIn('id', [2,5]);

        $this->assertCount(3, $filtered);
        $this->assertEquals("Joe", $filtered->first()["name"]);
        $this->assertEquals("Joe1", $filtered->last()["name"]);
    }

    public function testWhereNullAndNotNull(){
        $c = collect([
            ['id' => 1, 'name' => "Joe"],
            ['id' => 2, 'name' => "Donald"],
            ['id' => 3, 'name' => null],
            ['id' => 4, 'name' => "George"],
            ['id' => 5, 'name' => null],
        ]);
        $this->assertCount(3, $c->whereNotNull('name'));
        $this->assertCount(2, $c->whereNull('name'));
    }

}
