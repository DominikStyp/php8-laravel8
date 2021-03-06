<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('days-from-birth', function (){
    /** @var \Illuminate\Console\Command $this*/
    if($this->choice("Continue?", ["Yes", "No"], 0) === "No"){
        $this->comment("Goodbye...");
        return;
    }
    $y = $this->ask("Your birth year?");
    $m = $this->ask("Your birth month? (01-12)");
    $d = $this->ask("Your birth day? (01-31)");
    $date1 = \Carbon\Carbon::create($y, $m, $d);
    //$gender = $this->anticipate("Gender?", ["Man", "Woman"]);
    $date2 = \Carbon\Carbon::now();
    $interval = $date2->diff($date1);
    $this->line("Days from your birth: {$interval->days}");
    $this->comment("Weeks from your birth: " . $interval->days / 7);
    $this->comment("Years: {$interval->y}, Months: {$interval->m}, Days: {$interval->d} from your birth");
});

/**
 * Warnings for command shortcuts:
 * 1) can only be ONE letter ("o1" etc. does not work)
 * 2) "-o=234" doesn't work, only "-o 234" works correctly
 * 3) for option to have a value you MUST add "=" at the end
 */
Artisan::command('options-args {arg1} {--o|option1=}', function (){
    /** @var \Illuminate\Console\Command $this*/
    $this->comment("First argument: " . $this->argument("arg1"));

    if(!empty($this->option("option1"))) {
        $this->comment("First option: " . $this->option("option1"));
    }

    $this->comment("All available options are: ". print_r($this->options(), true));
});

/**
 * Using input arrays is possible for arguments and for options:
 *   php artisan input-array 1 2 3 #will make 3 element array as "arr" argument
 *
 * Using option arrays example:
 *   php artisan input-array 1 2 --optionArr=3 --optionArr=4
 * or use alternative with option shortcut
 *   php artisan input-array 1 2 -o 3 -o 4
 */
Artisan::command('input-array
                            {arr* : can be multiple values}
                            {--o|--optionArr=* : can be multiple values that are treated as an array}', function (){
    /**
     * @param array $arr1
     * @param array $arr2
     * @return array
     */
    $prepareRows = function (array $arr1, array $arr2){
        $result = array();
        $cnt = 0;

        while(!empty($arr1) || !empty($arr2)) {
            $result[$cnt][0] = !empty($arr1) ? array_shift($arr1) : "";
            $result[$cnt][1] = !empty($arr2) ? array_shift($arr2) : "";
            $cnt++;
        }

        return $result;
    };

    // if "arr*" is with "*" following always returns an array
    $tblHeaders = ['Arguments', 'Options'];
    $args = [];
    $opts = [];

    foreach($this->argument("arr") as $k => $a) {
        $args[$k] = $a;
    }

    if(!empty($this->option("optionArr"))) {
        foreach($this->option("optionArr") as $k => $a) {
            $opts[$k] = $a;
        }
    }

    $rows = $prepareRows($args, $opts);
    //dd($rows);
    $this->table($tblHeaders, $rows);
});

