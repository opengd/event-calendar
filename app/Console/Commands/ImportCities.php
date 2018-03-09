<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportCities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import_cities {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import cities to database from json source file';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $input_file_name = $this->argument('file');

        $myfile = fopen($input_file_name, "r") or die("Unable to open file!");
        $this->info('Reading  ' . $input_file_name);
        $in_file = fread($myfile, filesize($input_file_name));
        fclose($myfile);
        $this->info('Make json file to array.');
        $json_file = json_decode($in_file);

        $this->info('Writing cities from ' . $input_file_name . ' to database table cities.');

        $total = count($json_file);

        if ($this->confirm('File contains total ' . $total . ' records, do you wish to continue?')) {
            foreach ($json_file as $key => $city) {
                /*
                $new_city = new City();
                $new_city->name = $city->name;
                $new_city->country = $city->country;
                $new_city->latitude = $city->coord->lat;
                $new_city->longitude = $city->coord->lon;
                
                $new_city->save();
                */
                $this->progress($key + 1, $total);

            }

            $this->info(PHP_EOL . $total . ' cities have been imported to table cities from ' . $input_file_name);
        }
    }

    /**
     * Show current progress
     * 
     * @var int
     * @var int
     */
    protected function progress($done, $total) {
        $perc = floor(($done / $total) * 100);
        $left = 100 - $perc;
        $write = sprintf("\033[0G\033[2K[%'={$perc}s>%-{$left}s] - $perc%% - $done/$total", "", "");
        fwrite(STDERR, $write);
    }
}
