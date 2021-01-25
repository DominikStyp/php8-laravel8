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
    private $collection;
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

    /**
     * filter() expects TRUE to keep the element
     */
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

    /**
     * reject() expects TRUE to remove the element
     */
    public function testReject(){
        $withEmails = $this->collection->reject(function ($el) {
            return empty($el['email']);
        });
        $this->assertCount(1, $withEmails);
        /**
         * WARNING! after filter keys are preserved
         */
        $this->assertEquals('user2@user2.example.com', $withEmails->get(2)['email']);
    }

    public function testSkip(){
        $c = collect(range(1,10));
        // skips first 5 elements
        $c1 = $c->skip(5);
        $this->assertCount(5, $c1);
        $this->assertEquals(6, $c1->first());
    }

    public function testSkipUntil(){
        $c = collect(range(1,10));
        // skips elements UNTIL callback returns TRUE
        $c1 = $c->skipUntil(function($el) {
            return $el > 5;
        });
        $this->assertCount(5, $c1);
        $this->assertEquals(6, $c1->first());
    }

    // same as skipUntil() but expects reversed boolean condition
    public function testSkipWhile(){
        $c = collect(range(1,10));
        // skips elements WHILE callback returns TRUE
        $c1 = $c->skipWhile(function($el) {
            return $el <= 5;
        });
        $this->assertCount(5, $c1);
        $this->assertEquals(6, $c1->first());
    }

    // sorting
    public function testSort() {
        $sorted = $this->collection->sort(function ($el1, $el2) {
            return $el1['surname'] <=> $el2['surname'] ; // less than = -1, equals = 0, greater than = 1
        });
        /**
         * WARNING! keys are changed, re-maped
         */
        $this->assertEquals('zzz', $sorted->last()['surname']);
    }

    // mapping
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

    // filter by value
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

    // filter keys
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
    // filter by value
    public function testFilter1(){
        $res = collect([1,2,3,4,5])->filter(function($el){ return $el % 2 === 0; });
        $this->assertCount(2, $res);
    }

    // filter by value equals
    public function testFirstWhere(){
        $person = collect([
            ['name' => "Joe", 'id' => 1],
            ['name' => "Donald", 'id' => 2],
            ['name' => "Barrack", 'id' => 3]
        ])->firstWhere('name', 'Barrack');
        $this->assertEquals('Barrack', $person['name']);
    }

    // filter by value quals
    public function testWhere(){
        $c = collect([
            ['name' => "Joe", 'id' => 1],
            ['name' => "Donald", 'id' => 2],
            ['name' => "Barrack", 'id' => 3],
            ['name' => "Joe", 'id' => 4],
        ]);
        $filtered = $c->where('name', 'Joe');
        $this->assertCount(2, $filtered);
    }

    // filter by value in range
    public function testWhereBetweenAndNotBetween(){
        $c = collect([
            ['name' => "Joe", 'id' => 1],
            ['name' => "Donald", 'id' => 2],
            ['name' => "Barrack", 'id' => 3],
            ['name' => "Joe", 'id' => 4],
            ['name' => "Stan", 'id' => 5],
        ]);

        $filtered = $c->whereBetween('id', [2,4]);
        $this->assertCount(3, $filtered);
        $this->assertEquals("Donald", $filtered->first()["name"]);
        $this->assertEquals("Joe", $filtered->last()["name"]);

        $filteredInverse = $c->whereNotBetween('id', [2,4]);
        $this->assertCount(2, $filteredInverse);
        $this->assertEquals("Joe", $filteredInverse->first()["name"]);
        $this->assertEquals("Stan", $filteredInverse->last()["name"]);
    }


    public function testWhereInAndNotIn(){
        $c = collect([
            ['name' => "Joe", 'id' => 1],
            ['name' => "Donald", 'id' => 2],
            ['name' => "Barrack", 'id' => 3],
            ['name' => "Joe1", 'id' => 4],
            ['name' => "Stan", 'id' => 5],
        ]);

        $filtered = $c->whereIn('id', [2,5]);
        $this->assertCount(2, $filtered);
        $this->assertEquals("Donald", $filtered->first()["name"]);
        $this->assertEquals("Stan", $filtered->last()["name"]);

        $filtered = $c->whereNotIn('id', [2,5]);
        $this->assertCount(3, $filtered);
        $this->assertEquals("Joe", $filtered->first()["name"]);
        $this->assertEquals("Joe1", $filtered->last()["name"]);
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

    // search by value
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

    // search by common part
    // merge/join collection
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

    // search by common keys
    // merge/join collection
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

    // merge/join collection
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
        $this->assertEquals('d', $short->values()[1]);
    }

    public function testPipe(){
        $c = collect(range(1,20));
        $ret = $c
          ->pipe(function (Collection $c){
            return $c->transform(function ($el){
                return $el*2;
            });
        })->pipe(function(Collection $c){
                list($lower, $higher) = $c->partition(function($el){
                    return $el <= 20;
                });
                return $higher;
        })->pipe(function(Collection $c){
            return $c->reverse();
        });
        $this->assertEquals(40, $ret->first());
        $this->assertEquals(22, $ret->last());
        $this->assertCount(10, $ret);
    }

    public function testPluck(){
        $c = collect([
            ['id' => 1, 'name' => 'name1', 'role' => 'admin'],
            ['id' => 2, 'name' => 'name2', 'role' => 'user'],
            ['id' => 33, 'name' => 'name3', 'role' => 'admin'],
        ]);
        $idsOnly = $c->pluck('id');
        $this->assertEquals(1, $idsOnly[0]);
        $this->assertEquals(2, $idsOnly[1]);
        $this->assertEquals(33, $idsOnly[2]);
    }

    /**
     * Removing, appending, prepending elements
     */
    public function testPopPrependPush(){
        /**
         * WARNING pop() prepend() and push() operate on the ORIGINAL collection by reference
         */
        $c = collect([1,2,3]);
        // pop
        $el = $c->pop();
        $this->assertEquals(3, $el);
        $this->assertCount(2, $c);
        // prepend
        $c->prepend(5);
        $this->assertEquals(5, $c->first());
        $this->assertCount(3, $c);
        // push
        $c->push(6);
        $this->assertEquals(6, $c->last());
        $this->assertCount(4, $c);
    }

    public function testShift(){
        $c = collect([10,2,3]);
        $shifted = $c->shift();
        $this->assertEquals(10, $shifted);
        $this->assertCount(2, $c);
    }

    public function testPull(){
        $c = collect(['id' => 1, 'name' => 'name1', 'role' => 'admin']);
        // pulls element by key and removes it from collection
        $name = $c->pull('name');
        $this->assertEquals('name1', $name);
        $this->assertCount(2, $c);
    }

    public function testPut(){
        $c = collect(['id' => 1, 'name' => 'name1', 'role' => 'admin']);
        // places element at certain key
        $c->put('role', 'mod');
        $this->assertCount(3, $c);
        $this->assertEquals('mod', $c['role']);
    }

    public function testReduce(){
        $c = collect([1,2,4,8,16,32,64,128]);
        // reduce() iterate on collection,
        // remembers returned state to $carry
        // returns final $carry result in the end
        $sum = $c->reduce(function ($carry, $item){
            return $carry + $item;
        });
        $this->assertEquals(255, $sum);
        $sameCollection = $c->reduce(function ($carry, $item){
            return collect($carry)->push($item);
        });
        $this->assertEquals($c->count(), $sameCollection->count());
        $this->assertEquals($c->get(1), $sameCollection->get(1));
        $this->assertEquals($c->last(), $sameCollection->last());
    }

    public function testReduceWithKeys(){
        $collection = collect([
            'usd' => 1400,
            'gbp' => 1200,
            'eur' => 1000,
        ]);

        $ratio = [
            'usd' => 1,
            'gbp' => 1.37,
            'eur' => 1.22,
        ];
        $balanceSum = $collection->reduceWithKeys(function ($carry, $value, $key) use ($ratio) {
            return $carry + ($value * $ratio[$key]);
        });
        $this->assertEquals(4264, $balanceSum);
    }

    public function testReplace(){
        /**
         * replace() method will also overwrite items in the collection that have matching numeric keys
         */
        $c = collect(['one', 'two' => 'two', 'three']);
        $replaced = $c->replace([
            'two' => 'two_1',
            2 => 'three_1'
        ]);
        $this->assertEquals('one', $replaced[0]);
        $this->assertEquals('two_1', $replaced['two']);
        $this->assertEquals('three_1', $replaced[2]);
    }

    public function testReplaceRecursive(){
        $config1 = [
            'db' => [
                'user' => 'u1',
                'pass' => 'p1',
                'modules' => [
                    'm1', 'm2'
                ]
            ]
        ];
        $config2 = [
            'db' => [
                'pass' => '',
                'modules' => [
                    'm3'
                ]
            ]
        ];
        $c = collect($config1);
        /**
         * WARNING! array elements are replaces according to their keys: 0, 1..etc.
         * Arrays are not FULLY REPLACED like you may expect
         */
        $replaced = $c->replaceRecursive($config2);
        $this->assertEquals('u1', $replaced['db']['user']);
        $this->assertEquals('', $replaced['db']['pass']);
        $this->assertCount(2, $replaced['db']['modules']);
        $this->assertEquals('m3', $replaced['db']['modules'][0]);
        $this->assertEquals('m2', $replaced['db']['modules'][1]);
    }

    // searching by value
    public function testSearch(){
        $c = collect(['one', 'nine', 'ten', 'eleven']);
        // searches key by value
        $this->assertEquals(2, $c->search('ten'));
    }

    public function testSlice(){
        $c = collect(range(1,10));
        // offset number of elements are skipped
        // length number of elements are included
        $sliced = $c->slice(4, 3)->values();
        $this->assertEquals(5, $sliced[0]);
        $this->assertEquals(6, $sliced[1]);
        $this->assertEquals(7, $sliced[2]);
        $this->assertCount(3, $sliced);
    }

    public function testSortAndSortDescSimple(){
        $c = collect([7,10,3,4,2]);
        // sort
        $this->assertEquals(10, $c->sort()->last());
        $this->assertEquals(2, $c->sort()->first());
        // sortDesc
        $this->assertEquals(10, $c->sortDesc()->first());
        $this->assertEquals(2, $c->sortDesc()->last());
    }

    public function testSortAdvanced(){
        $c = collect([
            ['id' => 5, 'name' => 'name5'],
            ['id' => 2, 'name' => 'name2'],
            ['id' => 7, 'name' => 'name7'],
            ['id' => 3, 'name' => 'name3']
        ]);
        // callback is passed to usort()
        // https://www.php.net/manual/en/function.usort.php
        $sorted = $c->sort(function ($a, $b){
            /**
             *  echo 1 <=> 2, PHP_EOL; // -1
                echo 1 <=> 1, PHP_EOL; // 0
                echo 2 <=> 1, PHP_EOL; // 1
             */
             return $a['id'] <=> $b['id'];
        })->values(); // reset keys on the end
        $this->assertEquals(2, $sorted[0]['id']);
        $this->assertEquals(3, $sorted[1]['id']);
        $this->assertEquals(7, $sorted->last()['id']);
    }

    public function testSortByAndSortByDesc(){
        $c = collect([
            ['id' => 5, 'name' => 'name5'],
            ['id' => 2, 'name' => 'name2'],
            ['id' => 7, 'name' => 'name7'],
            ['id' => 3, 'name' => 'name3']
        ]);
        $sorted = $c->sortBy('id', SORT_NATURAL)->values();
        $this->assertEquals(2, $sorted[0]['id']);
        $this->assertEquals(3, $sorted[1]['id']);
        $this->assertEquals(7, $sorted->last()['id']);
        $desc = $c->sortByDesc('id', SORT_NATURAL)->values();
        $this->assertEquals(7, $desc[0]['id']);
        $this->assertEquals(5, $desc[1]['id']);
        $this->assertEquals(2, $desc->last()['id']);
    }

    public function testSortKeys(){
        $c = collect([
            'c' => 3,
            'a' => 1,
            'b' => 2,
            'e' => 5,
            'd' => 4,

        ]);
        // sortKeys
        $sorted = $c->sortKeys();
        $this->assertEquals(1, $sorted->first());
        $this->assertEquals('c', $sorted->keys()[2]);
        $this->assertEquals(5, $sorted->last());
        // sortKeysDesc
        $desc = $c->sortKeysDesc();
        $this->assertEquals(5, $desc->first());
        $this->assertEquals('d', $desc->keys()[1]);
        $this->assertEquals(1, $desc->last());
    }

    public function testSplice(){
        $c = collect([1, 2, 3, 4, 5]);
        $chunk = $c->splice(2, 1);
        $this->assertCount(1, $chunk);
        $this->assertEquals(3, $chunk->first());
        // splice also REMOVES the chunk from original collection
        $this->assertCount(4, $c);
        $this->assertFalse($c->search(3)); // 3 is removed
        $this->assertTrue(!empty($c->search(4))); // 4 is not removed
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

    public function testTake(){
        $c = collect(str_split("How are you"));
        $this->assertEquals("How", $c->take(3)->join(""));
        $this->assertEquals("you", $c->take(-3)->join(""));
        $c = collect(str_split("How are you"));
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

    public function testTap(){
        $c = collect([2, 4, 3, 1, 10, 5]);
        $sorted = $c->sort()
           ->tap(function (Collection $col){
               $this->assertEquals(1, $col->first());
               $this->assertEquals(10, $col->last());
               $col->shift(); //does not affect original collection here
               $col->reject(function ($el){
                   return true;
               });
           })
            ->slice(1)
            ->tap(function ($col){
                $this->assertEquals(2, $col->first());
                $this->assertEquals(10, $col->last());
            });
        $this->assertEquals(2, $sorted->first());
        $this->assertEquals(10, $sorted->last());
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

    public function testUnion(){
        $c = collect([
            1 => ['a'],
            2 => ['b']
        ]);
        $union = $c->union([
            1 => ['b'],
            3 => ['c']
        ]);
        /**
         * WARNING! union prefers ORIGINAL values and not overrides them if they exist
         */
        $this->assertEquals($union[1][0], 'a');
        $this->assertEquals($union[3][0], 'c') ;
    }

    public function testUnique(){
        $c = collect([1,1,1,2,2,3,4,5]);
        $this->assertCount(5, $c->unique());
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

    public function testWhereNullAndNotNull(){
        $c = collect([
            ['id' => 1, 'name' => "Joe"],
            ['id' => 2, 'name' => "Donald"],
            ['id' => 3, 'name' => null],
            ['id' => 4, 'name' => "George"],
            ['id' => 5, 'name' => null],
        ]);
        $this->assertCount(3, $c->whereNotNull('name'));
        $this->assertCount(2, $c->whereNull('name'));
    }

    public function testWrap(){
        $c = Collection::wrap(['John Doe']);
        $this->assertInstanceOf(Collection::class, $c);
        $this->assertCount(1, $c);
    }

    // merge/join collection
    public function testZip(){
        $c = collect(['one', 'two', 'three']);
        $a = [1, 2];

        /**
         * Difference between crossJoin and this is merging only FIRST combination
         * so 'one', 1  ... without 'one', 2 etc.
         */
        $zipped = $c->zip($a);
        $this->assertEquals('one', $zipped[0][0]);
        $this->assertEquals(1, $zipped[0][1]);

        $this->assertEquals('two', $zipped[1][0]);
        $this->assertEquals(2, $zipped[1][1]);

        $this->assertEquals('three', $zipped[2][0]);
        $this->assertEquals(null, $zipped[2][1]);
    }





}
