<?php

namespace TestBundle\Helper;

use Symfony\Component\Validator\Constraint;

class TooFewStocksCountProduct extends Constraint
{
    const IS_BLANK_ERROR = 'c1051bb4-d103-4f74-8988-acbcafc7fdc3';

    protected static $errorNames = array(
        self::IS_BLANK_ERROR => 'IS_BLANK_ERROR',
    );

    public $message = 'This value should not contain less then 5 stocks.';
}
