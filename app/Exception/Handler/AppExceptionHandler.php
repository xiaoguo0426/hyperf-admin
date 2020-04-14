<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace App\Exception\Handler;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use Sentry;

use Sentry\ClientBuilder;
use Sentry\Transport\SpoolTransport;
use Sentry\State\Hub;
use Sentry\Spool\MemorySpool;

class AppExceptionHandler extends ExceptionHandler
{
    /**
     * @var StdoutLoggerInterface
     */
    protected $logger;

    public function __construct(StdoutLoggerInterface $logger)
    {
        $this->logger = $logger;

    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {

        $this->logger->error(sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));
//        var_dump('AppException Handler');;
//        $options = ['dsn' => 'http://6ad3bdc5904a4f909b81e379c73ff92f@sentry-web:9000/2'];
//        $spool = new MemorySpool();
//        $transport = new SpoolTransport($spool);
//        $builder = ClientBuilder::create($options);
//        $httpTransport = $builder->createTransportInstance();
//        $builder->setTransport($transport);
//        Hub::getCurrent()->bindClient($builder->getClient());
//        Hub::getCurrent()->captureException(new \Exception($throwable->getMessage(),$throwable->getCode()));
//        var_dump($throwable);
        // 调用这个方法就会开始清空之前捕捉异常的队列，思考之后觉的放在定时任务里定时清空队列比较合理。
//        $spool->flushQueue($httpTransport);

        return $response->withStatus(500)->withBody(new SwooleStream('Internal Server Error.'));
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
