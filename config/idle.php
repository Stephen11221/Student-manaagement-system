<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Idle Timeout Configuration
    |--------------------------------------------------------------------------
    |
    | Specify the number of minutes a user can remain idle before being
    | automatically logged out. Session will be warned 1 minute before logout.
    |
    */

    'idle_timeout' => env('SESSION_IDLE_TIMEOUT', 15), // 15 minutes default
    'warning_time' => env('SESSION_WARNING_TIME', 1), // Show warning 1 minute before logout
];
