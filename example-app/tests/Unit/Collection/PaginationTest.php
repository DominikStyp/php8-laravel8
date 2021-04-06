<?php

namespace Tests\Unit\Collection;

use PHPUnit\Framework\TestCase;

class PaginationTest extends TestCase
{

    /**
     * @COOL
     */
    public function testPagination(){
        $p3_elems10 = collect(range(1,100))
            ->forPage(3, 10);

        $this->assertCount(10, $p3_elems10);
        $this->assertEquals(21, $p3_elems10->first());
        $this->assertEquals(30, $p3_elems10->last());
    }

}
