<?php

namespace app\controllers;
use app\models\Users;
use lithium\security\Auth;

class UsersController extends \lithium\action\Controller {
    public function add() {
        $user = Users::create();
        $user->save($this->request->data, array('safe' => true));
        return array('user' => $user->data());
    }

    public function index() {
        if (Auth::check('default', $this->request)) {
            return array(
                'users' => Users::all()
            );
        }
        return array('error' => 'Not authed');
    }
}
