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
 * @copyright   Copyright (c) 2009, Andrew Smith.
 * @since       0.1.0
 * @version     0.5.0
 */
class Strong_Driver_Activerecord extends Strong_Driver
{
    public function logged_in() {
        return (isset($_SESSION['auth_user']) && !empty($_SESSION['auth_user']));
    }

    public function login($username_or_email, $password, $remember) {
        if(! is_object($username_or_email)) {
            $user = User::find_by_username_or_email($username_or_email, $username_or_email);
        }

        if(($user->email === $username_or_email || $user->username === $username_or_email) && $user->password === $password) {
            return $this->complete_login($user);
        }

        return FALSE;
    }

    protected function complete_login($user) {
        $users = User::find($user->id);
        $users->logins = $user->logins + 1;
        $users->last_login = time();
        $users->save();

        $user_info = array(
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'logged_in' => TRUE
        );

        return parent::complete_login($user_info);
    }
}
