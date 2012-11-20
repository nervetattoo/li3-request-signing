<?php

namespace app\controllers;
use app\models\Users;
use lithium\security\Auth;

class UsersController extends \lithium\action\Controller
{
    /**
     * Override _init to set rendering type to json by default
     */
    public function _init()
    {
        $this->_render['type'] = 'json';
        parent::_init();
    }

    /**
     * Add a new User, needed for testing
     *
     * @return array
     */
    public function add()
    {
        $user = Users::create();
        $user->save($this->request->data, array('safe' => true));
        return array('user' => $user->data());
    }

    /**
     * List all users, if accessor is authed
     *
     * @return array
     */
    public function index()
    {
        if (Auth::check('default', $this->request)) {
            return array(
                'users' => Users::all()
            );
        }
        return array('error' => 'Not authed');
    }
}
