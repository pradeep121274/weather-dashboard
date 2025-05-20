<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
class WeatherController extends Controller
{
     public function index()
    {
        return view('weather.index');
    }

    public function fetchWeather(Request $request)
{
    $city = $request->input('city');
    $apiKey = env('OPENWEATHER_API_KEY');

    if (!$city || !$apiKey) {
        return response()->json(['error' => 'City or API key is missing.'], 400);
    }

    $currentResponse = Http::get("https://api.openweathermap.org/data/2.5/weather", [
        'q' => $city,
        'appid' => $apiKey,
        'units' => 'metric'
    ]);
    $forecastResponse = Http::get("https://api.openweathermap.org/data/2.5/forecast", [
        'q' => $city,
        'appid' => $apiKey,
        'units' => 'metric'
    ]);

    if ($currentResponse->failed() || $forecastResponse->failed()) {
        return response()->json([
            'error' => 'Could not retrieve weather data.',
            'debug' => [
                'current_status' => $currentResponse->status(),
                'forecast_status' => $forecastResponse->status(),
                'message' => $currentResponse->json()
            ]
        ], 404);
    }

    return response()->json([
        'current' => $currentResponse->json(),
        'forecast' => $forecastResponse->json()
    ]);
}

}
