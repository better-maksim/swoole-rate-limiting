<?php

/**
 * get请求用于获取时间
 * @param $url
 * @return mixed
 * @author guozhu<guozhu@tal.com>
 */
function get($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result, true);
}

/**
 * 多进程测试函数，模拟并发
 * @param $workNum
 * @param $url
 * @return array
 * @author guozhu<guozhu@tal.com>
 */
function test($workNum, $url)
{

    $workerNum = $workNum;
    $pool = new Swoole\Process\Pool($workerNum);
    $successCounter = new \Swoole\Atomic(0);
    $errorCounter = new \Swoole\Atomic(0);
    for ($i = 0; $i < $workerNum; $i++) {
        $process = new \Swoole\Process(function () use ($successCounter, $errorCounter, $url) {
            $data = get($url);
            if ($data['code'] == 200) {
                $successCounter->add();
            } else {
                $errorCounter->add();
            }
        });
        $process->start();
    }

    $pool->on("WorkerStop", function ($pool, $workerId) {

    });

    for ($i = 0; $i < $workerNum; $i++) {
        \Swoole\Process::wait();
    }
    return ['success' => $successCounter->get(), 'error' => $errorCounter->get()];
}