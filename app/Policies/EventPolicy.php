<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\Event;

class EventPolicy
{
    public function viewAny(Company $company): bool
    {
        return true;
    }

    public function view(Company $company, Event $event): bool
    {
        return (int) $event->company_id === (int) $company->id;
    }

    public function create(Company $company): bool
    {
        return !$company->is_suspended;
    }

    public function update(Company $company, Event $event): bool
    {
        return (int) $event->company_id === (int) $company->id && !$company->is_suspended;
    }

    public function delete(Company $company, Event $event): bool
    {
        return (int) $event->company_id === (int) $company->id;
    }

    public function publish(Company $company, Event $event): bool
    {
        return (int) $event->company_id === (int) $company->id
            && !$company->is_suspended
            && $event->isDraft();
    }
}
