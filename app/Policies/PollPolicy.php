<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\Poll;
use Illuminate\Auth\Access\HandlesAuthorization;

class PollPolicy
{
    use HandlesAuthorization;

    public function viewAny(Company $company): bool
    {
        return true;
    }

    public function view(Company $company, Poll $poll): bool
    {
        return $company->id === $poll->company_id;
    }

    public function create(Company $company): bool
    {
        return true;
    }

    public function update(Company $company, Poll $poll): bool
    {
        return $company->id === $poll->company_id;
    }

    public function delete(Company $company, Poll $poll): bool
    {
        return $company->id === $poll->company_id;
    }

    // Additional methods for custom actions
    public function publish(Company $company, Poll $poll): bool
    {
        return $company->id === $poll->company_id;
    }

    public function close(Company $company, Poll $poll): bool
    {
        return $company->id === $poll->company_id;
    }
}
