<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OnDifferentConnectionTest extends TestCase
{

    public function test_other_connection()
    {
        $products = Product::on('laravel8_db2_connection')
            ->select(['id', 'name'])
            ->get();

        $this->assertNotEmpty($products);
        $this->assertNotEmpty($products->get(0)->name);
    }

    public function test_other_connection_via_table()
    {
        $products = DB::connection('laravel8_db2_connection')
            ->select("select id, name from products");

        $this->assertNotEmpty($products);
        $this->assertNotEmpty($products[0]->name);
    }
}
