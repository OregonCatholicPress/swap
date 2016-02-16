<?php

/*
 * This file is part of Swap.
 *
 * (c) Florian Voutzinos <florian@voutzinos.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swap\Provider;

use Swap\Exception\UnsupportedCurrencyPairException;
use Swap\Model\CurrencyPair;
use Swap\Model\Rate;
use Swap\Util\StringUtil;

/**
 * European Central Bank provider.
 *
 * @author Florian Voutzinos <florian@voutzinos.com>
 */
class FixerProvider extends AbstractProvider
{
    const URL = 'https://api.fixer.io/';

    /**
     * {@inheritdoc}
     */
    public function fetchRate(CurrencyPair $currencyPair, $date = null)
    {
        $getAppend = function ($from, $to, $date = null) {
            $date = isset($date) ? ($date) : 'latest';
            return "$date?symbols=$to&base=$from";
        };

        $url = self::URL.$getAppend(
            $currencyPair->getBaseCurrency(),
            $currencyPair->getQuoteCurrency(),
            $date
        ); 

        $content = $this->fetchContent($url);
        $return = StringUtil::jsonToArray($content);
        $return['date'] = isset($date) ? new \DateTime($date) : new \DateTime();

        return new Rate(current($return['rates']), $return['date']);

        throw new UnsupportedCurrencyPairException($currencyPair);
    }
}
