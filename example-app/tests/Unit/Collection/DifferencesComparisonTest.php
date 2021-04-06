<?php

namespace Tests\Unit\Collection;

use PHPUnit\Framework\TestCase;

class DifferencesComparisonTest extends TestCase
{
    public function testDiff()
    {
        $c = collect([1,2,3,5,7]);

        $diff = $c->diff([2,3]);

        $this->assertCount(3, $diff);

        $this->assertTrue($diff->contains(1));
        $this->assertTrue($diff->contains(5));
        $this->assertTrue($diff->contains(7));
    }

    public function testDiffAssoc()
    {
        $c = collect([
            'one' => 1,
            'two' => 2,
            'three' => 3,
        ]);

        $diff = $c->diffAssoc([
            'one' => 1,
            'two' => 2,
            'four' => 4
        ]);

        $this->assertEquals(3, $diff['three']);
        $this->assertCount(1, $diff);
    }

    public function testDiffAssoc2()
    {
        $c = collect([
            'one' => 1,
            'two' => 2,
            'three' => 3,
        ]);

        // this check  BOTH value + key exists
        $diff = $c->diffAssoc([
            'one' => 11,
            'two' => 2,
            'four' => 4
        ]);

        $this->assertEquals(1, $diff['one']); // 'one' => 1 is not in second collection
        $this->assertEquals(3, $diff['three']); // 'three' => 3 is not in second collection
        $this->assertCount(2, $diff);
    }

    public function testDiffKeys()
    {
        $c = collect([
            'one' => 1,
            'two' => 2,
            'three' => 3,
        ]);

        // this check ONLY if keys exist
        $diff = $c->diffKeys([
            'one' => 11,
            'two' => 2,
            'four' => 4
        ]);

        $this->assertEquals(3, $diff['three']); // 'three' => 3 is not in second collection
        $this->assertCount(1, $diff);
    }


}
