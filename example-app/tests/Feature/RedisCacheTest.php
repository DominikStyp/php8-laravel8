<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class RedisCacheTest extends TestCase
{

    private $testObj;

    public function setUp(): void {
        parent::setUp();
        $this->testObj = new \stdClass();
        $this->testObj->arr = ['one', 'two', 'three'];
        $this->testObj->scalar = 999;
        $this->testObj->boolean = false;
        $this->testObj->str = "this_is_some_string";
    }


    public function test_no_delay()
    {
        Cache::put("test1", json_encode($this->testObj), 3);
        $obj = Cache::get("test1");
        $decoded = $this->getDecoded($obj);
        $this->assertEquals("this_is_some_string", $decoded->str);
        $this->assertEquals("three", $decoded->arr[2]);
    }

    public function test_expired()
    {
        Cache::flush(); // clear entire cache
        Cache::put("test2", json_encode($this->testObj), 2);
        sleep(3);
        $obj = Cache::get("test2");
        $this->assertNull($obj);
    }

    public function test_with_store_explicitly()
    {
        Cache::flush(); // clear entire cache
        $store = Cache::store('redis');
        $store->put("test3", json_encode($this->testObj), 2);
        sleep(3);
        $obj = $store->get("test3");
        $this->assertNull($obj);
    }

    public function test_cache_forget()
    {
        $store = Cache::store('redis');
        $store->flush(); // clear entire cache
        $store->put("test4", json_encode($this->testObj), 10);
        sleep(2);

        $this->assertTrue($store->has('test4'));
        $store->forget('test4');

        $this->assertFalse($store->has('test4'));
        $obj = $store->get("test4");
        $this->assertNull($obj);
    }

    public function test_retrieve_and_store_in_one_operation()
    {
        $store = Cache::store('redis');
        $store->flush(); // clear entire cache
        $rememberTriggeredTimes = 0;
        // remember should be triggered twice:
        // - first time when item doesn't exist
        // - second time when item expires
        $rememberClosure = function() use (&$rememberTriggeredTimes) {
            $rememberTriggeredTimes++;
            return json_encode($this->testObj);
        };

        $result = $store->remember('test5',2, $rememberClosure);
        $this->assertEquals(1, $rememberTriggeredTimes);

        $decoded = $this->getDecoded($result);
        $this->assertEquals("this_is_some_string", $decoded->str);

        sleep(3);
        $store->remember('test5',2, $rememberClosure);; // remember should be triggered again here
        $this->assertEquals(2, $rememberTriggeredTimes);
    }

    public function test_retrieve_and_delete_in_one_operation()
    {
        $store = Cache::store('redis');
        $store->flush(); // clear entire cache
        $store->put("test6", json_encode($this->testObj), 10);

        $decoded = $this->getDecoded($store->get('test6'));
        $this->assertEquals("this_is_some_string", $decoded->str);

        // pull first time should also return the result
        $decoded = $this->getDecoded($store->pull('test6'));
        $this->assertEquals("this_is_some_string", $decoded->str);

        // this time key should be removed
        $this->assertFalse( $store->has('test6') );
        $this->assertNull( $store->get('test6') );
    }

    public function test_add_only_if_item_doesnt_exist() {
        $store = Cache::store('redis');
        $store->flush(); // clear entire cache
        $store->put("test7", "first_value", 10);
        // won't be added because it already exists in cache
        $store->add("test7", "second_value", 10);
        $this->assertEquals("first_value", $store->get("test7"));

        $store->forget("test7");
        // will be added if not exists
        $store->add("test7", "second_value", 10);
        $this->assertEquals("second_value", $store->get("test7"));
    }


    public function test_increment()
    {
        $store = Cache::store('redis');
        $store->flush(); // clear entire cache
        $store->forget('some_scalar');
        $store->increment('some_scalar');
        $store->increment('some_scalar');
        $store->increment('some_scalar');
        $this->assertEquals(3, $store->get('some_scalar'));
    }


    /**
     * @param mixed $obj
     * @return \stdClass
     */
    private function getDecoded(string $obj): \stdClass {
        $this->assertNotEmpty($obj);
        $this->assertIsString($obj);
        $decoded = json_decode($obj);
        $this->assertInstanceOf(\stdClass::class, $decoded);
        return $decoded;
    }
}
