<?php

namespace Tests\Unit;

use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use PHPUnit\Framework\TestCase;

class LazyCollectionsTest extends TestCase
{
    private $file = 'testLog.txt';
    private $filePath;

    protected function setUp(): void {
        parent::setUp();

        $this->filePath = dirname(__FILE__).'/../../storage/logs/'.$this->file;

        if(!file_exists($this->filePath)){
            $this->createTestLogFile();
        }
    }

    private function createTestLogFile(){
        foreach(range(1, 10000) as $num){

            $this->assertIsInt(
                file_put_contents(
                    $this->filePath,
                    $num . ": " . str_repeat('abc', 30),
                    FILE_APPEND
                )
            );
        }
    }

    private function getLazyFileReader() : LazyCollection {
        return LazyCollection::make(function(){

            $handle = fopen($this->filePath, 'r');

            while ( ($line = fgets($handle)) !== false) {
                yield $line;
            }
        });
    }

    private function getRegularFileReader() : Collection {
        return collect(file($this->filePath));
    }


    public function testLazyCollection()
    {
       $memory = memory_get_usage();

       $c = $this->getLazyFileReader();
       $c->each(function ($el){
               $this->assertStringContainsString('abc', $el);
           });

       $memoryDelta = memory_get_usage() - $memory;
       $this->assertLessThan(1000, $memoryDelta);
    }

    public function testRegularCollection()
    {
        $memory = memory_get_usage();

        $c = $this->getRegularFileReader();
        $c->each(function ($el){
            $this->assertStringContainsString('abc', $el);
        });

        $memoryDelta = memory_get_usage() - $memory;
        $this->assertGreaterThan(900000, $memoryDelta);
    }
}
