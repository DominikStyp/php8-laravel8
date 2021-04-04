<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DBSelectOnMultipleConnectionsTest extends TestCase
{

    protected function setUp(): void {
        parent::setUp();
        $res1 = DB::connection('sqlite')
            ->update("UPDATE users SET email = ? WHERE id = ?", ['test123@mail.com', 1]);
        $res2 = DB::connection('sqlite_mirror')
            ->update("UPDATE users SET email = ? WHERE id = ?", ['test456@mail.com', 1]);
        $this->assertGreaterThan(0, $res1);
        $this->assertGreaterThan(0, $res2);

    }

    public function test_two_connections()
    {
       $result = DB::connection('sqlite')->selectOne("SELECT * FROM users");
       $this->assertIsNumeric($result->id);
       $this->assertEquals("test123@mail.com", $result->email);

       $result = DB::connection('sqlite_mirror')->selectOne("SELECT * FROM users");
       $this->assertIsNumeric($result->id);
       $this->assertEquals("test456@mail.com", $result->email);
    }

    public function test_select_with_default_connection()
    {
        DB::setDefaultConnection('sqlite_mirror');
        $result = DB::selectOne("SELECT email FROM users WHERE id = ?", [1]);
        $this->assertEquals("test456@mail.com", $result->email);
    }

    public function test_select_with_database()
    {
        DB::setDefaultConnection('laravel8_db1_connection');

        DB::update("UPDATE laravel8_db1.users SET email = ? WHERE id = ?", ['x@x.com', 1]);

        DB::update("UPDATE laravel8_db2.users SET email = ? WHERE id = ?", ['y@y.com', 1]);

        $result = DB::select("SELECT * FROM laravel8_db1.users WHERE id = ?", [1]);
        $this->assertEquals("x@x.com", $result[0]->email);

        $result = DB::select("SELECT * FROM laravel8_db2.users WHERE id = ?", [1]);
        $this->assertEquals("y@y.com", $result[0]->email);
    }
}
