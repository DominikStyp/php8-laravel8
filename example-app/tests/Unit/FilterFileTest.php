<?php

namespace Tests\Unit;

use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class FilterFileTest extends TestCase
{
    private $file = 'fileToFilter.txt';
    private $filePath;

    protected function setUp(): void {
        parent::setUp();
        $this->filePath = dirname(__FILE__).'/../../storage/logs/'.$this->file;
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testFilterFile()
    {
        $this->assertFileExists($this->filePath);
        $names = [];
        $prices = [];
        $content = collect(file($this->filePath));
        $content
            ->chunk(7)
            ->each(function ($chunk) use (&$names, &$prices) {
                $resetKeys = $chunk->values();
                if($resetKeys->count() === 7) {
                    $names[] = trim($resetKeys[0]);
                    $prices[] = trim($resetKeys[2]);
                } else {
                    throw new \Exception("\nChunk is not complete!\n". json_encode($chunk));
                }
            });
        $zipped = collect($names)->zip($prices);
        echo $this->presentZippedCollection($zipped);
    }

    private function presentZippedCollection(Collection $c): string {
        return $c->reduce(function($carry, $arr){
            return $carry . $arr[0] . ": " . $arr[1] . "\n";
        }, "");
    }
}
