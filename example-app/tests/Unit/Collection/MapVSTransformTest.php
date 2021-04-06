<?php

namespace Tests\Unit\Collection;

use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;

class MapVSTransformTest extends TestCase
{

    public function testMap() {
        $collection = collect([
            [
                'id' => 1,
                'surname' => 'ccc',
            ],
            [
                'id' => 11,
                'surname' => 'aaa',
            ],
            [
                'id' => 2,
                'surname' => 'bbb',
            ],
            [
                'id' => 3,
                'surname' => 'zzz',
            ],
            [
                'id' => 4,
                'surname' => 'ttt',
            ],
        ]);

        $mapped = $collection->map(function ($el, $index){
            $el['surname'] = Str::ucfirst($el['surname']);
            return $el;
        });

        $this->assertEquals("Ttt", $mapped[4]['surname']);
        /**
         * WARNING! Original collection is intact, surname is only changed in returned collection
         */
        $this->assertNotEquals("Ttt", $collection[4]['surname']);
    }

    public function testMapSpread(){
        $c = collect([
            ['id' => 1, 'name' => 'name1', 'role' => 'admin'],
            ['id' => 2, 'name' => 'name2', 'role' => 'user'],
            ['id' => 3, 'name' => 'name3', 'role' => 'admin'],
        ]);

        $mapped = $c
            ->map(function($el){ // first we must get rid of string keys (not allowed in mapSpread)
                return array_values($el);
            })
            ->mapSpread(function ($id, $name, $role){ //then we spread the elements to the variables
                return ['id' => $id, 'name' => $name, 'role' => $role, 'name_role' => "{$name}_{$role}"];
            });

        $this->assertCount(3, $mapped);
        $this->assertEquals('name1_admin', $mapped[0]['name_role']);
        $this->assertEquals('name3_admin', $mapped[2]['name_role']);
    }

    public function testMapVsTransform(){
        $c = collect(['a', 'b', 'c', 'd']);

        //-------- MAP ----------
        $res = $c->map(function($el){ return "${el}_${el}"; } );

        // new collection is modified
        $this->assertEquals('a_a', $res->first());
        // ...but original stays the same
        $this->assertNotEquals('aa', $c->first());

        // ------- TRANSFORM ---------
        $c->transform(function($el){ return "${el}_modified"; });

        $this->assertEquals('a_modified', $c->first());
    }


}
