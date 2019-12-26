#!/bin/bash
basepath=$(cd `dirname $0`; pwd)
cd $basepath
if [ -f "../runtime/hyperf.pid" ];then
cat ../runtime/hyperf.pid | awk '{print $1}' | xargs kill -9 && rm -rf ../runtime/hyperf.pid && rm -rf ../runtime/container
fi
php hyperf.php start