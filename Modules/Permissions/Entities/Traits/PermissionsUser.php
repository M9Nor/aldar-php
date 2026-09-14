<?php

namespace Modules\Permissions\Entities\Traits;

use Illuminate\Database\Eloquent\Builder;
use Silber\Bouncer\Database\HasRolesAndAbilities;
use Illuminate\Support\Str;

/**
 *
 */
trait PermissionsUser
{
    use HasRolesAndAbilities;
}
