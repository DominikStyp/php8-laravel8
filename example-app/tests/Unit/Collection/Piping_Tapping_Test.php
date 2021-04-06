<?php

namespace Tests\Unit\Collection;

use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class Piping_Tapping_Test extends TestCase
{

    public function testPipe(){
        $c = collect(range(1,20));
        $ret = $c
            ->pipe(function (Collection $c){
                return $c->transform(function ($el){
                    return $el*2;
                });
            })
            ->pipe(function(Collection $c){
                list($lower, $higher) = $c->partition(function($el){
                    return $el <= 20;
                });
                return $higher;
            })
            ->pipe(function(Collection $c){
                return $c->reverse();
            });
        $this->assertEquals(40, $ret->first());
        $this->assertEquals(22, $ret->last());
        $this->assertCount(10, $ret);
    }

    public function testTap(){
        $c = collect([2, 4, 3, 1, 10, 5]);

        $sorted = $c->sort()

            ->tap(function (Collection $col){
                $this->assertEquals(1, $col->first());
                $this->assertEquals(10, $col->last());
                $col->shift(); //does not affect original collection here
                $col->reject(function ($el){
                    return true;
                });
            })

            ->slice(1)

            ->tap(function ($col){
                $this->assertEquals(2, $col->first());
                $this->assertEquals(10, $col->last());
            });

        $this->assertEquals(2, $sorted->first());
        $this->assertEquals(10, $sorted->last());
    }


}
