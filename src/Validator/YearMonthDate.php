<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraints\Date;

#[Attribute]
class YearMonthDate extends Date
{
    public function __construct(...$args)
    {
        $this->message = 'radio_station.first_log_date_invalid_format';

        parent::__construct(...$args);
    }
}
