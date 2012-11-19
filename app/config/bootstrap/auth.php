<?php

use lithium\security\Auth;
use app\models\Users;

Auth::config(array(
	'default' => array(
		'adapter' => 'Form',
		'model' => 'Users',
		'fields' => array('username', 'password')
	)
));

Auth::applyFilter('check', function($self, $params, $chain) {
    $result = $chain->next($self, $params, $chain);

    if (isset($params['credentials']) && $params['credentials']) {

        $request = $params['credentials'];
        $signature = $request->env('HTTP_X_SIGNATURE');
        $username = $request->env('HTTP_X_USERNAME');

        if ($username && $signature) {
            $user = Users::first(array('conditions' => compact('username')));
            if ($request->is('get'))
                $signData = array_diff_key($request->query, array('url' => 'sodoff'));
            else
                $signData = $request->data;

            if ($signature === $user->sign($signData)) {
                // We have a successfully signed request, set user as authed
                return Auth::set($params['name'], $user->data());
            }
            else {
                throw new \Exception("Signature match failed in signed request");
            }
        }
    }
    return $result;
});
