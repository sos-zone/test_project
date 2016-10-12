<?php

namespace TestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Product
 *
 * @ORM\Table(name="tblProductData")
 * @ORM\Entity(repositoryClass="TestBundle\Entity\Repository\ProductRepository")
 */
class Product
{
    const CODE = 'Product Code';
    const NAME = 'Product Name';
    const DESCRIPTION = 'Product Description';
    const STOCK = 'Stock';
    const COST = 'Cost in GBP';
    const DISCONTINUED = 'Discontinued';

    /**
     * @var string
     *
     * @ORM\Column(name="strProductName", type="string", length=50, nullable=false)
     * @ JMS\SerializedName("name")
     */
    private $strProductName;

    /**
     * @var string
     *
     * @ORM\Column(name="strProductDesc", type="string", length=255, nullable=false)
     */
    private $strProductDesc;

    /**
     * @var string
     *
     * @ORM\Column(name="strProductCode", type="string", length=10, nullable=false)
     */
    private $strProductCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="strProductStock", type="integer", nullable=false)
     */
    private $strProductStock;

    /**
     * @var float
     *
     * @ORM\Column(name="strProductCost", type="decimal", precision=11, scale=2, nullable=false)
     */
    private $strProductCost;

    /**
     * @var boolean
     *
     * @ORM\Column(name="strProductDiscontinued", type="boolean", nullable=false)
     */
    private $strProductDiscontinued;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dtmAdded", type="datetime", nullable=true)
     */
    private $dtmadded;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dtmDiscontinued", type="datetime", nullable=true)
     */
    private $dtmdiscontinued;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="stmTimestamp", type="datetime", nullable=true)
     */
    private $stmtimestamp;

    /**
     * @var int
     *
     * @ORM\Column(name="intProductDataId", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $intproductdataid;



    /**
     * Set name
     *
     * @param string $strProductName
     * @return Product
     */
    public function setStrProductName($strProductName)
    {
        $this->strProductName = $strProductName;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getStrProductName()
    {
        return $this->strProductName;
    }

    /**
     * Set strProductDesc
     *
     * @param string $strProductDesc
     * @return product
     */
    public function setStrProductDesc($strProductDesc)
    {
        $this->strProductDesc = $strProductDesc;

        return $this;
    }

    /**
     * Get $strProductDesc
     *
     * @return string
     */
    public function getStrProductDesc()
    {
        return $this->strProductDesc;
    }

    /**
     * Set strProductCode
     *
     * @param string $strProductCode
     * @return Product
     */
    public function setStrProductCode($strProductCode)
    {
        $this->strProductCode = $strProductCode;

        return $this;
    }

    /**
     * Get $strProductCode
     *
     * @return string
     */
    public function getStrProductCode()
    {
        return $this->strProductCode;
    }

    /**
     * Set $strProductStock
     *
     * @param integer $strProductStock
     * @return Product
     */
    public function setStrProductStock($strProductStock)
    {
        $this->strProductStock = $strProductStock;

        return $this;
    }

    /**
     * Get strProductStock
     *
     * @return integer
     */
    public function getStrProductStock()
    {
        return $this->strProductStock;
    }

    /**
     * Set strProductCost
     *
     * @param float $strProductCost
     * @return Product
     */
    public function setStrProductCost($strProductCost)
    {
        $this->strProductCost = $strProductCost;

        return $this;
    }

    /**
     * Get strProductCost
     *
     * @return float
     */
    public function getStrProductCost()
    {
        return $this->strProductCost;
    }

    /**
     * Set strProductDiscontinued
     *
     * @param boolean $strProductDiscontinued
     * @return Product
     */
    public function setStrProductDiscontinued($strProductDiscontinued)
    {
        $this->strProductDiscontinued = $strProductDiscontinued;

        return $this;
    }

    /**
     * is strProductDiscontinued
     *
     * @return boolean
     */
    public function isStrProductDiscontinued()
    {
        return $this->strProductDiscontinued;
    }

    /**
     * Set dtmadded
     *
     * @param \DateTime $dtmadded
     * @return Product
     */
    public function setDtmadded($dtmadded)
    {
        $this->dtmadded = $dtmadded;

        return $this;
    }

    /**
     * Get dtmadded
     *
     * @return \DateTime
     */
    public function getDtmadded()
    {
        return $this->dtmadded;
    }

    /**
     * Set dtmdiscontinued
     *
     * @param \DateTime $dtmdiscontinued
     * @return Product
     */
    public function setDtmdiscontinued($dtmdiscontinued)
    {
        $this->dtmdiscontinued = $dtmdiscontinued;

        return $this;
    }

    /**
     * Get dtmdiscontinued
     *
     * @return \DateTime
     */
    public function getDtmdiscontinued()
    {
        return $this->dtmdiscontinued;
    }

    /**
     * Set stmtimestamp
     *
     * @param \DateTime $stmtimestamp
     * @return Product
     */
    public function setStmtimestamp($stmtimestamp)
    {
        $this->stmtimestamp = $stmtimestamp;

        return $this;
    }

    /**
     * Get stmtimestamp
     *
     * @return \DateTime
     */
    public function getStmtimestamp()
    {
        return $this->stmtimestamp;
    }

    /**
     * Get intproductdataid
     *
     * @return integer
     */
    public function getIntproductdataid()
    {
        return $this->intproductdataid;
    }
}
