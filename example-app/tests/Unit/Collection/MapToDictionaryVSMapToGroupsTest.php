<?php

namespace Tests\Unit\Collection;

use PHPUnit\Framework\TestCase;

class MapToDictionaryVSMapToGroupsTest extends TestCase
{

    public function test_map_to_ditionary()
    {
        $c = collect([
            ['product' => 'cheese', 'price' => 20, 'in_stock' => true],
            ['product' => 'milk', 'price' => 5, 'in_stock' => true],
            ['product' => 'milk', 'price' => 7, 'in_stock' => true],
            ['product' => 'milk', 'price' => 8, 'in_stock' => true],
            ['product' => 'bread', 'price' => 2, 'in_stock' => false],
        ]);
        /**
         *[
            "cheese" => array:1 [
                0 => array:2 [
                    "price" => 20
                    "in_stock" => true
                ]
            ]
            "milk" => array:3 [
                0 => array:2 [
                    "price" => 5
                    "in_stock" => true
                ]
                1 => array:2 [
                    "price" => 7
                    "in_stock" => true
                ]
                2 => array:2 [
                    "price" => 8
                    "in_stock" => true
                ]
            ]
            "bread" => array:1 [
                0 => array:2 [
                "price" => 2
                    "in_stock" => false
                ]
            ]
        ]
         */
        $res = $c->mapToDictionary(function ($item){
           return [
               $item['product'] =>
                        [
                            'price' =>  $item['price'],
                            'in_stock' => $item['in_stock']
                        ]
           ];
        });
        $this->assertCount(3, $res['milk']);

    }


    public function test_map_to_groups()
    {
        $c = collect([
            ['product' => 'cheese', 'price' => 20, 'in_stock' => true],
            ['product' => 'milk', 'price' => 5, 'in_stock' => true],
            ['product' => 'milk', 'price' => 7, 'in_stock' => true],
            ['product' => 'milk', 'price' => 8, 'in_stock' => true],
            ['product' => 'bread', 'price' => 2, 'in_stock' => false],
            ['product' => 'bread', 'price' => 3, 'in_stock' => false],
            ['product' => 'bread', 'price' => 1.5, 'in_stock' => false],
        ]);

        // basically does the same job as mapToDictionary, but returns grouped collections
        $res = $c->mapToGroups(function ($item){
            return [
                $item['product'] =>
                    [
                        'price' =>  $item['price'],
                        'in_stock' => $item['in_stock']
                    ]
            ];
        });

        $this->assertCount(3, $res['milk']);

        $lowestPrices = $res->map(function ($oneGroup){
            return $oneGroup->min('price');
        });

        $this->assertEquals(5, $lowestPrices['milk']);
        $this->assertEquals(1.5, $lowestPrices['bread']);
    }

}
