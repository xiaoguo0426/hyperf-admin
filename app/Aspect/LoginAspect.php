<?php

declare(strict_types=1);

namespace App\Aspect;

use App\Annotation\LoginAnnotation;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;

class LoginAspect extends AbstractAspect
{
    // 要切入的类，可以多个，亦可通过 :: 标识到具体的某个方法，通过 * 可以模糊匹配
    public $classes = [
        'App\Logic\LoginLogic::login',
    ];

    public $annotations = [
        LoginAnnotation::class,
    ];

    /**
     * @return mixed
     *
     * @throws \Hyperf\Di\Exception\Exception
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
//        var_dump($proceedingJoinPoint->getArguments());
        return $proceedingJoinPoint->process();
    }
}
