<?php

namespace Tests\Unit\Collection;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;

class CustomMACROTest extends TestCase
{

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


}
