<?php

namespace TestBundle\Services\CustomAssert;

use Symfony\Component\Validator\Constraints\LessThan as SfLessThan;
use TestBundle\Entity\Product;

class LessThan extends SfLessThan
{
    const TOO_HIGH_ERROR = '079d7420-2d13-460c-8756-de810eeb37d2';

    protected static $errorNames = array(
        self::TOO_HIGH_ERROR => 'TOO_HIGH_ERROR',
    );

    public $message = 'Value should be less than {{ compared_value }}';

    public function __construct($options)
    {
        // call Grandpa's constructor
        parent::__construct($options);

//        $this->message = $product->getStrProductName();
    }
}
