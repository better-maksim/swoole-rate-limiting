<?php

include 'Node.php';
include 'SlidingTimeWindow.php';



$limit = 20;
$count = 0;

$limiter = new SlidingTimeWindow(10, 10, 6000);
echo ("===========================================================================================".PHP_EOL);
echo("计数器滑动窗口算法测试开始" . PHP_EOL);
echo("开始模拟100组间隔150ms的50次请求" . PHP_EOL);


//new
//$faliCount = 0;
//for ($j = 0; $j < 100; $j++) {
//    $count = 0;
//    for ($i = 0; $i < 50; $i++) {
//        if ($limiter->tryAcquire()) {
//            $count++;
//        }
//    }
////    usleep(150);
//    //模拟50次请求，看多少能通过
//    for ($i = 0; $i < 50; $i++) {
//        if ($limiter->tryAcquire()) {
//            $count++;
//        }
//    }
//    if ($count > $limit) {
//        echo "时间窗口内放过的请求超过阈值，放过的请求数{$count}限流:{$limit}".PHP_EOL;
//        $faliCount++;
//    }
//    usleep((int)(rand(1,9) * 100));
//}
//echo "计数器滑动窗口算法测试结束，100组间隔150ms的50次请求模拟完成，限流失败组数：{$faliCount}".PHP_EOL;
//echo ("===========================================================================================".PHP_EOL);