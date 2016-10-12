<?php

namespace TestBundle\Services;

use Ddeboer\DataImport\Result;
use TestBundle\Entity\Product;
use Ddeboer\DataImport\Workflow;
use Symfony\Component\Validator\Constraints as Assert;
use Ddeboer\DataImport\Step\FilterStep;
use TestBundle\Services\FilterManager;

class ProductValidator
{
    const INVALID_DATA_HEADER = 'not correct CSV data header';
    const INVALID_DATA = 'not correct product data';
    const BLANK_DATA = ' field can not be blank';
    const TOO_LONG_DATA = 'to long data';
    const EMPTY_STOCK = 'stock count can\'t be blank';
    const TOO_SMALL_STOCK = 'cost is less than 5 units and (or) Stock count is less than 10';
    const TOO_BIG_STOCK = 'cost is more than 1000 units';

    const MAX_CODE_LENGTH = 10;
    const MAX_NAME_LENGTH = 50;
    const MAX_DESCRIPTION_LENGTH = 255;

    const MIN_STOCK_COUNT = 10;
    const MIN_COST = 5;
    const MAX_COST = 1000;

    private $productRepository;

    private $filterManager;

    private $em;

    public function __construct($productRepository, FilterManager $filterManager, $em)
    {
        $this->productRepository = $productRepository;
        $this->filterManager = $filterManager;
        $this->em = $em;
    }

    /**
     * set Correct Product filters
     *
     * @param Workflow $workflow
     * @param $productFields
     *
     * @return mixed
     */
    public function setCorrectProductFilters(Workflow $workflow, Array $productFields = Product::PRODUCT_DB_FIELDS)
    {
        $filterStep = (new FilterStep())->add($this->filterManager->getMaxCostFilter());
        /** @var FilterManager $this->filterManager */
        $workflow->addStep($filterStep);

        $filterStep = (new FilterStep())->add($this->filterManager->getMinCostFilter());
        /** @var FilterManager $this->filterManager */
        $workflow->addStep($filterStep);

        $filterStep = (new FilterStep())->add($this->filterManager->getMinStockCountFilter());
        /** @var FilterManager $this->filterManager */
        $workflow->addStep($filterStep);

        foreach ($productFields as $key => $field) {
            $filterStep = (new FilterStep())
                /** @var FilterManager $this ->filterManager */
                ->add($this->filterManager->getBlankFieldFilters($field));

            /** @var Workflow $workflow */
            $workflow->addStep($filterStep);
        }

        return $workflow;
    }
}