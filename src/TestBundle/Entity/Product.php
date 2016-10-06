<?php

namespace TestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Product
 *
 * @ORM\Table(name="tblProductData", uniqueConstraints={@ORM\UniqueConstraint(name="strProductCode", columns={"strProductCode"})})
 * @ORM\Entity(repositoryClass="TestBundle\Entity\Repository\ProductRepository")
 */
class Product
{
    const CODE = 'Product Code';
    const NAME = 'Product Name';
    const DESCRIPTION = 'Product Description';
    const STOCK = 'Stock';
    const COST_IN_GBP = 'Cost in GBP';
    const DISCONTINUED = 'Discontinued';

    /**
     * @var string
     *
     * @ORM\Column(name="strProductName", type="string", length=50, nullable=false)
     * @JMS\SerializedName("name")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="strProductDesc", type="string", length=255, nullable=false)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="strProductCode", type="string", length=10, nullable=false)
     */
    private $code;

    /**
     * @var integer
     *
     * @ORM\Column(name="strProductStock", type="integer", nullable=false)
     */
    private $stock;

    /**
     * @var float
     *
     * @ORM\Column(name="strProductCostInGBP", type="decimal", nullable=false)
     */
    private $costInGBP;

    /**
     * @var boolean
     *
     * @ORM\Column(name="strProductDiscontinued", type="boolean", nullable=false)
     */
    private $discontinued;

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
     * @ORM\Column(name="stmTimestamp", type="datetime", nullable=false)
     */
    private $stmtimestamp;

    /**
     * @var integer
     *
     * @ORM\Column(name="intProductDataId", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $intproductdataid;



    /**
     * Set name
     *
     * @param string $name
     * @return Product
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return product
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get $description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Product
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set Stock
     *
     * @param integer $stock
     * @return Product
     */
    public function setStock($stock)
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * Get stock
     *
     * @return integer
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * Set costInGBP
     *
     * @param float $costInGBP
     * @return Product
     */
    public function setCostInGBP($costInGBP)
    {
        $this->costInGBP = $costInGBP;

        return $this;
    }

    /**
     * Get costInGBP
     *
     * @return float
     */
    public function getCostInGBP()
    {
        return $this->costInGBP;
    }

    /**
     * Set discontinued
     *
     * @param boolean $discontinued
     * @return Product
     */
    public function setDiscontinued($discontinued)
    {
        $this->discontinued = $discontinued;

        return $this;
    }

    /**
     * is discontinued
     *
     * @return boolean
     */
    public function isDiscontinued()
    {
        return $this->discontinued;
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
