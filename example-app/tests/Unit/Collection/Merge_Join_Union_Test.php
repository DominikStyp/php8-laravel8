<?php

namespace Tests\Unit\Collection;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;

class Merge_Join_Union_Test extends TestCase
{

    public function testCombine() {
        $combined = collect(['id', 'name', 'surname', 'email'])
            ->combine([222, 'name222', 'surname222', 'user222@example.com']);

        $this->assertEquals('surname222', $combined['surname']);
    }

    public function testJoinWithLast() {
        $c = ['a', 'b', 'c', 'd', 'e'];

        $str1 = collect($c)->join(',', ' AND ');

        $this->assertEquals('a,b,c,d AND e', $str1);
    }

    public function testMergeRecursive(){
        $c = collect([
            'name' => 'user1',
            'roles' => ['user']
        ]);

        $toMerge = collect([
            'roles' => ['admin', 'mod']
        ]);

        $merged = $c->mergeRecursive($toMerge);

        $this->assertCount(3, $merged['roles']);
        $this->assertTrue(collect($merged['roles'])->contains('admin'));
        $this->assertTrue(collect($merged['roles'])->contains('user'));
    }


    public function testUnion(){
        $c = collect([
            1 => ['a'],
            2 => ['b']
        ]);

        $union = $c->union([
            1 => ['b'],
            3 => ['c']
        ]);

        /**
         * WARNING! union prefers ORIGINAL values and not overrides them if they exist
         */
        $this->assertEquals('a', $union[1][0]);
        $this->assertEquals('c', $union[3][0]) ;
    }


}
