<?php

/**
 * 测试 qps 15
 */
include dirname(dirname(__FILE__)) . '/lib/functions.php';

$data = test(100, 'http://localhost:9501/');
echo "通过 {$data['success']} 个" . PHP_EOL;
echo "失败 {$data['error']} 个" . PHP_EOL;
