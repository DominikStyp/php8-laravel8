<?php

namespace Tests\Unit\Collection;

use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class OnlyAsPluckMultipleValuesTest extends TestCase
{

    public function test_regular_pluck()
    {
        $c = collect([
            ['id' => 1, 'email' => 'a@a.com', 'name' => 'aaa', 'surname' => 'aaa_surname'],
            ['id' => 2, 'email' => 'b@b.com', 'name' => 'bbb', 'surname' => 'bbb_surname'],
            ['id' => 3, 'email' => 'c@c.com', 'name' => 'ccc', 'surname' => 'ccc_surname'],
        ]);

        // with pluck we can get one value, but we cannot get multiple values
        $this->assertEquals('b@b.com', $c->pluck('email')->get(1) );
    }


    private function initMacro()
    {
        /**
         * If user provides the custom $returnKey this will represent the value under this key in the input collection
         *
         * Example: ['id' => 1, 'email' => 'a@a.com', 'name' => 'aaa', 'surname' => 'aaa_surname']
         *
         * in this case if $returnKey === 'surname' the output key value will be 'aaa_surname'
         */
        Collection::macro('pluckMultipleValues', function(array $keys, string $returnKey = "") {
            if (empty($returnKey)) {
                return $this->mapWithKeys(function ($item, $key) use ($keys) {
                    return [
                        $key => collect($item)->only($keys)
                    ];
                });
            }

            return $this->mapWithKeys(function ($item) use ($keys, $returnKey) {
                return [
                    $item[$returnKey] => collect($item)->only($keys)
                ];
            });

        });
    }

    public function test_only_as_pluck_multiple_values()
    {
        $this->initMacro();

        $c = collect([
            ['id' => 1, 'email' => 'a@a.com', 'name' => 'aaa', 'surname' => 'aaa_surname'],
            ['id' => 2, 'email' => 'b@b.com', 'name' => 'bbb', 'surname' => 'bbb_surname'],
            ['id' => 3, 'email' => 'c@c.com', 'name' => 'ccc', 'surname' => 'ccc_surname'],
        ]);

        $res = $c->pluckMultipleValues(['email', 'name']);

        $this->assertEquals('a@a.com', $res->get(0)['email']);
        $this->assertEquals('c@c.com', $res->get(2)['email']);
        $this->assertEquals('ccc', $res->get(2)['name']);

        $this->assertTrue( empty($res->get(2)['surname']) ); // we didn't list it in argument array
    }

    public function test_only_as_pluck_multiple_values_with_custom_key()
    {
        $this->initMacro();

        $c = collect([
            ['id' => 1, 'email' => 'a@a.com', 'name' => 'aaa', 'surname' => 'aaa_surname'],
            ['id' => 2, 'email' => 'b@b.com', 'name' => 'bbb', 'surname' => 'bbb_surname'],
            ['id' => 3, 'email' => 'c@c.com', 'name' => 'ccc', 'surname' => 'ccc_surname'],
        ]);

        $res = $c->pluckMultipleValues(['email', 'name'], 'surname');

        $this->assertEquals('a@a.com', $res->get('aaa_surname')['email']);
        $this->assertEquals('c@c.com', $res->get('ccc_surname')['email']);
        $this->assertEquals('ccc', $res->get('ccc_surname')['name']);

        $this->assertTrue( empty($res->get('ccc_surname')['surname']) ); // we didn't list it in argument array
    }

}
