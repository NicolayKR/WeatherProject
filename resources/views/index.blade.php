<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Weather</title>
        <link rel="stylesheet" href="/css/app.css">
    </head>
<body style="margin: 0 !important; padding: 0 !important;">
    <div id="app">
        <meta name="csrf-token" content="{{ csrf_token() }}">    
        <router-view></router-view> 
    </div>     
    <script src="./js/app.js"></script> 
</body>
</html>
