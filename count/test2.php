<?php
/**
 * 测试 qps 15
 */
include dirname(dirname(__FILE__)) . '/lib/functions.php';

$workerNum = 10;

$pool = new Swoole\Process\Pool($workerNum);
$successCounter = new \Swoole\Atomic(0);
$errorCounter = new \Swoole\Atomic(0);
for ($i = 0; $i < $workerNum; $i++) {
    $process = new \Swoole\Process(function () use ($successCounter, $errorCounter) {
        $data = get('http://localhost:9501/');
        if ($data['code'] == 200) {
            $successCounter->add();
        } else {
            $errorCounter->add();
        }
    });
    $process->start();
}

usleep('');

$pool->on("WorkerStop", function ($pool, $workerId) {

});

for ($i = 0; $i < $workerNum; $i++) {
    \Swoole\Process::wait();
}

echo "通过 {$successCounter->get()} 个".PHP_EOL;
echo "失败 {$errorCounter->get()} 个".PHP_EOL;
