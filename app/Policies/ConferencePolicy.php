<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\Conference;

class ConferencePolicy
{
    public function viewAny(Company $company): bool
    {
        return true;
    }

    public function view(Company $company, Conference $conference): bool
    {
        return (int) $company->id === (int) $conference->company_id;
    }

    public function create(Company $company): bool
    {
        return true;
    }

    public function update(Company $company, Conference $conference): bool
    {
        return (int) $company->id === (int) $conference->company_id;
    }

    public function delete(Company $company, Conference $conference): bool
    {
        return (int) $company->id === (int) $conference->company_id;
    }
}
