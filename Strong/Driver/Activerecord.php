<?php

/**
 * Strong Authentication Library
 *
 * User authentication and authorization library
 *
 * @license     MIT Licence
 * @category    Driver
 * @author      Andrew Smith
 * @link        http://www.silentworks.co.uk
 * @copyright   Copyright (c) 2012, Andrew Smith.
 * @since       1.0.0
 * @version     1.0.0
 */
class Strong_Driver_Activerecord extends Strong_Driver
{
    public function loggedIn() {
        return (isset($_SESSION['auth_user']) && !empty($_SESSION['auth_user']));
    }

    public function login($usernameOrEmail, $password, $remember) {
        if(! is_object($usernameOrEmail)) {
            $user = User::find_by_username_or_email($usernameOrEmail, $usernameOrEmail);
        }

        if(($user->email === $usernameOrEmail || $user->username === $usernameOrEmail) && $user->password === $password) {
            return $this->completeLogin($user);
        }

        return FALSE;
    }

    protected function completeLogin($user) {
        $users = User::find($user->id);
        $users->logins = $user->logins + 1;
        $users->last_login = time();
        $users->save();

        $userInfo = array(
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'logged_in' => TRUE
        );

        return parent::completeLogin($userInfo);
    }
}
