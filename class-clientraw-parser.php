<?php
if (!defined('ABSPATH')) {
    exit;
}

class ClientrawParser {
    private $clientraw_url;

    public function __construct($clientraw_url) {
        $this->clientraw_url = $clientraw_url;
    }

    public function getWeatherData() {
        return $this->fetchWeatherData();
    }

    public function formatWeatherUpdate() {
        $data = $this->getWeatherData();
        if (!$data) {
            return 'Weather data unavailable';
        }

        return "🌡️ Temperature: {$data['temperature']}°C\n💧 Humidity: {$data['humidity']}%\n💨 Wind: {$data['wind_speed']} km/h {$data['wind_direction']}\n";
    }

    private function fetchWeatherData() {
        if (empty($this->clientraw_url)) {
            return false;
        }

        $response = wp_remote_get($this->clientraw_url, [
            'timeout' => 30,
            'sslverify' => false,
            'user-agent' => 'WeatherSocialPoster/1.0'
        ]);

        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            return false;
        }

        $content = wp_remote_retrieve_body($response);
        if (empty($content)) {
            return false;
        }

        $data = explode(' ', trim($content));
        if (count($data) < 6) {
            return false;
        }

        return [
            'temperature' => isset($data[4]) ? round(floatval($data[4]), 1) : 0,
            'humidity' => isset($data[5]) ? round(floatval($data[5])) : 0,
            'wind_speed' => isset($data[1]) ? round(floatval($data[1])) : 0,
            'wind_direction' => isset($data[3]) ? $this->getWindDirection($data[3]) : 'N/A'
        ];
    }

    private function getWindDirection($degrees) {
        $degrees = floatval($degrees);
        $directions = ['N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE', 'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW'];
        $index = round($degrees / 22.5) % 16;
        return $directions[$index];
    }
}
?>