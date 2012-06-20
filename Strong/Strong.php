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
        'provider' => 'PDO',
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
     * @var Strong_Provider
     */
    protected $provider;

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

        // Set the provider class name
        $provider = 'Strong_Provider_' . $this->config['provider'];
        
        if ( !class_exists($provider)) {
            throw new Exception('Strong is missing provider ' . $this->config['provider'] . ' in ' . get_class($this));
        }

        // Load the provider
        $provider = new $provider($this->config);

        if ( !($provider instanceof Strong_Provider)) {
            throw new Exception('The current Provider ' . $this->config['provider'] . ' is not a instance of ' . get_class($this));
        }

        // Load the provider for access
        $this->provider = $provider;

        //Set app name
        if ( !isset(self::$apps[$this->config['name']]) ) {
            $this->setName($this->config['name']);
        }
    }

    /**
     * User login check based on provider
     * 
     * @return booleon
     */
    public function loggedIn()
    {
        return $this->provider->loggedIn();
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
            $password = $this->provider->hashPassword($password);
        }

        return $this->provider->login($usernameOrEmail, $password, $remember);
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
        return $this->provider->logout($destroy);
    }

    /**
     * Get the users details stored in Session
     * 
     * @return array
     */
    public function getUser()
    {
        return $this->provider->getUser();
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
     * Get the Provider class being used specifically
     * 
     * @return Strong_Provider
     */
    public function getProvider()
    {
        return $this->provider;
    }
}
