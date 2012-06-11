<?php
if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50200)
    die('Strong requires PHP 5.2 or higher');

/**
 * Strong Authentication Library
 *
 * User authentication and authorization library
 *
 * @license     MIT Licence
 * @category    Libraries
 * @author      Andrew Smith
 * @link        http://www.silentworks.co.uk
 * @copyright   Copyright (c) 2012, Andrew Smith.
 * @version     1.0.0
 */

class Strong_Auth
{
    // Configuration
    protected $config = array(
        'name' => 'default',
        'driver' => 'PDO',
    );

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

    public static function factory($config = array())
    {
        return new Strong_Auth($config);
    }

    public static function instance($name = 'default')
    {
        return self::$apps[$name];
    }

    public function __construct($config = array())
    {
        // Save the config in gloabal variable
        $this->config = array_merge($this->config, $config);

        // Set the driver class name
        $driver = 'Strong_Driver_' . $this->config['driver'];

        if ( !class_exists($driver)) {
            throw new Exception('Strong is missing driver ' . $this->config['driver'] . ' in ' . get_class($this));
        }

        // Load the driver
        $driver = new $driver($this->config);

        if ( !($driver instanceof Strong_Driver)) {
            throw new Exception('The current Driver ' . $this->config['driver'] . ' is not a instance of ' . get_class($this));
        }

        // Load the driver for access
        $this->driver = $driver;

        //Set app name
        if ( !isset(self::$apps['default']) ) {
            $this->setName('default');
        }
    }

    public function loggedIn()
    {
        return $this->driver->loggedIn();
    }

    public function login($usernameOrEmail, $password, $remember = false)
    {
        if (empty($password)) {
            return false;
        }

        if (is_string($password)) {
            $password = $this->driver->hashPassword($password);
        }

        return $this->driver->login($usernameOrEmail, $password, $remember);
    }

    public function autoLogin()
    {
        return $this->driver->autoLogin();
    }

    public function logout($destroy = false)
    {
        return $this->driver->logout($destroy);
    }

    public function getUser()
    {
        return $this->driver->getUser();
    }

    /**
     * Set Strong application name
     * @param string $name The name of this Strong application
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
        self::$apps[$name] = $this;
    }

    public function getDriver()
    {
        return $this->driver;
    }
}
