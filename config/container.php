<?php
/**
 * Initialize a dependency injection container that implemented PSR-11 and return the container.
 */

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */
use Hyperf\Di\Container;
use Hyperf\Di\Definition\DefinitionSourceFactory;
use Hyperf\Utils\ApplicationContext;

$container = new Container((new DefinitionSourceFactory())());

return ApplicationContext::setContainer($container);
