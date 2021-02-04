<?php

use Swoole\Atomic;

class Node
{
    /**
     * @var float 时间
     */
    private $time;

    /**
     * 计数器
     * @var Atomic
     */
    private $counter;

    /**
     *
     */
    private $next;

    /**
     * @var
     */
    private $id;

    public function __construct($time, $counter, $id)
    {
        $this->time = $time;
        $this->counter = new Swoole\Atomic($counter);
        $this->id = $id;
    }

    public function setNext(Node $next)
    {
        $this->next = $next;
    }

    public function getNext(): Node
    {
        return $this->next;
    }

    /**
     * @return int
     */
    public function getTime(): int
    {
        return $this->time;
    }

    /**
     * @param float $time
     */
    public function setTime(float $time): void
    {
        $this->time = $time;
    }

    /**
     * @return mixed
     */
    public function getCounter(): int
    {
        return $this->counter->get();
    }

    /**
     */
    public function addCounter(): void
    {
        $this->counter->add();
    }

    public function setCounter($value)
    {
        $this->counter->set($value);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }


}