<?php

namespace app\extensions\command;

use \lithium\core\Environment;
use lithium\net\http\Service;
use app\models\Users as Model;

class Users extends \lithium\console\Command
{
    /**
     * Environment to use
     * @var string
     */
    public $env = 'production';

    /**
     * Host to connect to when using API
     * @var string
     */
    public $host = 'localhost';

    /**
     * Path in API requests
     * @var string
     */
    public $path = '/li3-request-signing/';

    protected function _init()
    {
        parent::_init();
        Environment::set($this->env);
    }

    /**
     * List users directly from Db
     */
    public function index()
    {
        $users = Model::all();
        $fields = array(array_keys($users->schema()->fields()));
        $users = $users->map(function($model)
        {
            return array_values($model->data());
        }, array('collect' => false));
        $columns = array_merge($fields, $users);
        $this->columns($columns);
    }

    /**
     * List users through API
     */
    public function consume($userId = false)
    {
        if (!$userId) $this->error("Missing userId");

        $user = Model::first($userId);

        $service = new Service(array('host' => $this->host));
        $resp = $service->get($this->path, array(), array(
            'type' => 'json',
            'headers' => array(
                'X_USERNAME' => $user->username,
                'X_SIGNATURE' => $user->sign(array($this->path))
            )
        ));
        print_r($resp);
    }
}
