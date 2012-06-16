<?php
if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50200)
    die('Strong requires PHP 5.2 or higher');

spl_autoload_register(array('Strong', 'autoload'));

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

class Strong
{
    /**
     * @var array
     */
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

    /**
     * @var Strong_Driver
     */
    protected $driver;

    /**
     * Autoloader to get all Strong related classes
     * 
     * @param string $class
     */
    public static function autoload($class)
    {
        if (0 !== strpos($class, 'Strong')) {
            return;
        }
        $file = dirname(__FILE__) . '/' . str_replace('_', DIRECTORY_SEPARATOR, substr($class,7)) . '.php';
        if (is_file($file)) {
            require $file;
        }
    }

    /**
     * Factory method to call Strong and initalize
     * 
     * @param array $config 
     * @return Strong
     */
    public static function factory($config = array())
    {
        return new Strong($config);
    }

    /**
     * Get and existing instance of Strong using a
     * static method
     * 
     * @param string $name 
     * @return Strong
     */
    public static function instance($name = 'default')
    {
        return self::$apps[$name];
    }

    /**
     * Instantiate Strong and provide config for your settings
     * 
     * @param array $config 
     */
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
        if ( !isset(self::$apps[$this->config['name']]) ) {
            $this->setName($this->config['name']);
        }
    }

    /**
     * User login check based on driver
     * 
     * @return booleon
     */
    public function loggedIn()
    {
        return $this->driver->loggedIn();
    }

    public static function protect($name = 'default')
    {
        if ( ! Strong::instance($name)->loggedIn()) {
            return false;
        }
        return true;
    }

    /**
     * To authenticate user based on username or email
     * and password
     * 
     * @param string $usernameOrEmail 
     * @param string $password 
     * @return booleon
     */
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

    /**
     * Log user out by deleting session key values or
     * deleting the session completely
     * 
     * @param booleon $destroy 
     * @return booleon
     */
    public function logout($destroy = false)
    {
        return $this->driver->logout($destroy);
    }

    /**
     * Get the users details stored in Session
     * 
     * @return array
     */
    public function getUser()
    {
        return $this->driver->getUser();
    }

    /**
     * Set Strong application name
     * 
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
        self::$apps[$name] = $this;
    }

    /**
     * Get the Driver class being used specifically
     * 
     * @return Strong_Driver
     */
    public function getDriver()
    {
        return $this->driver;
    }
}
