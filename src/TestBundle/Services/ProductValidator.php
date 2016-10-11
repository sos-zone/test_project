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
use TestBundle\Services\FilterManager;
use Ddeboer\DataImport\Writer\ArrayWriter;

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
     * Check is product already exists
     * @param string $code
     * @return boolean
     */
    public function isProductExists($code)
    {
        return $this->productRepository->findOneByCode($code);
    }

    /**
     * Check is product have blank fields
     *
     * @param Workflow $workflow
     * @param $productFields
     *
     * @return mixed
     */
    public function getBlankFieldsErrors(Workflow $workflow, $productFields)
    {
        $errBlankType = [];

        foreach ($productFields as $fieldName) {

            $filterStep = (new FilterStep())
                /** @var FilterManager $this->filterManager */
                ->add($this->filterManager->getBlankFieldFilters($fieldName));

            /** @var Workflow $workflow */
            $result = $workflow
                ->addStep($filterStep)
                ->process();

            /** @var Result $result */
            if ($result->getErrorCount()>0) {
                array_push(
                    $errBlankType,
                    new ProductError(
                        $result->getErrorCount(),
                        $fieldName.ProductValidator::BLANK_DATA,
                        $fieldName
                    )
                );
            }

        }

        return 0 == count($errBlankType) ? null : $errBlankType;
    }

    /**
     * get too small Product Errors
     *
     * @param Workflow $workflow
     * @param $productFields
     *
     * @return mixed
     */
    public function getTooSmallProductsError(Workflow $workflow, $productFields)
    {
        $tooSmallProducts = [];

        $filterStep = (new FilterStep())
            /** @var FilterManager $this->filterManager */
            ->add($this->filterManager->getMinStockCountFilter())
            ->add($this->filterManager->getMinCostFilter());

        /** @var Workflow $workflow */
        $result = $workflow
            ->addStep($filterStep)
            ->process();

        /** @var Result $result */
        if ($result->getErrorCount()>0) {
            array_push(
                $tooSmallProducts,
                new ProductError(
                    $result->getErrorCount(),
                    ProductValidator::TOO_SMALL_STOCK
                )
            );
        }

        return 0 == count($tooSmallProducts) ? null : $tooSmallProducts;
    }

    /**
     * get Too Big Cost Product Errors
     *
     * @param Workflow $workflow
     * @param $productFields
     *
     * @return mixed
     */
    public function getTooBigCostProductsError(Workflow $workflow, $productFields)
    {
        $tooSmallProducts = [];

        $filterStep = (new FilterStep())
            /** @var FilterManager $this->filterManager */
            ->add($this->filterManager->getMinStockCountFilter());

        /** @var Workflow $workflow */
        $result = $workflow
            ->addStep($filterStep)
            ->process();

        /** @var Result $result */
        if ($result->getErrorCount()>0) {
            array_push(
                $tooSmallProducts,
                new ProductError(
                    $result->getErrorCount(),
                    ProductValidator::TOO_BIG_STOCK
                )
            );
        }

        return 0 == count($tooSmallProducts) ? null : $tooSmallProducts;
    }

    /**
     * set Correct Product filters
     *
     * @param Workflow $workflow
     * @param $productFields
     *
     * @return mixed
     */
    public function setCorrectProductFilters(Workflow $workflow, Array $productFields)
    {
        $filterStep = (new FilterStep())
            /** @var FilterManager $this->filterManager */
            ->add($this->filterManager->getProductCodeFilter())
            ->add($this->filterManager->getProductNameFilter())
            ->add($this->filterManager->getProductDescriptionFilter())
            ->add($this->filterManager->getMinStockCountFilter())
            ->add($this->filterManager->getMinCostFilter())
            ->add($this->filterManager->getMinStockCountFilter())
        ;

        foreach ($productFields as $fieldName) {
            $filterStep->add($this->filterManager->getBlankFieldFilters($fieldName));
        }

        return $workflow
            ->addStep($filterStep);
    }
}