<?php

lithium\data\Connections::add('default', array(
    'type' => 'database',
    'adapter' => 'Sqlite3',
    'database' => LITHIUM_APP_PATH . '/resources/db/sqlite.db'
));
