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
    const DUPLICATE_CODE = 'product with same code is exist at DB';

    const MAX_CODE_LENGTH = 10;
    const MAX_NAME_LENGTH = 50;
    const MAX_DESCRIPTION_LENGTH = 255;

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
        return $product->getStock()<10;
    }

    /**
     * Check for too small cost
     * @param Product $product
     * @return boolean
     */
    public function isTooSmallCost(Product $product)
    {
        return $product->getCost()<5;
    }

    /**
     * Check for too big cost
     * @param Product $product
     * @return boolean
     */
    public function isTooBigCost(Product $product)
    {
        return $product->getCost()>1000;
    }

    /**
     * Check for duplicate product code
     * @param Product $product
     * @return boolean
     */
    public function isProductExists(Product $product)
    {
        return $this->productRepository->findByCode($product->getCode());
    }
}