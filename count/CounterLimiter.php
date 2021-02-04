<?php

use Swoole\Atomic;

class CounterLimiter
{
    /**
     * @var int 对打限制
     */
    protected $limit;

    /**
     * @var Atomic 原子计数器
     */
    protected $atomicCount;

    /**
     * CounterLimiter constructor.
     * @param int $windowSize 窗口大小
     * @param int $limit 窗口大小
     */
    public function __construct(int $windowSize = 1000, int $limit = 10)
    {
        $this->limit = $limit;
//        $this->atomicCount = new Atomic(0);
        $this->atomicCount;
        Swoole\Timer::tick($windowSize, function () {
            $this->atomicCount = 0;
        });
    }

    /**
     * 判断是否可以访问
     * @return bool
     * @author guozhu<guozhu@tal.com>
     */
    public function tryAcquire(): bool
    {
        var_dump($this->atomicCount);
        if ($this->atomicCount >= $this->limit) {
            return false;
        } else {
            $this->atomicCount++;
            return true;
        }

    }

}