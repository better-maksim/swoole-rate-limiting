<?php

class LeakyBucket
{
    /**
     * @var int 流速
     */
    private $speed;

    /**
     * @var int 桶的大小
     */
    private $burst;

    /**
     * @var int 最后的更新时间
     */
    private $refreshTime = null;

    /**
     *
     */
    private $water;
    /**
     * @var \Swoole\Lock
     */
    private $clock;


    public function __construct(float $speed, $burst, \Swoole\Atomic\Long $water, \Swoole\Atomic\Long $refreshTime, \Swoole\Lock $lock)
    {
        $this->speed = $speed;
        $this->burst = $burst;
        $this->clock = $lock;
        $this->water = $water;
        $this->refreshTime = $refreshTime;
    }
    /**
     * 获取毫秒级实现
     * @return float
     * @author guozhu<guozhu@tal.com>
     */
    private function msecsTime()
    {
        list($msecs, $sec) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($msecs) + floatval($sec)) * 1000);
    }

    /**
     * 尝试是否可以访问
     * @return bool
     * @author guozhu<guozhu@tal.com>
     */
    public function tryAcquire()
    {
        //加锁，避免资源争抢
        $this->clock->lock();
        $currentTime = $this->msecsTime();
        // 先执行漏水，计算剩余水量

        $water = max(0, $this->water->get() - ($currentTime - $this->refreshTime->get()) * $this->speed);
//        echo ("max(0,{$this->water->get()} -  ({$currentTime} - {$this->refreshTime->get()} * {$this->rate}) = {$water}").PHP_EOL;
        $this->water->set($water);
        $this->refreshTime->set($currentTime);

        if ($this->water->get() < $this->burst) {
            $this->water->add();
            $this->clock->unlock();
            return true;
        }
        $this->clock->unlock();

        return false;

    }
}

max(0,

    $this->water->get() - ($currentTime - $this->refreshTime->get()) *
    $this->speed);
