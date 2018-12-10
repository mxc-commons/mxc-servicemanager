<?php

namespace ZendTest\ServiceManager\TestAsset\Magicals;

use stdClass;

class Magical
{
    protected $object;
    protected $options;

    public function __construct(stdClass $object, array $options = null)
    {
        $this->object = $object;
        $this->options = $options;
    }
}
