<?php

class TokenBucketLimiter
{
    /**
     * @var int 令牌桶容量
     */
    private $capacity;
    /**
     * @var int 令牌产生速率
     */
    private $rate;
    /**
     * @var int 令牌数量
     */
    private $tokenAmount;


    public function __construct(int $capacity, int $rate)
    {
        //初始化令牌桶
        $this->capacity = new Swoole\Atomic($capacity);;
        //生产速率
        $this->rate = $rate;
        $this->tokenAmount = new Swoole\Atomic($capacity);
        swoole_timer_tick(500 / $rate, function () {
            $this->tokenAmount->add();
            if ($this->tokenAmount->get() > $this->capacity->get()) {
                $this->tokenAmount->set($this->capacity->get());
            }
        });
    }

    public function tryAcquire(): bool
    {
        if ($this->tokenAmount->get() > 0) {
            $this->tokenAmount->sub();
            return true;
        } else {
            return false;
        }
    }
}