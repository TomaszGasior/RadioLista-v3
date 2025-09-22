<?php

namespace App\Dto;

use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\Validator\Constraints as Assert;

#[Exclude]
class RadioTableSearchDto
{
    // Search term equal to "*" causes MySQL error.
    // https://bugs.mysql.com/bug.php?id=78485
    #[Assert\NotBlank]
    #[Assert\NotEqualTo('*')]
    public ?string $searchTerm = null;
}
