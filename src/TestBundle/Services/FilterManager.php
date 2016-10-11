<?php

namespace TestBundle\Services;

use Ddeboer\DataImport\Result;
use TestBundle\Entity\Product;
use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Reader\CsvReader;
use Ddeboer\DataImport\Workflow\StepAggregator;
use Symfony\Component\Validator\Constraints as Assert;
use Ddeboer\DataImport\Step\FilterStep;
use TestBundle\Helper\ProductError;

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
        return $this->getFilter(Product::STOCK, new Assert\GreaterThan(ProductValidator::MIN_STOCK_COUNT));
    }

    /**
     * get min Stock Count filter
     *
     * @return mixed
     */
    public function getMinCostFilter()
    {
        return $this->getFilter(Product::COST, new Assert\GreaterThan(ProductValidator::MIN_COST));
    }

    /**
     * get max Product Cost filter
     *
     * @return mixed
     */
    public function getMaxCostFilter()
    {
        return $this->getFilter(Product::COST, new Assert\LessThan(ProductValidator::MAX_COST));
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