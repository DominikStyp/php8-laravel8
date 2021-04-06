<?php

namespace Tests\Unit\Collection;

use PHPUnit\Framework\TestCase;

class PickingSpecificKeysTest extends TestCase
{
    /**
     * returns items with ONLY the keys defined as arguments
     */
    public function testOnly(){
        $c = collect(
            [
                'id' => 1,
                'name' => 'name1',
                'email' => 'e@mail.com',
                'role' => 'admin',
            ],
        );

        $onlyName = $c->only('name');

        $this->assertCount(1, $onlyName);
        $this->assertEquals('name1', $onlyName['name']);
        $this->assertTrue(empty($onlyName['role']));
        $this->assertTrue(empty($onlyName['email']));
        $this->assertTrue(empty($onlyName['id']));
    }


    /**
     * returns items with the keys EXCEPT defined as arguments
     */
    public function testExcept(){
        $c = collect(
            [
                'id' => 1,
                'name' => 'name1',
                'email' => 'e@mail.com',
                'role' => 'admin',
                'password' => 'admin123'
            ],
        );

        $exceptPassword = $c->except('password');

        $this->assertCount(4, $exceptPassword);
        $this->assertEquals('name1', $exceptPassword['name']);
        $this->assertEquals('admin', $exceptPassword['role']);

        $this->assertTrue(empty($exceptPassword['password']));
    }

    /**
     * returs array with SPECIFIC-KEY elements
     */
    public function testPluck(){
        $c = collect([
            ['id' => 1, 'name' => 'name1', 'role' => 'admin'],
            ['id' => 2, 'name' => 'name2', 'role' => 'user'],
            ['id' => 33, 'name' => 'name3', 'role' => 'admin'],
        ]);

        $idsOnly = $c->pluck('id');

        $this->assertEquals(1, $idsOnly[0]);
        $this->assertEquals(2, $idsOnly[1]);
        $this->assertEquals(33, $idsOnly[2]);
    }

    /**
     * pulls element by key and removes it from collection
     * AFFECTS ORIGINAL COLLECTION!
     */
    public function testPull(){
        $c = collect([
            'id' => 1,
            'name' => 'don',
            'email' => 'shaggy@s.com',
            'role' => 'admin'
        ]);

        $name = $c->pull('name');

        $this->assertEquals('don', $name);
        $this->assertCount(3, $c);
        $this->assertTrue(empty($c['name']));
        $this->assertEquals(1, $c['id']);
        $this->assertEquals('shaggy@s.com', $c['email']);
        $this->assertEquals('admin', $c['role']);
    }

    public function testPut(){
        $c = collect([
            'id' => 1,
            'name' => 'name1',
            'role' => 'admin'
        ]);

        // places element at certain key
        $c->put('role', 'mod');
        $this->assertCount(3, $c);
        $this->assertEquals('mod', $c['role']);
    }


}
