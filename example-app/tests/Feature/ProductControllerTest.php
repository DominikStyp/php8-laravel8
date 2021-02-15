<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\ResourceControllerAbstract;
use Tests\ResourceControllerTestInterface;
use Tests\TestCase;

class ProductControllerTest extends ResourceControllerAbstract
{

    private $prefix = '/product';


    public function test_index_GET() {
        $content = $this->jsonGet("{$this->prefix}/");
        $this->assertStringContainsString("::index", $content->resource_method);
    }

    public function test_create_GET() {
        $content = $this->jsonGet("{$this->prefix}/create");
        $this->assertStringContainsString("::create", $content->resource_method);
        $this->assertStringContainsString("to create the resource", $content->message);
    }

    public function test_store_POST() {
       $data = [
           'id' => 4,
           'name' => 'product4',
           'desc' => 'product 4 description'
       ];
       $content = $this->jsonPost("{$this->prefix}/", $data);
       $this->assertStringContainsString("::store", $content->resource_method);
       $this->assertEquals("new product created", $content->message);
    }

    public function test_show_GET() {
        $content = $this->jsonGet("{$this->prefix}/3");
        $this->assertStringContainsString("::show", $content->resource_method);
        $this->assertIsObject($content->data);
        $this->assertEquals(3, $content->data->id);
        $this->assertEquals('display product', $content->message);
        $this->assertEquals("product3", $content->data->name);
    }

    public function test_edit_GET() {
        $content = $this->jsonGet("{$this->prefix}/3/edit");
        $this->assertEquals('edit form for product', $content->message);
        $this->assertStringContainsString("::edit", $content->resource_method);
    }

    public function test_update_PUT_PATCH() {
        $content = $this->jsonPut("{$this->prefix}/3", ['name' => 'product33']);
        $this->assertEquals('product updated', $content->message);
        $this->assertStringContainsString("::update", $content->resource_method);
    }

    public function test_destroy_DELETE() {
        $content = $this->jsonDelete("{$this->prefix}/3");
        $this->assertEquals('product destroyed', $content->message);
        $this->assertStringContainsString("::destroy", $content->resource_method);
    }
}
