<?php

namespace App\Util\Amazon\Finance;

class ValueAddedServiceChargeEventList extends FinanceBase
{
    /**
     * @param $financialEvents
     * @return bool
     */
    public function run($financialEvents): bool
    {

        return true;
    }
}