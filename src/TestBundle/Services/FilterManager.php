<?php

namespace TestBundle\Services;

use TestBundle\Entity\Product;
use Symfony\Component\Validator\Constraints as Assert;

class FilterManager
{
    private $validator;

    public function __construct($validator)
    {
        $this->validator = $validator;
    }

    /**
     * get blank validator filter
     *
     * @param string $fieldName
     *
     * @return mixed
     */
    public function getBlankFieldFilters($fieldName = Product::STOCK)
    {
        return $this->getFilter($fieldName, new Assert\NotBlank());
    }

    /**
     * get min Stock Count filter
     *
     * @return mixed
     */
    public function getMinStockCountFilter()
    {
        return $this->getFilter('strProductStock', new Assert\GreaterThan(ProductValidator::MIN_STOCK_COUNT));
    }

    /**
     * get min Stock Count filter
     *
     * @return mixed
     */
    public function getMinCostFilter()
    {
        return $this->getFilter('strProductCost', new Assert\GreaterThan(ProductValidator::MIN_COST));
    }

    /**
     * get max Product Cost filter
     *
     * @return mixed
     */
    public function getMaxCostFilter()
    {
        return $this->getFilter('strProductCost', new Assert\LessThan(ProductValidator::MAX_COST));
    }

    /**
     * get Product Code Filter
     *
     * @return mixed
     */
    public function getProductCodeFilter()
    {
        return $this->getFilter(Product::CODE, new Assert\LessThan(ProductValidator::MAX_CODE_LENGTH ));
    }

    /**
     * get Product Name Filter
     *
     * @return mixed
     */
    public function getProductNameFilter()
    {
        return $this->getFilter(Product::NAME, new Assert\LessThan(ProductValidator::MAX_NAME_LENGTH));
    }

    /**
     * get Product Description Filter
     *
     * @return mixed
     */
    public function getProductDescriptionFilter()
    {
        return $this->getFilter(Product::NAME, new Assert\LessThan(ProductValidator::MAX_DESCRIPTION_LENGTH));
    }

    /**
     * get Product
     *
     * @return mixed
     */
    public function getProductFilter($code)
    {
        return $this->getFilter(Product::CODE, new Assert\IdenticalTo($code));
    }

    /**
     * get filter
     *
     * @param string $fieldName
     * @param $filterFnc
     *
     * @return mixed
     */
    private function getFilter($fieldName, $filterFnc)
    {
        $validatorFilter = new \Ddeboer\DataImport\Filter\ValidatorFilter($this->validator);
        $validatorFilter->throwExceptions(true);
        $validatorFilter->setStrict(false);
        $validatorFilter->add($fieldName, $filterFnc);

        return $validatorFilter;
    }
}