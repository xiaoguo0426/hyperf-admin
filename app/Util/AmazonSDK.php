<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util;

use AmazonPHP\SellingPartner\AccessToken;
use AmazonPHP\SellingPartner\Configuration;
use AmazonPHP\SellingPartner\Exception\ApiException;
use AmazonPHP\SellingPartner\Exception\InvalidArgumentException;
use AmazonPHP\SellingPartner\Extension;
use AmazonPHP\SellingPartner\Marketplace;
use AmazonPHP\SellingPartner\SellingPartnerSDK;
use AmazonPHP\SellingPartner\STSClient;
use App\Model\AmazonAppModel;
use App\Util\RedisHash\AmazonAccessTokenHash;
use App\Util\RedisHash\AmazonSessionTokenHash;
use Buzz\Client\Curl;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LogLevel;

class AmazonSDK
{
    private int $id;

    private int $merchant_id;

    private int $merchant_store_id;

    private string $seller_id;

    private string $app_id;

    private string $app_name;

    private string $aws_access_key;

    private string $aws_secret_key;

    private string $user_arn;

    private string $role_arn;

    private string $lwa_client_id;

    private string $lwa_client_id_secret;

    private string $region;

    private string $country_ids;

    private array $marketplace_ids;

    private string $refresh_token;

    private array $marketplace_id_country_map = [];

    private SellingPartnerSDK $sdk;

    public function __construct(AmazonAppModel $amazonAppModel)
    {
        $this->setId($amazonAppModel->id);
        $this->setMerchantId($amazonAppModel->merchant_id);
        $this->setMerchantStoreId($amazonAppModel->merchant_store_id);
        $this->setSellerId($amazonAppModel->seller_id);
        $this->setAppId($amazonAppModel->app_id);
        $this->setAppName($amazonAppModel->app_name);
        $this->setAwsAccessKey($amazonAppModel->aws_access_key);
        $this->setAwsSecretKey($amazonAppModel->aws_secret_key);
        $this->setUserArn($amazonAppModel->user_arn);
        $this->setRoleArn($amazonAppModel->role_arn);
        $this->setLwaClientId($amazonAppModel->lwa_client_id);
        $this->setLwaClientIdSecret($amazonAppModel->lwa_client_id_secret);
        $this->setRegion($amazonAppModel->region);
        $this->setCountryIds($amazonAppModel->country_ids);

        $marketplace_ids = [];
        foreach ($this->getCountryIds() as $countryId) {
            try {
                $country = Marketplace::fromCountry($countryId);
                $marketplace_ids[] = $country->id();
                $this->marketplace_id_country_map[$country->id()] = $countryId;
            } catch (InvalidArgumentException $exception) {
            }
        }
        $this->setMarketplaceIds($marketplace_ids);

        $this->setRefreshToken($amazonAppModel->refresh_token);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getMerchantId(): int
    {
        return $this->merchant_id;
    }

    public function setMerchantId(int $merchant_id): void
    {
        $this->merchant_id = $merchant_id;
    }

    public function getMerchantStoreId(): int
    {
        return $this->merchant_store_id;
    }

    public function setMerchantStoreId(int $merchant_store_id): void
    {
        $this->merchant_store_id = $merchant_store_id;
    }

    public function getSellerId(): string
    {
        return $this->seller_id;
    }

    public function setSellerId(string $seller_id): void
    {
        $this->seller_id = $seller_id;
    }

    public function getAppId(): string
    {
        return $this->app_id;
    }

    public function setAppId(string $app_id): void
    {
        $this->app_id = $app_id;
    }

    public function getAppName(): string
    {
        return $this->app_name;
    }

    public function setAppName(string $app_name): void
    {
        $this->app_name = $app_name;
    }

    public function getAwsAccessKey(): string
    {
        return $this->aws_access_key;
    }

    public function setAwsAccessKey(string $aws_access_key): void
    {
        $this->aws_access_key = $aws_access_key;
    }

    public function getAwsSecretKey(): string
    {
        return $this->aws_secret_key;
    }

    public function setAwsSecretKey(string $aws_secret_key): void
    {
        $this->aws_secret_key = $aws_secret_key;
    }

    public function getUserArn(): string
    {
        return $this->user_arn;
    }

    public function setUserArn(string $user_arn): void
    {
        $this->user_arn = $user_arn;
    }

    public function getRoleArn(): string
    {
        return $this->role_arn;
    }

    public function setRoleArn(string $role_arn): void
    {
        $this->role_arn = $role_arn;
    }

    public function getLwaClientId(): string
    {
        return $this->lwa_client_id;
    }

    public function setLwaClientId(string $lwa_client_id): void
    {
        $this->lwa_client_id = $lwa_client_id;
    }

    public function getLwaClientIdSecret(): string
    {
        return $this->lwa_client_id_secret;
    }

    public function setLwaClientIdSecret(string $lwa_client_id_secret): void
    {
        $this->lwa_client_id_secret = $lwa_client_id_secret;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function setRegion(string $region): void
    {
        $this->region = $region;
    }

    public function getCountryIds(): array
    {
        return explode(',', $this->country_ids);
    }

    public function setCountryIds(string $country_ids): void
    {
        $this->country_ids = $country_ids;
    }

    public function getMarketplaceIds(): array
    {
        return $this->marketplace_ids;
    }

    public function setMarketplaceIds(array $marketplace_ids): void
    {
        $this->marketplace_ids = $marketplace_ids;
    }

    public function getRefreshToken(): string
    {
        return $this->refresh_token;
    }

    public function setRefreshToken(string $refresh_token): void
    {
        $this->refresh_token = $refresh_token;
    }

    /**
     * @throws ApiException
     * @throws ClientExceptionInterface
     * @throws \JsonException
     */
    public function getSdk(): SellingPartnerSDK
    {
        $factory = new Psr17Factory();
        $client = new Curl($factory);

        $sts = new STSClient(
            $client,
            $requestFactory = $factory,
            $streamFactory = $factory
        );

        $region = $this->getRegion();

        $hash = \Hyperf\Support\make(AmazonSessionTokenHash::class, ['merchant_id' => $this->getMerchantId(), 'merchant_store_id' => $this->getMerchantStoreId(), 'region' => $region]);
        $sessionToken = $hash->sessionToken;
        if ($sessionToken) {
            $assumeRole = new STSClient\Credentials($hash->accessKeyId, $hash->secretAccessKey, $sessionToken, (int) $hash->expiration);
        } else {
            $assumeRole = $sts->assumeRole(
                $this->getAwsAccessKey(),
                $this->getAwsSecretKey(),
                $this->getRoleArn()
            );

            $hash->accessKeyId = $assumeRole->accessKeyId();
            $hash->secretAccessKey = $assumeRole->secretAccessKey();
            $hash->sessionToken = $assumeRole->sessionToken();
            $hash->expiration = $assumeRole->expiration();
            $hash->ttl(50 * 60);
        }

        $configuration = Configuration::forIAMRole(
            $this->getLwaClientId(),
            $this->getLwaClientIdSecret(),
            $assumeRole
        );

        $logger = new Logger('amazon');
        $logger->pushHandler(new StreamHandler(BASE_PATH . '/runtime/sp-api-php.log', Level::Info));

//        $configuration->setDefaultLogLevel(LogLevel::INFO);
//
//        $configuration->registerExtension(new class implements Extension {
//            public function preRequest(string $api, string $operation, RequestInterface $request): void
//            {
//                echo "pre: " . $api . "::" . $operation . " " . $request->getUri() . "\n";
//            }
//
//            public function postRequest(string $api, string $operation, RequestInterface $request, ResponseInterface $response): void
//            {
//                echo "post: " . $api . "::" . $operation . " " . $request->getUri() . " "
//                    . $response->getStatusCode() . " rate limit: " . implode(' ', $response->getHeader('x-amzn-RateLimit-Limit')) . "\n";
//            }
//        });

        $this->sdk = SellingPartnerSDK::create($client, $factory, $factory, $configuration, $logger);

        return $this->sdk;
    }

    /**
     * @throws ApiException
     * @throws ClientExceptionInterface
     */
    public function getToken(string $region): AccessToken
    {
        $hash = \Hyperf\Support\make(AmazonAccessTokenHash::class, ['merchant_id' => $this->getMerchantId(), 'merchant_store_id' => $this->getMerchantStoreId(), 'region' => $region]);
        $token = $hash->token;
        if ($hash->token) {
            $accessToken = new AccessToken(
                $token,
                $hash->refreshToken,
                $hash->type,
                $hash->expiresIn,
                $hash->grantType
            );
        } else {
            $accessToken = $this->sdk->oAuth()->exchangeRefreshToken($this->getRefreshToken());
            $hash->load([
                'token' => $accessToken->token(),
                'refreshToken' => $accessToken->refreshToken(),
                'type' => $accessToken->type(),
                'expiresIn' => $accessToken->expiresIn(),
                'grantType' => $accessToken->grantType(),
            ]);

            $ttl = $accessToken->expiresIn() - 120;
            $hash->ttl($ttl);
        }

        return $accessToken;
    }

    /**
     * 根据marketplace_id获取对应的国家.
     */
    public function fetchCountryFromMarketplaceId(string $marketplace_id): string
    {
        return $this->marketplace_id_country_map[$marketplace_id] ?? '';
    }

    /**
     * @throws InvalidArgumentException
     */
    public function fetchMarketplaceIdFromCountryId(string $country_id): string
    {
        return Marketplace::fromCountry($country_id)->id();
    }
}
