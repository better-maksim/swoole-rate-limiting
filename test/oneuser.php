<?php
include dirname(dirname(__FILE__)) . '/lib/functions.php';
$success = 0;
$error = 0;
for ($i = 0; $i < 65; $i++) {
    $data = get('http://127.0.0.1:9501/');
    if ($data['code'] == 200) {
        $success++;
    } else {
        $error++;
    }
    echo $i.PHP_EOL;
    usleep(1000000);
}
echo "成功{$success} 失败{$error}".PHP_EOL;