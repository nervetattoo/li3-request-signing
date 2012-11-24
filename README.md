Implementing request signing in Lithium
=======================================

Example code accompanying the [Implementing request signing in Lithium]() blog post.

## Dependencies

* php 5.3+
* php-sqlite

## Files of interest

Check out the [auth config file](https://github.com/nervetattoo/li3-request-signing/blob/master/app/config/bootstrap/auth.php) for the filter that
reads out request signing credentials in a request when `Auth::check` is triggered on a protected resource.

Also see the [users cli command *consume*](https://github.com/nervetattoo/li3-request-signing/blob/master/app/extensions/command/Users.php) that allows you
to test using the request signing API implementation.
