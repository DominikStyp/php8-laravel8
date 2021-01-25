<?php

namespace Tests\Unit;

use App\SpecificCollection\NullableStringCollection;
use PHPUnit\Framework\TestCase;

class CollectionHigherOrderMessagesTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testNullize()
    {
        $c = collect([
            ['id' => 1, 'name' => 'name1', 'surname' => 'surname1'],
            ['id' => 2, 'name' => 'name2', 'surname' => ''],
            ['id' => 3, 'name' => '', 'surname' => 'surname3'],
            ['id' => 4, 'name' => '', 'surname' => ''],
        ]);
        $nullableItems = $c->mapInto(NullableStringCollection::class);

        // higher order messages will invode each() method
        // on every OBJECT in the collection
        // so objects MUST have a higher-order-message-method implemented

        $nullableItems->each->nullizeEmptyString();

        // before loop surname must be string
        $this->assertTrue(gettype($c[1]['surname']) === "string");

        // so now surname is null, and not string
        $this->assertIsNotString($nullableItems[1]['surname']);
        $this->assertTrue(gettype($nullableItems[1]['surname']) === "NULL");
    }
}
