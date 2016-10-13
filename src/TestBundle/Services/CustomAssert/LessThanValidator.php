<?php

namespace TestBundle\Services\CustomAssert;

use Symfony\Component\Validator\Constraints\AbstractComparisonValidator;

/**
 * Validates values are less than the previous (<).
 *
 * @author Daniel Holmes <daniel@danielholmes.org>
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class LessThanValidator extends AbstractComparisonValidator
{
    /**
     * {@inheritdoc}
     */
    protected function compareValues($value1, $value2)
    {
        return $value1 < $value2;
    }

    /**
     * {@inheritdoc}
     */
    protected function getErrorCode()
    {
        return LessThan::TOO_HIGH_ERROR;
    }
}
