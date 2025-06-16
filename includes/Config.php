<?php
namespace EcoPowerTracker;

class Config {
    private static $instance = null;
    private $settings = [];

    public static function getInstance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->settings = [
            'version' => defined('ECOPOWER_TRACKER_VERSION') ? ECOPOWER_TRACKER_VERSION : 'unknown',
            'co2_conversion_factor' => get_option('ecopower_tracker_co2_factor', 0.001),
            'plant_types' => class_exists('EcoPower_Tracker_Utils') ? EcoPower_Tracker_Utils::get_plant_types() : [],
            'cache_duration' => apply_filters('ecopower_tracker_cache_duration', 3600),
        ];
    }

    public function get($key, $default = null) {
        return isset($this->settings[$key]) ? $this->settings[$key] : $default;
    }
} 