<?php

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
 * @since       0.1.0
 * @version     0.5.0
 */
abstract class Strong_Driver
{
    // Configuration
    protected $config;

    public function __construct(array $config) {
        // Load Session
        if(session_id() === "") {
            session_start();
        }

        // Save the config in gloabal variable
        $this->config = $config;
    }

    public function logged_in() {
        return $this->driver->logged_in();
    }

    abstract public function login($username_or_email, $password, $remember);

    public function auto_login() {
        return FALSE;
    }

    public function logout($destroy = FALSE) {
        if ($destroy === TRUE) {
            // Destroy the session completely
            session_destroy();
        }
        else {
            // Remove the user object from the session
            $_SESSION['auth_user'] = array();
        }

        // Double check
        return !$this->logged_in();
    }

    public function get_user() {
        if(isset($_SESSION['auth_user']) && !empty($_SESSION['auth_user'])){
            return $_SESSION['auth_user'];
        }
    }

    protected function complete_login($user) {
        // Store session data
        $_SESSION['auth_user'] = $user;
        return TRUE;
    }
}
