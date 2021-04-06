<?php

namespace Tests\Unit\Collection;

use PHPUnit\Framework\TestCase;

class ChunkingSplittingSlicingTest extends TestCase
{

    public function testChunk() {
        $c = collect(range(0,99));

        $chunks = $c->chunk(10);

        foreach($chunks as $chnk){
            $this->assertCount(10, $chnk);
        }
    }

    public function testChunkWhile() {
        $c = collect(str_split('AAAAAABBCCCD'));

        $chunks = $c->chunkWhile(function ($value, $key, $currentChunk) {
            //if last element of this chunk equals $value, chunk is not splitted
            // echo json_encode($currentChunk) . "\n";
            return $value === $currentChunk->last();
        });

        $all = $chunks->all();

        foreach($all[0] as $letter){
            $this->assertEquals('A', $letter);
        }

        foreach($all[1] as $letter){
            $this->assertEquals('B', $letter);
        }

        $this->assertCount(3, $all[2]);
        $this->assertCount(1, $all[3]);
    }


    public function testSplice(){
        $c = collect([1, 2, 3, 4, 5]);

        // $chunk = [3]
        // $c = [1,2,4,5]
        $chunk = $c->splice(2, 1);

        $this->assertCount(1, $chunk);
        $this->assertEquals(3, $chunk->first());

        // splice also REMOVES the chunk from original collection
        // affects original collection

        $this->assertCount(4, $c);

        $this->assertFalse($c->search(3)); // 3 is removed

        $this->assertIsInt($c->search(1));
        $this->assertIsInt($c->search(2));
        $this->assertIsInt($c->search(4));
        $this->assertIsInt($c->search(5));
    }

    public function testSlice(){
        $c = collect(range(1,10));

        // offset number of elements are skipped
        // length number of elements are included

        // $sliced = [5, 6, 7]
        // $c = [1,2,3,4,5,6,7,8,9,10]

        $sliced = $c->slice(4, 3)->values();

        // slice (UNLINE splice) does not affect original collection
        // no element of the original array was affected
        foreach(range(1,10) as $num) {
            $this->assertIsInt($c->search($num));
        }

        $this->assertCount(3, $sliced);
    }

    public function testSplit(){
        // if there is MORE elements than even groups
        // first group will have more elements
        // last will always have less nr of elements
        $c = collect(range(1,20));

        $chunks = $c->split(3);

        $this->assertCount(7, $chunks[0]);
        $this->assertCount(7, $chunks[1]);
        $this->assertCount(6, $chunks[2]);

        // difference split() vs splitIn() here is
        // that split() distributes items to groups MOST evenly possible
        // ex: 1,1,0 ... 1,1,1 ... 2,1,1 ... 2,2,1... 2,2,2... 3,2,2 etc...
        $c = collect(range(1,22));

        $chunks = $c->split(3);

        $this->assertCount(8, $chunks[0]);
        $this->assertCount(7, $chunks[1]);
        $this->assertCount(7, $chunks[2]);

    }

    public function testSplitIn(){
        $c = collect(range(1,20));

        $chunks = $c->splitIn(3);

        $this->assertCount(7, $chunks[0]);
        $this->assertCount(7, $chunks[1]);
        $this->assertCount(6, $chunks[2]);

        // difference between split() vs splitIn() here:
        // 8, 7, 7  vs 8, 8, 6
        // this is because splitIn() tries to fill-in groups
        // to max (8) elements and then allocate remainder to the last group
        $c = collect(range(1,22));

        $chunks = $c->splitIn(3);

        $this->assertCount(8, $chunks[0]);
        $this->assertCount(8, $chunks[1]);
        $this->assertCount(6, $chunks[2]);
    }


    /**
     * Split collection into partitions by certain condition
     *
     * TODO: next  https://laravel.com/docs/8.x/collections#method-partition
     */
    public function testPartition(){
        $c = collect(['aaa','b','ccc','d','eeeee']);
        /**
         * WARNING! Keys in $long and $short ARE PRESERVED from original collection
         *  [
        0 => "aaa"
        2 => "ccc"
        4 => "eeeee"
        ]
         */

        list($long, $short) = $c->partition(function ($el){
            return strlen($el) > 1;
        });

        $this->assertCount(3, $long);
        $this->assertCount(2, $short);
        /**
         * values() is resetting the collection keys from 0, 2, 4 to 0, 1, 2
         */
        $this->assertEquals('eeeee', $long->values()[2]);
        $this->assertEquals('d', $short->values()[1]);
    }



    public function testTake(){
        $c = collect(str_split("How are you"));

        $this->assertEquals("How", $c->take(3)->join(""));
        $this->assertEquals("you", $c->take(-3)->join(""));
    }

    public function testTakeUntilAndWhile(){
        $c = collect(str_split("How are you? I'm fine."));
        /**
         * WARNING! We cannot go backwards with it
         */

        $str1 = $c->takeUntil(function (string $el){
            return $el === "?";
        })->join("");

        $str2 = $c->takeWhile(function (string $el){
            return $el !== "?";
        })->join("");

        $this->assertEquals("How are you", $str1);
        $this->assertEquals("How are you", $str2);
    }

}
