<?php

/*
 * This file is part of Swap.
 *
 * (c) Florian Voutzinos <florian@voutzinos.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swap;

use Swap\Model\CurrencyPair;

/**
 * An implementation of Swap.
 *
 * @author Florian Voutzinos <florian@voutzinos.com>
 */
class Swap implements SwapInterface
{
    private $provider;
    private $cache;
    private $dateCache;

    public function __construct(ProviderInterface $provider, CacheInterface $cache = null, CacheDateInterface $dateCache = null)
    {
        $this->provider = $provider;
        $this->cache = $cache;
        $this->dateCache = $dateCache;
    }

    /**
     * {@inheritdoc}
     */
    public function quote($currencyPair, $date = null)
    {
        if (is_string($currencyPair)) {
            $currencyPair = CurrencyPair::createFromString($currencyPair);
        } elseif (!$currencyPair instanceof CurrencyPair) {
            throw new \InvalidArgumentException(
                'The currency pair must be either a string or an instance of CurrencyPair'
            );
        }

        //todo: abstract???
        if (isset($date)) {
            // date cache check, first cache check 
            if (null !== $this->dateCache && null !== $rate = $this->dateCache->fetchRate($currencyPair, $date)) {
                return $rate;
            }

            $rate = $this->provider->fetchRate($currencyPair, $date);

            //store date and rate
            if (null !== $this->dateCache && isset($date)) {
                $this->dateCache->storeRate($currencyPair, $rate, $date);
            }

            return $rate;
        }

        // original cache check
        if (null !== $this->cache && null !== $rate = $this->cache->fetchRate($currencyPair)) {
            return $rate;
        }

        $rate = $this->provider->fetchRate($currencyPair);

        // original cache set
        if (null !== $this->cache) {
            $this->cache->storeRate($currencyPair, $rate);
        }

        return $rate;
    }
}
