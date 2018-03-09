<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\City;

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
})->describe('Display an inspiring quote');

Artisan::command('import_cities_old {file}', function ($file) {
    $myfile = fopen($file, "r") or die("Unable to open file!");
    $this->info('Reading  ' . $file);
    $in_file = fread($myfile, filesize($file));
    fclose($myfile);
    $this->info('Make json file to array.');
    $json_file = json_decode($in_file);

    $this->info('Writing cities from ' . $file . ' to database table cities.');

    $total = count($json_file);

    if ($this->confirm('File contains total ' . $total . ' records, do you wish to continue?')) {
        foreach ($json_file as $key => $city) {
            
            $new_city = new City();
            $new_city->name = $city->name;
            $new_city->country = $city->country;
            $new_city->latitude = $city->coord->lat;
            $new_city->longitude = $city->coord->lon;
            
            $new_city->save();
            
            $done = $key + 1;
            $perc = floor(($done / $total) * 100);
            $left = 100 - $perc;
            $write = sprintf("\033[0G\033[2K[%'={$perc}s>%-{$left}s] - $perc%% - $done/$total", "", "");
            fwrite(STDERR, $write);
        }

        $this->info(PHP_EOL . $total . ' cities have been imported to table cities from ' . $file);
    }

})->describe('Import cities to database from json source file');