<?php

namespace App\Policies;

use App\Models\Quotation;
use App\Models\User;

class QuotationPolicy
{
    public function view(User $user, Quotation $quotation): bool
    {
        return $user->id === $quotation->user_id || $user->hasRole('admin') || $user->hasRole('super-admin');
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function delete(User $user, Quotation $quotation): bool
    {
        return $user->id === $quotation->user_id || $user->hasRole('admin');
    }
}
