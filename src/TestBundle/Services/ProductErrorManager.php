<?php

namespace TestBundle\Services;

class ProductErrorManager
{
    const INVALID_DATA = 'not correct data';
    const TOO_LONG_DATA = 'to long data';
    const EMPTY_STOCK = 'stock count can\'t be blank';
    const TOO_SMALL_STOCK = 'cost is less than 5GBP and (or) Stock count is less than 10';
    const TOO_BIG_STOCK = 'cost is more than 1000GBP';
    const DUPLICATE_CODE = 'product with same code is exist at DB';

}