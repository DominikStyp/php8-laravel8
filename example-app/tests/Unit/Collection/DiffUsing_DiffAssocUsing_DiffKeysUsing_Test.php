<?php

namespace Tests\Unit\Collection;

use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class DiffUsing_DiffAssocUsing_DiffKeysUsing_Test extends TestCase
{

    public function test_diff_using()
    {
        /**
         * Idea:
         * Project was migrated from the $oldUsers to the $newUsers in database
         * 1) every user got the new domain instead of .com => .mail.com
         * 2) id's of the users changed
         * 3) some got deleted, and some got added to the new database
         *    deleted: ['id' => 3, 'email' => 'b@b.com', 'name' => 'John', 'surname' => 'Doe'],
         *
         * How can we quickly check, what is the difference between $oldUsers and $newUsers
         * and which of $oldUsers ARE MISSING in the $newUsers array
         * considering 'id' can now be changed, and domain got changed from '.com' => '.mail.com'
         */
        $oldUsers = collect([
            ['id' => 1, 'email' => 'a@a.com', 'name' => 'John', 'surname' => 'Doe'],
            ['id' => 2, 'email' => 'aaa@a.com', 'name' => 'Johnn', 'surname' => 'Does'],
            ['id' => 3, 'email' => 'b@b.com', 'name' => 'John', 'surname' => 'Doe'],
            ['id' => 4, 'email' => 'aaaa@a.com', 'name' => 'Jane', 'surname' => 'Great'],
            ['id' => 5, 'email' => 'd@d.mail.com', 'name' => 'Ann', 'surname' => 'High'],
            ['id' => 6, 'email' => 'e@e.com', 'name' => 'Jim', 'surname' => 'Slow'],
        ]);

        $newUsers = collect([
            ['id' => 11, 'email' => 'a@a.mail.com', 'name' => 'John', 'surname' => 'Doe'],
            ['id' => 22, 'email' => 'aaa@a.mail.com', 'name' => 'Johnn', 'surname' => 'Does'],

            ['id' => 44, 'email' => 'aaaa@a.mail.com', 'name' => 'Jane', 'surname' => 'Great'],
            ['id' => 55, 'email' => 'd@d.mail.com', 'name' => 'Ann', 'surname' => 'High'],
            ['id' => 66, 'email' => 'e@e.mail.com', 'name' => 'Jim', 'surname' => 'Slow'],

            ['id' => 33, 'email' => 'cc@b.mail.com', 'name' => 'Jason', 'surname' => 'Depp'],
        ]);

        // check the users that are NOT present in the $newUsers
        $notInNewUsers = $oldUsers->diffUsing($newUsers, function($a, $b){
            $regex = "#(?:.com|.mail.com)$#";
            $m1 = preg_replace($regex, ".mail.com", $a['email']);
            $m2 = preg_replace($regex, ".mail.com", $b['email']);
            //dump($m1, $m2, "\n");;
            if($m1 === $m2) {
                return 0;
            }
            return -1;
        });

        $this->assertCount(1, $notInNewUsers);
        $u = $notInNewUsers->first();
        $this->assertEquals("b@b.com", $u['email']);
        $this->assertEquals(3, $u['id']);

        //dump($notInNewUsers);
    }

}
