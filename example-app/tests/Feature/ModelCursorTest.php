<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ModelCursorTest extends TestCase
{

    public function testCursor()
    {
       DB::enableQueryLog();

       foreach(Product::cursor() as $product)
       {
           $this->assertNotEmpty($product->name);
       }
       // under the hood it still uses "select * from `products`"
       // so be aware of that in order not to blow your DB with such queries
       dump(DB::getQueryLog());
    }

    public function testChunk()
    {
        DB::enableQueryLog();

        /**
         * Better in terms of database efficiency would be to use chunk,
         * because it limits the result to the certain amount of records
         *  "select * from `products` where `id` > ? order by `id` asc limit 4"
         */
        Product::chunkById(4, function($products){
            foreach($products as $product){
                $this->assertNotEmpty($product->name);
            }
        });

        dump(DB::getQueryLog());
    }
}
