<?php

namespace Tests\Feature;

use App\Jobs\UpdateProductJob;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SerializeModelsUsingJobTest extends TestCase
{

    protected function setUp(): void {
        parent::setUp();
        Artisan::call('queue:clear');
    }

    public function test_serialize_model_in_database()
    {
        $this->assertTrue(Product::findOrFail(1)->update(['stock_amount' => 0]));
        $this->assertEquals(0, Product::findOrFail(1)->stock_amount);

        UpdateProductJob::dispatch( Product::findOrFail(1) )->onConnection('database');

        Artisan::call('queue:work', [
            '--stop-when-empty' => true,
            'connection' => 'database'
        ]);

        $this->assertEquals(1, Product::findOrFail(1)->stock_amount);
    }
}
