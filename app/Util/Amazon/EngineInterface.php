<?php

namespace App\Util\Amazon;

use AmazonPHP\SellingPartner\AccessToken;
use AmazonPHP\SellingPartner\SellingPartnerSDK;
use App\Util\AmazonSDK;

interface EngineInterface
{
    public function launch(AmazonSDK $amazonSDK, SellingPartnerSDK $sdk, AccessToken $accessToken, CreatorInterface $creator): bool;
}