<?php

namespace TestBundle\Helper;

class ProductError
{
    /**
     * @var integer
     */
    private $errCount;

    /**
     * @var string
     */
    private $fieldName;

    /**
     * @var string
     */
    private $message;

    public function __construct($errCount, $message, $fieldName = 'undefined')
    {
        $this->setErrCount($errCount);
        $this->setFieldName($fieldName);
        $this->setMessage($message);
    }

    /**
     * @return int
     */
    public function getErrCount()
    {
        return $this->errCount;
    }

    /**
     * @param int $errCount
     */
    public function setErrCount($errCount)
    {
        $this->errCount = $errCount;
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * @param string $fieldName
     */
    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

}