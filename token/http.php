<?php
include "TokenBucketLimiter.php";


$http = new Swoole\Http\Server("127.0.0.1", 9501);

$limiter = new TokenBucketLimiter(10, 2);

$http->on("start", function () {
    echo "http server is started at http://127.0.0.1:9501" . PHP_EOL;
});

$http->on("request", function ($request, $response) use ($limiter) {
    //Chrome 请求两次问题
    if ($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {
        $response->end();
        return;
    }

    $response->header("Content-Type", "text/html; charset=utf-8");

    if (!$limiter->tryAcquire()) {
        $response->end(json_encode(['code' => 4300, 'msg' => '拒绝访问']));
    } else {
        $response->end(json_encode(['code' => 200, 'msg' => 'hello world!']));
    }
});

$http->start();