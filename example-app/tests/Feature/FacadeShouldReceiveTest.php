<?php

namespace Tests\Feature;

use App\Models\User;
use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FacadeShouldReceiveTest extends TestCase
{
    use RefreshDatabase;

    public function test_cache_facade()
    {
        Cache::shouldReceive('get')
            ->once()
            ->with('abc')
            ->andReturn('trele');

        $this->assertEquals('trele', Cache::get('abc'));
    }

    public function test_storage_facade()
    {
        $fileFrom = storage_path('logs/laravel.log');
        $fileTo = storage_path('logs/laravel1.log');

        Storage::shouldReceive('copy')
            ->once()
            ->with($fileFrom, $fileTo)
            ->andReturn(true);

        $this->assertTrue(Storage::copy($fileFrom, $fileTo));
    }





}
