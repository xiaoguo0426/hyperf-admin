<?php

namespace App\Util\Log;

class AmazonFbaInventory extends AbstractLog
{
    public function __construct()
    {
        parent::__construct('inventory', 'amazon-fba');
    }
}