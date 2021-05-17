<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Utils\LimitIdFinder;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FindMinOrMaxIdWithinRecordsAmountTest extends TestCase
{
    protected function setUp(): void {
        parent::setUp();
        DB::enableQueryLog();
    }

    protected function tearDown(): void {
        //dump(DB::getQueryLog());
        DB::disableQueryLog();
        parent::tearDown();
    }


    public function closest_id_provider()
    {
        return [
            [1, 100],
            [5, 45],
            [2, 20],
            [10, 15],
            [28, 45],
            [990, 10],
        ];
    }


    /**
     * @dataProvider closest_id_provider
     *
     * @param $lastId
     * @param $expectedRecords
     */
    public function test_check_closest_id($lastId, $expectedRecords)
    {
        $finder = new LimitIdFinder('products', $expectedRecords, $lastId);
        $upperLimitId = $finder->findUpperId();

       // echo "\nQueries count", $finder->getNextIdQueriesCount();

        $cnt = DB::table('products')
            ->select('id')
            ->where('id', '>', $lastId)
            ->where('id', '<=', $upperLimitId)
            ->count();

        $this->assertEquals($expectedRecords, $cnt);
    }

}
