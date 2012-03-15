<?php
if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50200)
    die('Strong requires PHP 5.2 or higher');

/**
 * Strong Authentication Library
 *
 * User authentication and authorization library
 * note: Some functionality were taken from KohanaPHP Auth library
 *
 * @license     MIT Licence
 * @category    Libraries
 * @author      Andrew Smith
 * @link        http://www.silentworks.co.uk
 * @copyright   Copyright (c) 2012, Andrew Smith.
 * @since       1.0.0
 * @version     1.0.0
 */

class Strong_Auth
{
    // Slim Log instance
    protected $log;

    // Configuration
    protected $config;

    /**
     * @const string
     */
    const VERSION = '1.0.0';

    /**
     * @var array[Strong]
     */
    protected static $apps = array();

    // Drivers
    protected $driver;

    public static function factory($config = array()) {
        return new Strong($config);
    }

    public static function instance($name = 'default') {
        return isset(self::$apps[(string)$name]) ? self::$apps[(string)$name] : null;
    }

    public function __construct($config = array()) {
        // Slim Instance Name
        $instance_name = isset($config['slim.instance']) ? $config['slim.instance'] : 'default';

        // Load Instance of Slim log
        $this->log = Slim::getInstance($instance_name)->getLog();

        // Save the config in gloabal variable
        $this->config = $config;

        // Set the driver class name
        $driver = 'Strong_Driver_' . $config['driver'];

        if (!class_exists($driver)) {
            $this->log->error('Strong is missing driver' . $config['driver'] . ' in ' . get_class($this));
            Slim::handleErrors(4, 'Strong is missing driver ' . $config['driver'] . ' in ' . get_class($this), __FILE__, 73);
        }

        // Load the driver
        $driver = new $driver($config);

        if (!($driver instanceof Strong_Driver)) {
            $this->log->error('The current Driver ' . $config['driver'] . ' is not a instance of ' . get_class($this));
        }

        // Load the driver for access
        $this->driver = $driver;

        //Set app name
        if ( !isset(self::$apps['default']) ) {
            $this->setName('default');
        }

        $this->log->debug('Strong Library loaded');
    }

    public function logged_in() {
        return $this->driver->logged_in();
    }

    public function login($username_or_email, $password, $remember = FALSE) {
        if (empty($password)) {
            return FALSE;
        }

        if (is_string($password)) {
            $password = $this->hash_password($password);
        }

        return $this->driver->login($username_or_email, $password, $remember);
    }

    public function auto_login() {
        return $this->driver->auto_login();
    }

    public function logout($destroy = FALSE) {
        return $this->driver->logout($destroy);
    }

    public function get_user() {
        return $this->driver->get_user();
    }

    /**
     * Set Strong application name
     * @param string $name The name of this Strong application
     * @return void
     */
    public function setName( $name ) {
        $this->name = $name;
        self::$apps[$name] = $this;
    }

    /**
     * Get Strong application name
     * @return string|null
     */
    public function getName() {
        return $this->name;
    }

    public function hash_password($plain_text) {
        $site_key = isset($this->config['site_key']) ? $this->config['site_key'] : '0ca232f94c7bbe5251c5811f1d7df9d474a5576c6698dec5e';
        $nonce = isset($this->config['nonce']) ? $this->config['nonce'] : 'd5b81f75d61b52555beb9cc61491ba8746b91cbfb9a2a9cdc';

        return hash_hmac('sha512', $plain_text . $nonce, $site_key);
    }
}
