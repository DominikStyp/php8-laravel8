<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ChunkWithIdOptimisedTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    protected function setUp(): void {
        parent::setUp();
        DB::enableQueryLog();
    }

    protected function tearDown(): void {
        dump(DB::getQueryLog());
        DB::disableQueryLog();
        parent::tearDown();
    }

    private function getUniqueRandomNumbers(int $from, int $to, int $amount)
    {
        $arr = range($from, $to);
        $res = [];

        foreach(array_rand($arr, $amount) as $randKey){
            $res[] = $arr[$randKey];
        }

        if($amount !== count($res)){
            throw new \Exception("Expected amount is not big enough, something wrong with params");
        }

        return $res;
    }

    /**
     * @return void
     */
    public function test_new_method()
    {
        $this->assertDatabaseCount('products', 1000);

        // remove random 20 products from between: 100 >= id >= 1
        Product::whereIn('id', $this->getUniqueRandomNumbers(1,100, 30) )
            ->delete();

        $this->assertDatabaseCount('products', 1000 - 30);

        // remove another 5 products from between 110 >= id >= 106
        Product::whereIn('id', range(106,110) )
            ->delete();

        $this->assertDatabaseCount('products', 1000 - 30 - 5);

        Product::chunkWithIdOptimised(100, function($products){
             $this->assertNotEmpty($products);
             $this->assertCount(100, $products);
             // last id should be 100 + 30 missing records + another 5 missing so 136
             $this->assertEquals(136, $products->last()->id);
             return false; // and we stop the chunking here
        } );
    }
}
