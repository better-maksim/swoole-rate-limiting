<?php
include "Node.php";
include 'SlidingTimeWindow.php';

$http = new Swoole\Http\Server("127.0.0.1", 9501);
$clock = new \Swoole\Lock();
$limiter = new SlidingTimeWindow(6, 80, 1000, $clock);


$http->on("start", function () {
    echo "http server is started at http://127.0.0.1:9502" . PHP_EOL;
});

$http->on("request", function ($request, $response) use ($limiter) {
    //Chrome 请求两次问题
    if ($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {
        $response->end();
        return;
    }

    $response->header("Content-Type", "text/html; charset=utf-8");

    if (!$limiter->tryAcquire()) {
        $response->status(403);
        $response->end(json_encode(['code' => 4300, 'msg' => '拒绝访问']));
    } else {
        $response->end(json_encode(['code' => 200, 'msg' => 'hello world!']));
    }
});

$http->start();