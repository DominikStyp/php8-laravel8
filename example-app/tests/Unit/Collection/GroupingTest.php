<?php

namespace Tests\Unit\Collection;

use PHPUnit\Framework\TestCase;

class GroupingTest extends TestCase
{

    /**
     * Grouping by specific key that has specific value
     */
    public function testGroupBy() {
        $c = collect([
            ['id' => 1, 'name' => 'name1', 'role' => 'user'],
            ['id' => 2, 'name' => 'name2', 'role' => 'user'],
            ['id' => 3, 'name' => 'name3', 'role' => 'admin'],
            ['id' => 4, 'name' => 'name4', 'role' => 'user'],
            ['id' => 5, 'name' => 'name4', 'role' => 'mod'],
        ]);
        $grouped = $c->groupBy('role');
        // assert only users
        $this->assertTrue( $grouped['user']->every(function($e){ return $e['role'] === 'user';  }) );
        // assert only admins
        $this->assertTrue( $grouped['admin']->every(function($e){ return $e['role'] === 'admin';  }) );
    }

    /**
     * Custom mapping
     */
    public function testMapToGroups(){
        $c = collect([
            ['id' => 1, 'name' => 'name1', 'role' => 'admin'],
            ['id' => 2, 'name' => 'name2', 'role' => 'user'],
            ['id' => 3, 'name' => 'name3', 'role' => 'admin'],
            ['id' => 4, 'name' => 'name3', 'role' => 'admin'],
            ['id' => 5, 'name' => 'name3', 'role' => 'mod'],
        ]);
        $grouped = $c->mapToGroups(function ($item){
            return [ $item['role']  => $item['id'] ];
        });
        $this->assertCount(3, $grouped['admin']);
        $this->assertCount(1, $grouped['mod']);
        $this->assertCount(3, $grouped);
    }

}
