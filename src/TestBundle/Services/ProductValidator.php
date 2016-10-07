<?php

namespace TestBundle\Services;

use TestBundle\Entity\Product;

class ProductValidator
{
    const INVALID_DATA_HEADER = 'not correct CSV data header';
    const INVALID_DATA = 'not correct product data';
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

    public function __construct($productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Check for too few stocks
     * @param Product $product
     * @return boolean
     */
    public function isTooFewStocks(Product $product)
    {
        return $product->getStock() < $this::MIN_STOCK_COUNT;
    }

    /**
     * Check for too small cost
     * @param Product $product
     * @return boolean
     */
    public function isTooSmallCost(Product $product)
    {
        return $product->getCost() < $this::MIN_COST;
    }

    /**
     * Check for too big cost
     * @param Product $product
     * @return boolean
     */
    public function isTooBigCost(Product $product)
    {
        return $product->getCost() > $this::MAX_COST;
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
}