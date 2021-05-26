<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Http\Controllers\PostController;
use App\Console\Commands\updateDb;
use App\Console\Commands\updateHistoryDb;
use App\Console\Commands\updateThreeDaysDb;
use App\Models\HistoryWeather;
use App\Models\HistoryAllWeather;
use App\Models\HistoryTreeDayWeather;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\updateDb',
        'App\Console\Commands\updateHistoryDb',
        'App\Console\Commands\updateThreeDaysDb'
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {  
    
        $schedule->command('minute:update')->everyThirtyMinutes(); //Thirty
        $schedule->command('update:history')->everyThreeHours();
        $schedule->command('update:threedays')->daily()->timezone("Europe/Moscow");
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
