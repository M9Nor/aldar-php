<?php

return [
    'config' => [
        // Whether a role with 'roles.update' permission can manage all permissions, even if not listed in their own privileges.
        'can_update_all_permissions' => false
    ],

    /* ENABLE_SUPERPOWERS=false disables Bouncer's ROOT superpowers. Read through config() so
       config:cache cannot silently turn it null (Phase 3). */
    'superpowers' => env('ENABLE_SUPERPOWERS', true),
];
