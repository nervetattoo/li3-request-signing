<?php

use lithium\data\Connections;

Connections::add('default', array(
    'type' => 'database',
    'adapter' => 'Sqlite3',
    'database' => LITHIUM_APP_PATH . '/resources/db/sqlite.db'
));
