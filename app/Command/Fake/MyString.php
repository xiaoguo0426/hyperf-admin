<?php

namespace App\Command\Fake;

use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Psr\Container\ContainerInterface;
use App\Util\MyString as MyStringNew;

#[Command]
class MyString extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('fake:my-string');
    }

    public function handle(): void
    {
        $common = MyStringNew::findCommonSubstring('abcdefg', 'defghij');
        if (! empty($common)) {
            $this->info("重复部分: " . $common);
        } else {
            $this->info('没有重复部分');
        }

        $str1 = "apple123";
        $str2 = "treeapple";

        $commonPrefix = MyStringNew::findCommonPrefix($str1, $str2);
        if (! empty($commonPrefix)) {
            $this->info("重复开头部分: " . $commonPrefix);
        } else {
            $this->info('没有重复开头部分');
        }

    }

}