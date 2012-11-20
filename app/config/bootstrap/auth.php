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

Auth::applyFilter('check', function($self, $params, $chain)
{
    $result = $chain->next($self, $params, $chain);

    if (isset($params['credentials']) && $params['credentials']) {

        $request = $params['credentials'];
        $signature = $request->env('HTTP_X_SIGNATURE');
        $username = $request->env('HTTP_X_USERNAME');

        if ($username && $signature) {
            // Attempt finding a user by this username
            $user = Users::first(array('conditions' => compact('username')));
            if (!$user) {
                throw new \Exception("Invalid user $username");
            }

            /**
             * GET and POST/PUT passes payload differently
             * Also doing rewriting means that the `url` GET param is added by Lithium
             */
            $signData = $request->is('get')
                ? array_diff_key($request->query, array('url' => 'sodoff'))
                : $request->data;
            array_unshift($signData, $request->env('REQUEST_URI'));

            if ($signature === $user->sign($signData)) {
                /**
                 * We have a successfully signed request, set user as authed
                 * `$params['name']` is the name of the auth used
                 */
                return Auth::set($params['name'], $user->data());
            }
            else {
                throw new \Exception("Signature match failed in signed request");
            }
        }
    }
    return $result;
});
