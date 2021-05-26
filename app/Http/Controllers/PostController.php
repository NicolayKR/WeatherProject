<?php

namespace App\Http\Controllers;
use App\Http\Requests\PostRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Contact;
use Illuminate\Support\Facades\DB;
use App\Models\HistoryWeather;
use App\Models\HistoryAllWeather;
use App\Models\HistoryTreeDayWeather;

class PostController extends Controller
{
    public function getWeather(Request $request){
        $name = $request->query('name'); 
         //Проверка, есть ли в бд
        $result = $this->findСityFromCurr($name);        
        if($result==1) {
            $collection = HistoryWeather::selectRaw('name, curr_temperat,description ,pressure,humidity , wind,icon')
                                        ->selectRaw('round((min_temp+max_temp)/2) as avg_temp')
                                        ->selectRaw('EXTRACT(DOW FROM time_weather) as date')
                                        ->where('name',$name)->get();
         }    
        elseif($result==0) { 
            $url = 'http://api.openweathermap.org/data/2.5/forecast';
            $options= array(
                'q'=> $name,
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
            $this->insertCurrWeather($weather, $name);
            $this->insertIntoAllWeather($weather, $name);
            $this->updateThreeDays();
            $collection = HistoryWeather::selectRaw('name, curr_temperat,min_temp,max_temp,description ,pressure,humidity , wind,icon')
                                        ->selectRaw('round((min_temp+max_temp)/2) as avg_temp')
                                        ->selectRaw('EXTRACT(DOW FROM time_weather) as date')
                                        ->where('name',$name)->get();
        }
        $nextDays = $this->getWeatherForTwoNextDays($name);
        $all = collect($collection)->merge($nextDays);
        return $all;  
    }

    public function findСityFromCurr($name)
        {
            $result = HistoryWeather::select(HistoryWeather::raw('COUNT(*)'))->where('name', $name)->count();         
            return $result;          
        }
    
    public function updateCurrWeather($weather, $id_city){
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
        HistoryWeather::where('id_city', '=', $id_city)->update(array(
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
                                         
     }
    public function insertCurrWeather($weather, $name){
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
        $newCity = HistoryWeather::create(array(
            'name'  => $name,
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
    public function insertIntoAllWeather($weather, $name){
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
            'name'  => $name,
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
    public function getWeatherForTwoNextDays($name) {
        $collection1 = HistoryTreeDayWeather::selectRaw('name')
        ->selectRaw('round(avg(curr_temperat)) as curr_temperat')
        ->selectRaw('round((avg(min_temp)+avg(max_temp))/2) as avg_temp')
        ->selectRaw('max(description) as description')
        ->selectRaw('round(avg(pressure)) as pressure')
        ->selectRaw('round(avg(humidity)) as humidity')
        ->selectRaw('round(avg(wind)) as wind')
        ->selectRaw('max(icon) as icon')
        ->selectRaw('extract(dow from time_weather) as date')
        ->whereRaw("time_weather::date = (NOW()::date+ interval '1 day')::date")
        ->where('name', '=', $name)
        ->groupBy('name')
        ->groupBy('date')->get();
    $collection2 = HistoryTreeDayWeather::selectRaw('name')
        ->selectRaw('round(avg(curr_temperat)) as curr_temperat')
        ->selectRaw('round((avg(min_temp)+avg(max_temp))/2) as avg_temp')
        ->selectRaw('max(description) as description')
        ->selectRaw('round(avg(pressure)) as pressure')
        ->selectRaw('round(avg(humidity)) as humidity')
        ->selectRaw('round(avg(wind)) as wind')
        ->selectRaw('max(icon) as icon')
        ->selectRaw('extract(dow from time_weather) as date')
        ->whereRaw("time_weather::date = (NOW()::date+ interval '2 day')::date")
        ->where('name', '=', $name)
        ->groupBy('name')
        ->groupBy('date')->get();
    $all = collect($collection1)->merge($collection2);
    return $all;  
    }
            
    public function getTimeRange(Request $request)
    {   
        $name = $request->query('name');     
        $collection = HistoryAllWeather::selectRaw('min(time_weather::date) as time_weather')
                    ->where('name','=',$name) ->get();
        return $collection;
    
    }
    public function getChangeWeather(Request $request){
        
        $name = $request->query('name'); 
        $start = $request->query('start'); 
        $end = $request->query('end'); 
        $collection = HistoryAllWeather::select('time_weather','curr_temperat' ,'min_temp','max_temp')
                        ->where('name', $name)
                        ->whereBetween('time_weather',[$start,$end])
                        ->orderBy('time_weather','asc')->get();
        return $collection;

    }
    public function updateThreeDays() {
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
