<?php

namespace Tests\Unit\Collection;

use PHPUnit\Framework\TestCase;

class FilteringByConditionTest extends TestCase
{

    // filter by value
    public function testEvery() {
        $strs = collect([
            'aa', 'bb', '', 'c', 'dd', '1', null
        ]);
        // checks if ALL elements match the condition
        $check = $strs->every(function ($el){
            return strlen($el) >= 2;
        });

        $this->assertFalse($check);
    }

    // filter keys
    public function testExcept(){
        $users = collect([
            ['id' => 1, 'name' => 'name1', 'email' => 'email1@s.com', 'password' => '123'],
            ['id' => 2, 'name' => 'name2', 'email' => 'email2@x.com', 'password' => '456']
        ]);

        $noIds = $users->map(function($el){
            return collect($el)->except(['id', 'password']);
        });

        $this->assertTrue(empty($noIds[0]['password']));
        $this->assertTrue(empty($noIds[1]['id']));
    }

    // filter by value
    public function testFilter1(){
        $res = collect([1,2,3,4,5])
            ->filter(function($el){
            return $el % 2 === 0;
        });

        $this->assertCount(2, $res);
    }



    /**
     * filter() expects TRUE to keep the element
     */
    public function testFilter() {
        $collection = collect([
            [
                'id' => 1,
                'name' => 'user1',
            ],
            [
                'id' => 11,
            ],
            [
                'id' => 2,
                'email' => 'user2@user2.example.com',
            ],
            [
                'id' => 3,
            ]
        ]);

        $withEmails = $collection->filter(function ($el) {
            return !empty($el['email']);
        });

        $this->assertCount(1, $withEmails);

        /**
         * WARNING! after filter keys are preserved
         */
        $this->assertEquals('user2@user2.example.com', $withEmails->get(2)['email']);
    }

    /**
     * reject() expects TRUE to remove the element
     */
    public function testReject(){
        $collection = collect([
            [
                'id' => 1,
                'name' => 'user1',
            ],
            [
                'id' => 11,
            ],
            [
                'id' => 2,
                'email' => 'user2@user2.example.com',
            ],
            [
                'id' => 3,
            ]
        ]);

        $withEmails = $collection->reject(function ($el) {
            return empty($el['email']);
        });

        $this->assertCount(1, $withEmails);
        /**
         * WARNING! after filter - keys are preserved
         */
        $this->assertEquals('user2@user2.example.com', $withEmails->get(2)['email']);
    }

    public function testForget_PASS_BY_REFERENCE() {
        $c = collect([
            'x' => 1,
            'y' => 2
        ]);
        // WARNING: modifies initial c by reference
        $c->forget('x');
        $this->assertCount(1, $c);
        $this->assertTrue(empty($c['x']));
    }


    /**
     * Skips x items from the beginning
     */
    public function testSkip(){
        $c = collect(range(1,10));

        // skips first 5 elements
        $c1 = $c->skip(5);

        $this->assertCount(5, $c1);
        $this->assertEquals(6, $c1->first());
    }

    /**
     * skips items UNTIL callback returns TRUE
     */
    public function testSkipUntil(){
        $c = collect(range(1,10));

        // skips elements UNTIL callback returns TRUE
        $c1 = $c->skipUntil(function($el) {
            return $el > 5;
        });

        $this->assertCount(5, $c1);
        $this->assertEquals(6, $c1->first());
    }

    // same as skipUntil() but expects reversed boolean condition
    public function testSkipWhile(){
        $c = collect(range(1,10));
        // skips elements WHILE callback returns TRUE

        $c1 = $c->skipWhile(function($el) {
            return $el <= 5;
        });

        $this->assertCount(5, $c1);
        $this->assertEquals(6, $c1->first());
    }


}
