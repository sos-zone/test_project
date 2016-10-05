<?php

namespace TestBundle\Services;

use TestBundle\Entity\Product;

class ProductErrorManager
{
    const INVALID_DATA = 'not correct data';
    const TOO_LONG_DATA = 'to long data';
    const EMPTY_STOCK = 'stock count can\'t be blank';
    const TOO_SMALL_STOCK = 'cost is less than 5GBP and (or) Stock count is less than 10';
    const TOO_BIG_STOCK = 'cost is more than 1000GBP';
    const DUPLICATE_CODE = 'product with same code is exist at DB';

    private $productRepository;

    public function __construct($productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Check for too small stock
     * @param Product $product
     * @return boolean
     */
    public function isTooSmallStock(Product $product)
    {
        return $product->getCostInGBP()<5 && $product->getStock()<10;
    }

    /**
     * Check for too big cost
     * @param Product $product
     * @return boolean
     */
    public function isTooBigCost(Product $product)
    {
        return $product->getCostInGBP()>1000;
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