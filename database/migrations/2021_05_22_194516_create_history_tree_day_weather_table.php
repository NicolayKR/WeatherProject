<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryTreeDayWeatherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history_tree_day_weather', function (Blueprint $table) {
            $table->id();
            $table->string('name',50);
            $table->integer('id_city');
            $table->double('curr_temperat',3,2);//Текущая температура
            $table->double('min_temp',2,2);
            $table->double('max_temp',2,2);
            $table->string('description',100);
            $table->integer('pressure');//Давление
            $table->integer('humidity');//Осадки
            $table->double('wind',2,2);//Скорость ветра
            $table->dateTime('time_weather');
            $table->string('icon',100);//иконка погоды
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('history_tree_day_weather');
    }
}
