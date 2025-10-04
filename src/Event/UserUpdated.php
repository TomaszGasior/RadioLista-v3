<?php

namespace App\Event;

use App\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
readonly class UserUpdated
{
    public function __construct(public User $user, public array $changedFields) {}
}
