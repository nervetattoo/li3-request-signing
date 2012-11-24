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

/**
 * Filter calls to `Auth::check()` and if the `HTTP_X_USERNAME' and `HTTP_X_SIGNATURE`
 * headers are sent use this to either verify the request or throw a no-access exception
 *
 * We use **url** + either query string *OR* POST fields for GET and POST respectively
 * to generate a new signature on our side, if this signature matches the one that is passed
 * we consider the request as a valid request for that username
 */
Auth::applyFilter('check', function($self, $params, $chain)
{
    $result = $chain->next($self, $params, $chain);

    /**
     * `Auth::check` is called in two context
     * 1. With a `Request` object to sign a user in
     * 2. With no arguments to check if the current user is signed in
     *
     * We only need to check in the first case.
     */
    if (isset($params['credentials']) && $params['credentials']) {

        $request = $params['credentials'];
        $signature = $request->env('HTTP_X_SIGNATURE');
        $username = $request->env('HTTP_X_USERNAME');

        if ($username && $signature) {
            /**
             * Find the username the request is attempted to be made for
             * The user object is needed because it holds the secret key
             * we need to be able to regenerate the signature
             */
            $user = Users::first(array('conditions' => compact('username')));
            if (!$user) {
                throw new \Exception("Invalid user $username");
            }

            /**
             * GET and POST/PUT passes payload differently, this either `query` or `data`
             * Also doing rewriting can mean that the `url` GET param is added
             */
            $signData = $request->is('get')
                ? array_diff_key($request->query, array('url' => 'sodoff'))
                : $request->data;

            /**
             * Prepend the request path so all requests with no data
             * does not get the same key
             */
            array_unshift($signData, $request->env('base'));

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
