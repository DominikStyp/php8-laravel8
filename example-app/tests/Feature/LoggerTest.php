<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class LoggerTest extends TestCase
{


    public function test_logger()
    {
        Log::info("123456");
        $this->assertTrue(true);
    }
}
