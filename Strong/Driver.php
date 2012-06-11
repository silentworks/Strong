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
 * @since       1.0.0
 * @version     1.0.0
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

    public function loggedIn() {
        return $this->driver->loggedIn();
    }

    abstract public function login($usernameOrEmail, $password, $remember);

    public function autoLogin() {
        return FALSE;
    }

    public function logout($destroy = FALSE) {
        if ($destroy === TRUE) {
            // Destroy the session completely
            session_destroy();
        } else {
            // Remove the user object from the session
            $_SESSION['auth_user'] = array();
        }

        // Double check
        return !$this->loggedIn();
    }

    public function getUser() {
        if(isset($_SESSION['auth_user']) && !empty($_SESSION['auth_user'])){
            return $_SESSION['auth_user'];
        }
    }

    protected function completeLogin($user) {
        // Store session data
        $_SESSION['auth_user'] = $user;
        return TRUE;
    }
}
