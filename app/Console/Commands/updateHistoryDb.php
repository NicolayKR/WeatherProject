<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\HistoryWeather;
use App\Models\HistoryAllWeather;
use App\Models\HistoryTreeDayWeather;

class updateHistoryDb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update HistoryTable';

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
            $Currtemperat = round($weather["list"][0]["main"]["temp"]);
            $minTemp = round($weather["list"][0]["main"]["temp_min"]);
            $maxTemp = round($weather["list"][0]["main"]["temp_max"]);
            $description = $weather["list"][0]["weather"][0]["description"];
            $pressure = $weather["list"][0]["main"]["pressure"];
            $humidity = $weather["list"][0]["main"]["humidity"];
            $wind  = round($weather["list"][0]["wind"]["speed"]);
            $timeWeather = $weather["list"][0]["dt_txt"];
            $icon = 'http://openweathermap.org/img/w/'.(string)$weather["list"][0]["weather"][0]["icon"].'.png';
            $id_city = $weather["city"]["id"]; 
            $newCity = HistoryAllWeather::create(array(
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
