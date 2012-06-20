<?php
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
 * @since       0.1.0
 * @version     0.5.0
 */
abstract class Strong_Provider
{
    /**
     * @var array
     */
    protected $config;

    /**
     * Initalize the provider and start session if
     * one is not already started.
     * 
     * @param array $config 
     */
    public function __construct(array $config) {
        // Load Session
        if(session_id() === "") {
            session_start();
        }

        // Save the config in gloabal variable
        $this->config = $config;
    }

    /**
     * User login check based on provider
     * 
     * @return booleon
     */
    abstract public function loggedIn();

    /**
     * To authenticate user based on username or email
     * and password
     * 
     * @param string $usernameOrEmail 
     * @param string $password 
     * @return booleon
     */
    abstract public function login($usernameOrEmail, $password);

    /**
     * Log user out by deleting session key values or
     * deleting the session completely
     * 
     * @param booleon $destroy 
     * @return booleon
     */
    public function logout($destroy = false) {
        if ($destroy === true) {
            // Destroy the session completely
            session_destroy();
        } else {
            // Remove the user object from the session
            $_SESSION['auth_user'] = array();
        }

        // Double check
        return !$this->loggedIn();
    }

    /**
     * Get the users details stored in Session
     * 
     * @return array
     */
    public function getUser() {
        if(isset($_SESSION['auth_user']) && !empty($_SESSION['auth_user'])){
            return $_SESSION['auth_user'];
        }
    }

    /**
     * Login and store user details in Session
     * 
     * @param array $user 
     * @return booleon
     */
    protected function completeLogin($user) {
        // Store session data
        $_SESSION['auth_user'] = $user;
        return true;
    }
}
