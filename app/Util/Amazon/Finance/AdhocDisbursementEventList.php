<?php

namespace App\Util\Amazon\Finance;

class AdhocDisbursementEventList extends FinanceBase
{

    public function run($financialEvents): bool
    {
        // TODO: Implement run() method.
        echo '1111' . '-' . time();
        return true;
    }
}