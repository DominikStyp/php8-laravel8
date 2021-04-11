<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductsCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $c = Category::all();
        $p = Product::all();
        $toAttach = $p->random(5)->pluck('id')->toArray();

        // higher order proxy can't work here
        // since in this case ->products() returns a Collection instead of BelongsToMany relationship
        // IMPORTANT! result of the higher order method is ALWAYS the result of the collection method
        // $c->each->products()->attach($toAttach);

        $c->each(function($c) use ($toAttach) { $c->products()->attach($toAttach); });
    }
}
