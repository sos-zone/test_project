<?php

namespace TestBundle\Helper;

class ProductError
{
    /**
     * @var integer
     */
    private $rowNum;

    /**
     * @var string
     */
    private $message;

    public function __construct($rowNum, $message)
    {
        $this->setRowNum($rowNum);
        $this->setMessage($message);
    }

    /**
     * @return int
     */
    public function getRowNum()
    {
        return $this->rowNum;
    }

    /**
     * @param int $rowNum
     */
    public function setRowNum($rowNum)
    {
        $this->rowNum = $rowNum;
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