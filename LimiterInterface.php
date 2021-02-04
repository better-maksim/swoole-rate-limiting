<?php

interface LimiterInterface
{
    public function allow(): bool;
}