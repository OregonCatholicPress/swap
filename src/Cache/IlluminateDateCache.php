<?php

/*
 * This file is part of Swap.
 *
 * (c) Florian Voutzinos <florian@voutzinos.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swap\Cache;

use Illuminate\Contracts\Cache\Store;
use Swap\CacheDateInterface;
use Swap\Model\CurrencyPair;
use Swap\Model\Rate;

/**
 * IlluminateCache implementation.
 *
 * @author Florian Voutzinos <florian@voutzinos.com>
 */
final class IlluminateDateCache implements CacheDateInterface
{
    private $store;
    private $ttl;

    /**
     * Creates a new Illuminate cache.
     *
     * @param Store   $store The cache store
     * @param integer $ttl   The ttl in minutes
     */
    public function __construct(Store $store, $ttl = 0)
    {
        $this->store = $store;
        $this->ttl = $ttl;
    }

    protected function getKey(CurrencyPair $currencyPair, $date) {
        return $currencyPair->toString().'_'.$date;
    }

    public function fetchRate(CurrencyPair $currencyPair, $date)
    {
        return $this->store->get($this->getKey($currencyPair, $date));
    }

    public function storeRate(CurrencyPair $currencyPair, Rate $rate, $date)
    {
        $key = $this->getKey($currencyPair, $date);
        $this->store->put($key, $rate, $this->ttl);
    }
}
