<?php

namespace App\Observers;

use App\Models\Organization;

class OrganizationObserver
{
    public function created(Organization $organization): void
    {
        // also create the limits
    }
}
