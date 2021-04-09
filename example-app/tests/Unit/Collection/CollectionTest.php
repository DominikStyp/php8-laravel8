<?php


namespace Tests\Unit\Collection;


use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase{


    public function testCountBy() {
        $c = collect(['alice@gmail.com', 'bob@yahoo.com', 'carlos@gmail.com']);

        $counted = $c->countBy(function ($email) {
            return explode("@", $email)[1];
            //return substr(strrchr($email, "@"), 1);
        });
        $res = $counted->all();

        $this->assertEquals(2, $res['gmail.com']);
        $this->assertEquals(1, $res['yahoo.com']);
    }


    public function testEachSpread() {
        $users = collect([
            ['x@y.com', 'prop111', 'name111'],
            ['y@z.com', 'prop222', 'name222'],
            ['ff@ff.com', '', '']
        ]);
        /**
         * WARNING! String keys are NOT ALLOWED in eachSpread() which gives error:
         * "Error: Cannot unpack array with string keys"
         *
         * WARNING! If arrays don't have the SAME length
         * "ArgumentCountError: Too few arguments to function"
         */
        $users->eachSpread(function (string $email, string $prop = '', string $name = ''){
           $this->assertStringContainsString('@', $email);
            /**
             * WARNING!
             * Why if 2nd value doesn't exist '2' (key) is passed as value ???
             */
           if(!empty($prop)) $this->assertStringContainsString('prop', $prop, "Prop is: $prop");
           if(!empty($name)) $this->assertStringContainsString('name', $name, "Name is: $name");
        });

    }


    /**
     *  @COOL
     */
    public function testFlip() {
        $c = collect([
            'one' => 1,
            'two' => 2,
            2 => 'two',
            3 => 'two',
            4 => 'two'
        ]);
        /**
         * WARNING! if values have DUPLICATES, only the LAST duplicate is gonna be in output collection
          [
            1 => "one"
            2 => "two"
            "two" => 4
          ]

         */
        $f = $c->flip();
        $this->assertCount(3, $f);
    }


    public function testPadNumberWithCollection(){
        $numToPad = 32;
        $paddedNum = collect(str_split($numToPad))->pad(-5, 0)->join("");
        $this->assertEquals("00032", $paddedNum);

        $numToPad = 3332;
        $paddedNum = collect(str_split($numToPad))->pad(-5, 0)->join("");
        $this->assertEquals("03332", $paddedNum);
    }

    public function testTimes(){
        $c = Collection::times(10, function($num){
            return pow($num, 2);
        });
        $this->assertEquals(1 ,$c->get(0));
        $this->assertEquals(4 ,$c->get(1));
        $this->assertEquals(9 ,$c->get(2));
        $this->assertEquals(16 ,$c->get(3));
    }

    public function testUnless(){
        $c = collect([1,2,3]);

        /**
         * WARNING! those functions are NOT loop-like
         * They are invoked only once
         * unless() is invoked when first argument is FALSE
         */
        $c->unless(false, function ($col){
           $col->push(4);
        });

        $this->assertCount(4, $c);
        $this->assertEquals(4, $c->last());
    }

    public function testWhen(){
        $c = collect([1,2,3]);
        /**
         * WARNING! those functions are NOT loop-like
         * They are invoked only once
         * when() is invoked when first argument is TRUE
         */
        $c->when(false, function ($col){
            $col->push(4);
        });
        $this->assertCount(3, $c);
        $this->assertEquals(3, $c->last());
    }

    public function testUnwrap(){
        /**
         * unwrap() returns collection's underlying items
         * in this case this is array
         */
        $c = Collection::unwrap( collect([1,2,3]) );
        $this->assertIsArray($c);
        $this->assertCount(3, $c);
        $str = Collection::unwrap( 'str' );
        $this->assertIsString($str);
    }

    public function testWrap(){
        $c = Collection::wrap(['John Doe']);
        $this->assertInstanceOf(Collection::class, $c);
        $this->assertCount(1, $c);
    }

}
