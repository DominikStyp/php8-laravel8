<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\Concerns\InteractsWithSession;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class IsAdminBySessionTest extends TestCase
{
    use InteractsWithSession;

    public function test_is_not_an_admin()
    {
        $response = $this->get('/is_admin_by_session');
        $response->assertStatus(500);
        $response->assertSeeText("You're not an admin", false);
    }

    public function test_is_an_admin()
    {
        $response = $this
            ->withSession(['is_admin' => true])
            ->get('/is_admin_by_session');
        $response->assertStatus(200);
        $response->assertDontSeeText("You're not an admin", false);
    }

}
