<?php

include 'LeakyBucket.php';

$http = new Swoole\Http\Server("127.0.0.1", 9501);
$water = new \Swoole\Atomic\Long();
$lastTime = new \Swoole\Atomic\Long();

$lock = new \Swoole\Lock(SWOOLE_MUTEX, 'http');

$limiter = new LeakyBucket(1, 10, $water, $lastTime, $lock);


$http->on("start", function () {
    echo "http server is started at http://127.0.0.1:9501" . PHP_EOL;
});

$http->on("request", function ($request, $response) use ($limiter, $lock) {
    //Chrome 请求两次问题
    if ($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {
        $response->end();
        return;
    }
    $response->header("Content-Type", "text/html; charset=utf-8");


    $ret = !$limiter->tryAcquire();

    if ($ret) {
        $response->status(403);
        $response->end(json_encode(['code' => 4300, 'msg' => '拒绝访问']));
    } else {
        $response->end(json_encode(['code' => 200, 'msg' => 'hello world!']));
    }
});

$http->start();