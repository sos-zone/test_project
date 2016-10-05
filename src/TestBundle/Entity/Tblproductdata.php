<?php

namespace TestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Tblproductdata
 *
 * @ORM\Table(name="tblProductData", uniqueConstraints={@ORM\UniqueConstraint(name="strProductCode", columns={"strProductCode"})})
 * @ORM\Entity(repositoryClass="TestBundle\Entity\Repository\TblproductdataRepository")
 */
class Tblproductdata
{
    /**
     * @var string
     *
     * @ORM\Column(name="strProductName", type="string", length=50, nullable=false)
     * @JMS\SerializedName("productName")
     */
    private $productName;

    /**
     * @var string
     *
     * @ORM\Column(name="strProductDesc", type="string", length=255, nullable=false)
     */
    private $productDesc;

    /**
     * @var string
     *
     * @ORM\Column(name="strProductCode", type="string", length=10, nullable=false)
     */
    private $strproductcode;

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
     * Set productName
     *
     * @param string $productName
     * @return Tblproductdata
     */
    public function setProductName($productName)
    {
        $this->productName = $productName;

        return $this;
    }

    /**
     * Get productName
     *
     * @return string 
     */
    public function getProductName()
    {
        return $this->productName;
    }

    /**
     * Set productDesc
     *
     * @param string $productDesc
     * @return Tblproductdata
     */
    public function setProductDesc($productDesc)
    {
        $this->productDesc = $productDesc;

        return $this;
    }

    /**
     * Get $productDesc
     *
     * @return string 
     */
    public function getProductDesc()
    {
        return $this->productDesc;
    }

    /**
     * Set strproductcode
     *
     * @param string $strproductcode
     * @return Tblproductdata
     */
    public function setStrproductcode($strproductcode)
    {
        $this->strproductcode = $strproductcode;

        return $this;
    }

    /**
     * Get strproductcode
     *
     * @return string 
     */
    public function getStrproductcode()
    {
        return $this->strproductcode;
    }

    /**
     * Set dtmadded
     *
     * @param \DateTime $dtmadded
     * @return Tblproductdata
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
     * @return Tblproductdata
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
     * @return Tblproductdata
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
