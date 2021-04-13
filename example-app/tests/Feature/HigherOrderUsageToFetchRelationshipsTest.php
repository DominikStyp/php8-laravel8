<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HigherOrderUsageToFetchRelationshipsTest extends TestCase
{

    public function test_filter()
    {
        /** @var Category $c */
        $c = Category::find(1);
        $names = $c->products->filter->isEven()->map->toArray()->pluck('name');
        /**
           Illuminate\Support\Collection {#1600
            #items: array:3 [
                0 => "ullam exercitationem"
                1 => "velit non"
                2 => "distinctio eum"
             ]
           }

         */
        $this->assertNotEmpty($names[0]);
        $this->assertNotEmpty($names[1]);
    }
}
