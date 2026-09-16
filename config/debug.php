<?php

return [
    /*
     | Debug output inside application code. Read through config() so config:cache keeps it,
     | unlike env(), which returns null once the config is cached (Phase 3).
     */
    'enabled' => env('APP_DEBUG', false),
];
