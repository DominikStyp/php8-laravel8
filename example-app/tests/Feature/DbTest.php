<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DbTest extends TestCase
{

    public function testConnectionIsValid()
    {
        /** @var \PDO $pdo */
       $pdo = DB::getPdo();
       $server = $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);
       $this->assertNotEmpty($server);
    }
}
