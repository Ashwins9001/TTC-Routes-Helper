<?php

/*
    Used to define API codes
*/

    //Connect to DB itself required constants 
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASSWORD', '');
    define('DB_NAME', 'users_ttc_app');

    //Define return codes
    define('USER_CREATED', 101);
    define('USER_EXISTS', 102);
    define('USER_FAILURE', 103);

    define('USER_AUTHENTICATED', 201);
    define('USER_NOT_FOUND', 202);
    define('USER_NOT_AUTHENTICATED', 203);

    define('BALANCE_UPDATE', 301);
    define('BALANCE_FAILURE', 302);

