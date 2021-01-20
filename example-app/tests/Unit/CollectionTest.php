<?php


namespace Tests\Unit;


use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase{

    /**
     * @var \Illuminate\Support\Collection
     */
    private $c;
    protected function setUp(): void {
        $this->collection = collect([
            [
                'id' => 1,
                'name' => 'user1',
                'surname' => 'ccc',
            ],
            [
                'id' => 11,
                'name' => 'user1',
                'surname' => 'aaa',
            ],
            [
                'id' => 2,
                'name' => 'user2',
                'email' => 'user2@user2.example.com',
                'surname' => 'bbb',
            ],
            [
                'id' => 3,
                'name' => 'user3',
                'surname' => 'zzz',
            ],
            [
                'id' => 4,
                'name' => 'user4',
                'surname' => 'ttt',
            ],
        ]);
        parent::setUp();
    }

    public function testFilter() {
        $withEmails = $this->collection->filter(function ($el) {
            return !empty($el['email']);
        });
        $this->assertCount(1, $withEmails);
        /**
         * WARNING! after filter keys are preserved
         */
        $this->assertEquals('user2@user2.example.com', $withEmails->get(2)['email']);
    }

    public function testSort() {
        $sorted = $this->collection->sort(function ($el1, $el2) {
            return $el1['surname'] <=> $el2['surname'] ; // less than = -1, equals = 0, greater than = 1
        });
        /**
         * WARNING! keys are changed, re-maped
         */
        $this->assertEquals('zzz', $sorted->last()['surname']);
    }

    public function testMap() {
        $mapped = $this->collection->map(function ($el, $index){
           $el['surname'] = Str::ucfirst($el['surname']);
           return $el;
        });
        $this->assertEquals("Ttt", $mapped[4]['surname']);
        /**
         * WARNING! Original collection is intact, surname is only changed in returned collection
         */
        $this->assertNotEquals("Ttt", $this->collection[4]['surname']);
    }

    public function testCollapse() {
        $collapsed = $this->collection->collapse();
        $this->assertCount(4, $collapsed);
        /**
         * WARNING! if keys of sub-arrays are the same, last element overrides all previous keys
         */
        $this->assertEquals($collapsed['id'], 4);
        $this->assertEquals($collapsed['email'], "user2@user2.example.com");
    }

    public function testCombine() {
        $combined = collect(['id', 'name', 'surname', 'email'])
            ->combine([222, 'name222', 'surname222', 'user222@example.com']);
        $this->assertEquals('surname222', $combined['surname']);
    }

    public function testReplaceRegexMacro() {
        Collection::macro('findRegex', function ($regex) {
            return $this->map(function ($value) use ($regex) {
                if(!preg_match($regex, $value, $matches)){
                    return "";
                }
                return $matches[0];
            });
        });
        $emailRegex = '#[A-Z0-9][A-Z0-9._%+-]{0,63}@(?:(?=[A-Z0-9-]{1,63}\.)[A-Z0-9]+(?:-[A-Z0-9]+)*\.){1,8}[A-Z]{2,63}#si';
        $foundEmails = collect([
            "something one@email.com sd9fs8d09fs80",
            "23423kl4jj som4x.ss3.k@some3324.comain.com.info @ some@thing anything",
            "23423kl4jj @some3324.comain.com @ some@thing anything"
        ])->findRegex($emailRegex);
        $this->assertEquals("one@email.com", $foundEmails[0]);
        $this->assertEquals("som4x.ss3.k@some3324.comain.com.info", $foundEmails[1]);
        $this->assertEquals("", $foundEmails[2]);
    }

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

    public function testContains() {
        $c = collect([1,2,3,4,6,7]);
        $this->assertFalse($c->contains(5));
        $res = $c->contains(function ($val, $key){
            return $val > 7;
        });
        $this->assertFalse($res);
    }

    public function testCountBy() {
        $c = collect(['alice@gmail.com', 'bob@yahoo.com', 'carlos@gmail.com']);
        $counted = $c->countBy(function ($email) {
            return explode("@", $email)[1];
            //return substr(strrchr($email, "@"), 1);
        });
        $res = $counted->all();
        //dd($res);
        $this->assertEquals(2, $res['gmail.com']);
        $this->assertEquals(1, $res['yahoo.com']);
    }

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

    public function testDuplicates() {
        $c = collect(['a', 'a', 'b', 'c', 'd', 'd', 'd']);
        // WARNING! This does not COUNT duplicates
        // it just gives  keys where duplicates are in ORIGINAL collection
        $duplicates = $c->duplicates();
        $this->assertCount(3, $duplicates);
        $this->assertEquals('a', $duplicates[1]);
        $this->assertEquals('d', $duplicates[5]);
        $this->assertEquals('d', $duplicates[6]);
        // duplicates with COUNT values now returns counted duplicates as expected
        /*
            array:2 [
              "a" => 1
              "d" => 2
            ]
         */
        $countedDuplicates = $c->duplicates()->countBy();
        $this->assertEquals(1,$countedDuplicates['a']);
        $this->assertEquals(2,$countedDuplicates['d']);
        $this->assertCount(2, $countedDuplicates);
    }

    public function testDuplicatesByKey() {
        $c = collect([
            ['a' => 'a'],
            ['a' => 'a'],
            ['b' => 'bb'],
            ['d' => 'dd', 'a' => 'aa'],
            ['x' => 'z', 'd' => 'ddd'],
            ['y' => 'yy', 'z' => 'zzz'],
        ]);
        $duplicates = $c->duplicates('a');
        /**
         * [
             1 => "a"
             4 => null
             5 => null
            ]

         */
        $this->assertCount(3, $duplicates);
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

    public function testEvery() {
        $strs = collect([
            'aa', 'bb', '', 'c', 'dd', '1', null
        ]);
        // checks if ALL elements match the condition
        $check = $strs->every(function ($el){
            return strlen($el) >= 2;
        });
        $this->assertFalse($check);
    }

    public function testExcept(){
        $users = collect([
            ['id' => 1, 'name' => 'name1', 'email' => 'email1@s.com', 'password' => '123'],
            ['id' => 2, 'name' => 'name2', 'email' => 'email2@x.com', 'password' => '456']
        ]);
        $noIds = $users->map(function($el){
            return collect($el)->except(['id', 'password']);
        });
        $this->assertTrue(empty($noIds[0]['password']));
        $this->assertTrue(empty($noIds[1]['id']));
    }

    public function testFilter1(){
        $res = collect([1,2,3,4,5])->filter(function($el){ return $el % 2 === 0; });
        $this->assertCount(2, $res);
    }

    public function testFirstWhere(){
        $person = collect([
            ['name' => "Joe", 'id' => 1],
            ['name' => "Donald", 'id' => 2],
            ['name' => "Barrack", 'id' => 3]
        ])->firstWhere('name', 'Barrack');
        $this->assertEquals('Barrack', $person['name']);
    }

    public function testFlatMap(){
        $configs = collect([
            ['app_key' => '123_gggg', 'user_key' => '456-hhh'],
            ['user_email' => 'u@ux.com', 'some_other_key' => 'xxxx']
        ]);
        $flat = $configs->flatMap(function($values){ // only values of all keys are passed here
            return array_map('strtoupper', $values);
        });
        $this->assertEquals('123_GGGG', $flat['app_key']);
    }

    public function testFlatten(){
        $c = collect([
            'one' => 1,
            'two' => [
                'three' => [
                    'four'
                ]
            ],
            'x' => 'four'
        ]);
        $f = $c->flatten();
        /*
         * [
                0 => 1
                1 => "four"
                2 => "four"
            ]
         */
        $this->assertCount(3, $f);
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


    public function testForget_PASS_BY_REFERENCE() {
        $c = collect([
            'x' => 1,
            'y' => 2
        ]);
        // WARNING: modifies initial c by reference
        $c->forget('x');
        $this->assertCount(1, $c);
        $this->assertTrue(empty($c['x']));
    }

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

    /**
     * @COOL !!!
     */
    public function testGroupBy() {
        $c = collect([
            ['id' => 1, 'name' => 'name1', 'role' => 'user'],
            ['id' => 2, 'name' => 'name2', 'role' => 'user'],
            ['id' => 3, 'name' => 'name3', 'role' => 'admin'],
            ['id' => 4, 'name' => 'name4', 'role' => 'user'],
            ['id' => 5, 'name' => 'name4', 'role' => 'mod'],
        ]);
        $grouped = $c->groupBy('role');
        // assert only users
        $this->assertTrue( $grouped['user']->every(function($e){ return $e['role'] === 'user';  }) );
        // assert only admins
        $this->assertTrue( $grouped['admin']->every(function($e){ return $e['role'] === 'admin';  }) );
    }

    public function testHas() {
        /**
         * WARNING! If key does not have value, has() returns false
         */
        $this->assertFalse(
            collect(['x', 'y', 'z'])->has('z')
        );
        $this->assertTrue(
            collect(['x' => 1, 'y' => 2, 'z' => 3])->has('z')
        );
    }

    public function testIntersect() {
        $roles = ['admin', 'mod', 'subsriber', 'new', 'inactive'];
        $cnt = collect(['user'])->intersect($roles)->count();
        $this->assertEquals(0, $cnt);
        $commonRoles = collect(['admin','some_role', 'other_role', 'new'])
            ->intersect($roles);
        $this->assertTrue( $commonRoles->contains('admin') );
        $this->assertTrue( $commonRoles->contains('new') );
        $this->assertEquals(2, $commonRoles->count());
    }

    public function testIntersectByKeys() {
        $c = collect([
            'x' => 'xx',
            'y' => 'yy',
            'z' => 'zz'
        ]);
        $intersect = $c->intersectByKeys([
            'a' => 'aaa',
            'x' => '123',
            'z' => '444',
        ]);
        /**
           [
            "x" => "xx"
            "z" => "zz"
           ]
         */
        $this->assertTrue( $intersect->has('x') );
        $this->assertTrue( $intersect->has('z') );
    }

    public function testJoinWithLast() {
        $c = ['a', 'b', 'c', 'd', 'e'];
        $str1 = collect($c)->join(',', ' AND ');
        $this->assertEquals('a,b,c,d AND e', $str1);
    }

    public function testMapVsTransform(){
        //-------- MAP ----------
        $c = collect(['a', 'b', 'c', 'd']);
        $res = $c->map(function($el){ return "${el}_${el}"; } );
        // new collection is modified
        $this->assertEquals('a_a', $res->first());
        // ...but original stays the same
        $this->assertNotEquals('aa', $c->first());
        // ------- TRANSFORM ---------
        $c->transform(function($el){ return "${el}_modified"; });
        $this->assertEquals('a_modified', $c->first());
    }

    public function testMapSpread(){
        $c = collect([
            ['id' => 1, 'name' => 'name1', 'role' => 'admin'],
            ['id' => 2, 'name' => 'name2', 'role' => 'user'],
            ['id' => 3, 'name' => 'name3', 'role' => 'admin'],
        ]);
        $mapped = $c
            ->map(function($el){ // first we must get rid of string keys (not allowed in mapSpread)
                return array_values($el);
            })
            ->mapSpread(function ($id, $name, $role){ //then we spread the elements to the variables
            return ['id' => $id, 'name' => $name, 'role' => $role, 'name_role' => "{$name}_{$role}"];
        });
        $this->assertCount(3, $mapped);
        $this->assertEquals('name1_admin', $mapped[0]['name_role']);
        $this->assertEquals('name3_admin', $mapped[2]['name_role']);
    }

    public function testMapToGroups(){
        $c = collect([
            ['id' => 1, 'name' => 'name1', 'role' => 'admin'],
            ['id' => 2, 'name' => 'name2', 'role' => 'user'],
            ['id' => 3, 'name' => 'name3', 'role' => 'admin'],
            ['id' => 4, 'name' => 'name3', 'role' => 'admin'],
            ['id' => 5, 'name' => 'name3', 'role' => 'mod'],
        ]);
        $grouped = $c->mapToGroups(function ($item){
            return [ $item['role']  => $item['id'] ];
        });
        $this->assertCount(3, $grouped['admin']);
        $this->assertCount(1, $grouped['mod']);
        $this->assertCount(3, $grouped);
    }

    public function testMax(){
        $c = collect([
            ['cnt' => 99],
            ['cnt' => 10],
            ['cnt' => '100'],
        ]);
        $this->assertEquals(100, $c->max('cnt'));
    }

    public function testMergeRecursive(){
        $c = collect([
            'name' => 'user1',
            'roles' => ['user']
        ]);
        $toMerge = collect([
            'roles' => ['admin', 'mod']
        ]);
        $merged = $c->mergeRecursive($toMerge);
        $this->assertCount(3, $merged['roles']);
        $this->assertTrue(collect($merged['roles'])->contains('admin'));
        $this->assertTrue(collect($merged['roles'])->contains('user'));
    }

    public function testOnlyAndExcept(){
        $c = collect(
            [
                'id' => 1,
                'name' => 'name1',
                'email' => 'e@mail.com',
                'role' => 'admin',
                'password' => 'admin123'
            ],
        );
        // ------- only
        $onlyName = $c->only('name');
        $this->assertCount(1, $onlyName);
        $this->assertEquals('name1', $onlyName['name']);
        $this->assertTrue(empty($onlyName['role']));
        //------------ except
        $exceptPassword = $c->except('password');
        $this->assertCount(4, $exceptPassword);
        $this->assertEquals('name1', $exceptPassword['name']);
        $this->assertEquals('admin', $exceptPassword['role']);
        $this->assertTrue(empty($exceptPassword['password']));
    }

    public function testPadNumberWithCollection(){
        $numToPad = 32;
        $paddedNum = collect(str_split($numToPad))->pad(-5, 0)->join("");
        $this->assertEquals("00032", $paddedNum);

        $numToPad = 3332;
        $paddedNum = collect(str_split($numToPad))->pad(-5, 0)->join("");
        $this->assertEquals("03332", $paddedNum);
    }

    /**
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
        $this->assertEquals('d', $short->values()[1]);    }

}
