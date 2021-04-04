<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CacheModelInDbTest extends TestCase
{

    public function test_cache_model()
    {
        $model = Product::find(1);
        Cache::put('product', $model, 60);
        $fetchedModel = Cache::get('product');
        $this->assertEquals($model->id, $fetchedModel->id);
        $this->assertEquals($model->name, $fetchedModel->name);
    }
}
