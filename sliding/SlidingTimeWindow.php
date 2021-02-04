<?php

declare(strict_types=1);

class SlidingTimeWindow
{
    /**
     * @var int  单位时间分割多少块
     */
    private $slot;

    /**
     * @var int 单位时间次数
     */
    private $limit;

    /**
     * @var 时间单元
     */
    private $timeUnit;

    /**
     * @var Node
     */
    private $lastNode = null;

    /**
     * @var int 每个 slot time
     */
    private $slotTime;
    /**
     * @var \Swoole\Lock
     */
    private $clock;

    /**
     * 窗口总数
     * SlidingTimeWindow constructor.
     * @param int $slot
     * @param int $limit
     * @param int $timeUnit
     */
    public function __construct(int $slot, int $limit, int $timeUnit, \Swoole\Lock $clock)
    {
        $this->slot = $slot;
        $this->limit = $limit;
        $this->timeUnit = $timeUnit;
        $this->init();
        $this->clock = $clock;
    }

    /**
     * 初始化
     * @author guozhu<guozhu@tal.com>
     */
    private function init()
    {
        $currentNode = null;
        $current = $this->msecsTime();
        for ($i = 0; $i < $this->slot; $i++) {
            if ($this->lastNode == null) {
                $node = new Node($current, 0, $i + 1);
                $currentNode = $this->lastNode = $node;
            } else {
                $this->lastNode->setNext(new Node($current, 0, $i + 1));
                $this->lastNode = $this->lastNode->getNext();
            }
        }
        $this->lastNode->setNext($currentNode);
        //每个单元的时间
        $this->slotTime = $this->timeUnit / $this->slot;
    }

    /**
     * 尝试能否访问
     * @return bool
     * @author guozhu<guozhu@tal.com>
     */
    public function tryAcquire()
    {
        $this->clock->lock();
        $this->reset();
        $sum = $this->getSum();

        if ($sum >= $this->limit) {
            $this->clock->unlock();
            return false;
        }
        echo "当前 id " . $this->lastNode->getId() . PHP_EOL;
        echo "单元内访问次数为{$sum}".PHP_EOL;
        $this->lastNode->addCounter();
        $this->clock->unlock();
        return true;

    }

    public function getSum(): int
    {
        $sum = 0;
        $currentNode = $this->lastNode;
        for ($i = 0; $i < $this->slot; $i++) {
            $sum += $currentNode->getCounter();
            $currentNode = $currentNode->getNext();
        }
        return $sum;
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
     * 华东窗口
     * @author guozhu<guozhu@tal.com>
     */
    private function reset()
    {

        $currentTime = $this->msecsTime();
        $time = $this->lastNode->getTime();
        $diff = $currentTime - $time;
        echo "当前单元已经存活:{$diff}".PHP_EOL;
        $count = (int)(($currentTime - $time) / $this->slotTime);
        if ($count > $this->slot) {
            $count = $this->slot;
        }
        $this->_reset($count, $currentTime);
    }

    /**
     * 滑动
     * @param $num
     * @param $currentTime
     * @author guozhu<guozhu@tal.com>
     */
    private function _reset($num, $currentTime)
    {
        if ($num ==  0) {
            return;
        }
        $currentNode = $this->lastNode;
        for ($i = 0; $i < $num; $i++) {
            $currentNode = $currentNode->getNext();
            $currentNode->setTime($currentTime);
            $currentNode->setCounter(0);
            $this->lastNode = $currentNode;
        }
    }
}