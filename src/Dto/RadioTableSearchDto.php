<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class RadioTableSearchDto
{
    // Search term equal to "*" causes MySQL error.
    // https://bugs.mysql.com/bug.php?id=78485
    #[Assert\NotBlank]
    #[Assert\NotEqualTo('*')]
    public ?string $searchTerm = null;
}
