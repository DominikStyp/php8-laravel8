<?php
namespace App\SpecificCollection;

use Illuminate\Support\Collection;

class NullableStringCollection extends Collection {

    public function nullizeEmptyString(){
        $this->transform(function($subEl){
            if(is_string($subEl) && empty($subEl)){
               return null;
            }
        });
    }

}
