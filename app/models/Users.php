<?php

namespace app\models;

class Users extends \lithium\data\Model {
    protected $_schema = array(
        '_id' => array('type' => 'id'),
        'username' => array('type' => 'string', 'null' => false),
        'password' => array('type' => 'string', 'null' => false),
        'apiKey'  => array('type' => 'string', 'null' => true)
    );

    /**
     * Verify signature
     *
     * @param object $entity
     * @param array $payload
     */
    public static function sign($entity, array $payload)
    {
        ksort($payload);
        $message = '';
        foreach ($payload as $k => $v) {
            if (!is_array($v))
                $message .= $k.$v;
        }

        return hash_hmac('sha1', $message, $entity->apiKey);
    }
}
