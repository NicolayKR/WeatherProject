<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\HistoryWeather;
use App\Models\HistoryAllWeather;
use App\Models\HistoryTreeDayWeather;

class updateThreeDaysDb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:threedays';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
        HistoryTreeDayWeather::query()->truncate();
        HistoryTreeDayWeather::select('alter sequence history_tree_day_weather_id_seq restart');
        $collection = HistoryWeather::select('name')->get()->values()->all();
        foreach ($collection as $city) {
            $url = 'http://api.openweathermap.org/data/2.5/forecast';
            $options= array(
                'q'=> $city->name,
                'APPID' => '552c8e284d0aef519e863788b862f2f5',
                'units'=>'metric',
                'lang'=>'en'
            );
            $ch= curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch, CURLOPT_URL,$url.'?'.http_build_query($options));       
            $response = curl_exec($ch); 
            curl_close($ch);
            $weather = json_decode($response,true); 
            date_default_timezone_set("Europe/Moscow");
            for ($i = 0; $i < 24; $i++) {
                $Currtemperat = round($weather["list"][$i]["main"]["temp"]);
                $id_city = $weather["city"]["id"]; 
                $minTemp = round($weather["list"][$i]["main"]["temp_min"]);
                $maxTemp = round($weather["list"][$i]["main"]["temp_max"]);
                $description = $weather["list"][$i]["weather"][0]["description"];
                $pressure = $weather["list"][$i]["main"]["pressure"];
                $humidity = $weather["list"][$i]["main"]["humidity"];
                $wind  = round($weather["list"][$i]["wind"]["speed"]);
                $timeWeather = $weather["list"][$i]["dt_txt"];
                $icon = 'http://openweathermap.org/img/w/'.(string)$weather["list"][$i]["weather"][0]["icon"].'.png';
                $newCity = HistoryTreeDayWeather::create(array(
                    'name'  => $city->name,
                    'id_city' =>  $id_city,
                    'curr_temperat'   => $Currtemperat,
                    'min_temp' =>  $minTemp,
                    'max_temp' =>  $maxTemp,
                    'description' =>  $description,
                    'pressure' =>  $pressure,
                    'humidity' =>  $humidity,
                    'wind' =>  $wind,
                    'time_weather' =>  $timeWeather,
                    'icon' =>  $icon
                ));
                $newCity->save(); 
                }
            } 
    }
    
}
