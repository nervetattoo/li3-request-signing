<?php

namespace app\models;

class Users extends \lithium\data\Model
{
    protected $_schema = array(
        '_id' => array('type' => 'id'),
        'username' => array('type' => 'string', 'null' => false),
        'password' => array('type' => 'string', 'null' => false),
        'apiKey'  => array('type' => 'string', 'null' => true)
    );

    /**
     * Generate a signature given a payload (POST/GET data from a request)
     * This signature is used to handle authentication
     *
     * @param object $entity
     * @param array $payload
     */
    public static function sign($entity, array $payload)
    {
        ksort($payload);
        $message = '';
        foreach ($payload as $k => $v) {
            if (!is_array($v) && !empty($v))
                $message .= $k.$v;
        }

        return hash_hmac('sha1', $message, $entity->apiKey);
    }
}
