<?php

namespace ZendTest\ServiceManager\TestAsset\Magicals;

class ExplicitInvokable
{
    protected $options;

    public function __construct(array $options = null)
    {
        $this->options = $options;
    }
}
