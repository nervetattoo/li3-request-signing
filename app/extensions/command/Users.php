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
    public $path = '/li3-request-signing';

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
     * Play around with changing the value of `q` to see
     * how the sent signature is unique for each combination of query paramters
     *
     * @param int $userId Id of user to make API call as
     * @param string $q Add a `q` argument to the URL to see it change
     */
    public function consume($userId = false, $q = '')
    {
        if (!$userId) $this->error("Missing userId");

        $user = Model::first($userId);

        $signature = $user->sign(array($this->path, 'q' => $q));

        $this->header("Generating different signatures for different urls");
        $this->columns(array(
            array('Path', 'Username', 'Signature'),
            array('/', $user->username, $user->sign(array('/', 'q' => $q))),
            array($this->path, $user->username, $signature)
        ));

        $service = new Service(array('host' => $this->host));
        $resp = $service->get($this->path, compact('q'), array(
            'type' => 'json',
            'headers' => array(
                'X_USERNAME' => $user->username,
                'X_SIGNATURE' => $signature
            )
        ));
        print_r($resp);
    }
}
