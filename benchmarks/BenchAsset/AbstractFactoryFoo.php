<?php
/**
 * @link      https://github.com/zendframework/zend-servicemanager for the canonical source repository
 * @copyright Copyright (c) 2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @copyright Copyright 2018 maxence operations gmbh, Germany
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendBench\ServiceManager\BenchAsset;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

class AbstractFactoryFoo implements AbstractFactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName === 'foo') {
            return new Foo($options);
        }
        return false;
    }

    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return ($requestedName === 'foo');
    }
}
