<?php


namespace App\Util;


use AmazonPHP\SellingPartner\AccessToken;
use AmazonPHP\SellingPartner\Configuration;
use AmazonPHP\SellingPartner\Exception\InvalidArgumentException;
use AmazonPHP\SellingPartner\Marketplace;
use AmazonPHP\SellingPartner\SellingPartnerSDK;
use AmazonPHP\SellingPartner\STSClient;
use App\Model\AmazonAppModel;
use App\Util\RedisHash\AmazonAccessTokenHash;
use App\Util\RedisHash\AmazonSessionTokenHash;
use Buzz\Client\Curl;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Nyholm\Psr7\Factory\Psr17Factory;

class AmazonSDK
{
    private int $id;
    private int $merchant_id;
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

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getMerchantId(): int
    {
        return $this->merchant_id;
    }

    /**
     * @param int $merchant_id
     */
    public function setMerchantId(int $merchant_id): void
    {
        $this->merchant_id = $merchant_id;
    }

    /**
     * @return string
     */
    public function getSellerId(): string
    {
        return $this->seller_id;
    }

    /**
     * @param string $seller_id
     */
    public function setSellerId(string $seller_id): void
    {
        $this->seller_id = $seller_id;
    }

    /**
     * @return string
     */
    public function getAppId(): string
    {
        return $this->app_id;
    }

    /**
     * @param string $app_id
     */
    public function setAppId(string $app_id): void
    {
        $this->app_id = $app_id;
    }

    /**
     * @return string
     */
    public function getAppName(): string
    {
        return $this->app_name;
    }

    /**
     * @param string $app_name
     */
    public function setAppName(string $app_name): void
    {
        $this->app_name = $app_name;
    }

    /**
     * @return string
     */
    public function getAwsAccessKey(): string
    {
        return $this->aws_access_key;
    }

    /**
     * @param string $aws_access_key
     */
    public function setAwsAccessKey(string $aws_access_key): void
    {
        $this->aws_access_key = $aws_access_key;
    }

    /**
     * @return string
     */
    public function getAwsSecretKey(): string
    {
        return $this->aws_secret_key;
    }

    /**
     * @param string $aws_secret_key
     */
    public function setAwsSecretKey(string $aws_secret_key): void
    {
        $this->aws_secret_key = $aws_secret_key;
    }

    /**
     * @return string
     */
    public function getUserArn(): string
    {
        return $this->user_arn;
    }

    /**
     * @param string $user_arn
     */
    public function setUserArn(string $user_arn): void
    {
        $this->user_arn = $user_arn;
    }

    /**
     * @return string
     */
    public function getRoleArn(): string
    {
        return $this->role_arn;
    }

    /**
     * @param string $role_arn
     */
    public function setRoleArn(string $role_arn): void
    {
        $this->role_arn = $role_arn;
    }

    /**
     * @return string
     */
    public function getLwaClientId(): string
    {
        return $this->lwa_client_id;
    }

    /**
     * @param string $lwa_client_id
     */
    public function setLwaClientId(string $lwa_client_id): void
    {
        $this->lwa_client_id = $lwa_client_id;
    }

    /**
     * @return string
     */
    public function getLwaClientIdSecret(): string
    {
        return $this->lwa_client_id_secret;
    }

    /**
     * @param string $lwa_client_id_secret
     */
    public function setLwaClientIdSecret(string $lwa_client_id_secret): void
    {
        $this->lwa_client_id_secret = $lwa_client_id_secret;
    }

    /**
     * @return string
     */
    public function getRegion(): string
    {
        return $this->region;
    }

    /**
     * @param string $region
     */
    public function setRegion(string $region): void
    {
        $this->region = $region;
    }

    /**
     * @return array
     */
    public function getCountryIds(): array
    {
        return explode(',', $this->country_ids);
    }

    /**
     * @param string $country_ids
     */
    public function setCountryIds(string $country_ids): void
    {
        $this->country_ids = $country_ids;
    }

    /**
     * @return array
     */
    public function getMarketplaceIds(): array
    {
        return $this->marketplace_ids;
    }

    /**
     * @param array $marketplace_ids
     */
    public function setMarketplaceIds(array $marketplace_ids): void
    {
        $this->marketplace_ids = $marketplace_ids;
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refresh_token;
    }

    /**
     * @param string $refresh_token
     */
    public function setRefreshToken(string $refresh_token): void
    {
        $this->refresh_token = $refresh_token;
    }

    public function getSdk(): SellingPartnerSDK
    {
        $factory = new Psr17Factory();
        $client = new Curl($factory);

        $sts = new STSClient(
            $client,
            $requestFactory = $factory,
            $streamFactory = $factory
        );
        $hash = make(AmazonSessionTokenHash::class, ['merchant_id' => $this->getMerchantId(), 'merchant_store_id' => $this->getId()]);
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
            $expiration = $assumeRole->expiration();
            $hash->expiration = $expiration;
            $hash->ttl(25 * 60);
        }

        $configuration = Configuration::forIAMRole(
            $this->getLwaClientId(),
            $this->getLwaClientIdSecret(),
            $assumeRole
        );

        $logger = new Logger('amazon');
        $logger->pushHandler(new StreamHandler(BASE_PATH . '/runtime/' . '/sp-api-php.log', Logger::ERROR));

        $this->sdk = SellingPartnerSDK::create($client, $factory, $factory, $configuration, $logger);

        return $this->sdk;

    }

    public function getToken(): AccessToken
    {
        $hash = make(AmazonAccessTokenHash::class, ['merchant_id' => $this->getMerchantId(), 'merchant_store_id' => $this->getId()]);
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
            $hash->ttl(24 * 60);
        }

        return $accessToken;
    }

    /**
     * 根据marketplace_id获取对应的国家
     * @param string $marketplace_id
     * @return string
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