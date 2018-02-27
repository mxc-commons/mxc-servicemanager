<?php
/**
 * @link      http://github.com/zendframework/zend-servicemanager for the canonical source repository
 * @copyright Copyright (c) 2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\ServiceManager\AbstractFactory;

use ArrayObject;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

use function array_map;
use function array_values;
use function is_array;
use function json_encode;

final class ConfigAbstractFactory implements AbstractFactoryInterface
{
    private $config = null;
    private $dependencies = null;
    private $serviceDependencies = null;


    private function getConfig(\Interop\Container\ContainerInterface $container, $requestedName)
    {
        if (! $container->has('config')) {
            throw new ServiceNotCreatedException('Cannot find a config array in the container.');
        }

        $config = $container->get('config');
        if (! (is_array($config) || $config instanceof ArrayObject)) {
            throw new ServiceNotCreatedException('Config must be an array or an instance of ArrayObject.');
        }

        if (! isset($config[self::class])) {
            throw new ServiceNotCreatedException('Cannot find a `' . self::class . '` key in the config array.');
        }

        $this->dependencies = $config[self::class];
        $this->config = $config;
    }

    /**
     * Factory can create the service if there is a key for it in the config
     *
     * {@inheritdoc}
     */
    public function canCreate(\Interop\Container\ContainerInterface $container, $requestedName)
    {
        $this->getConfig($container, $requestedName);
        return isset($this->dependencies[$requestedName]);
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke(\Interop\Container\ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($this->config === null) {
            $this->getConfig($container, $requestedName);
        }

        if (! is_array($this->dependencies)) {
            throw new ServiceNotCreatedException('Dependencies config must exist and be an array or ArrayObject.');
        }

        if (! isset($this->dependencies[$requestedName])) {
            throw new ServiceNotCreatedException(
                sprintf('Dependencies config must hold a key %s.', $requestedName)
            );
        }

        if (! is_array($this->dependencies[$requestedName])) {
            throw new ServiceNotCreatedException(
                sprintf('Dependencies config for %s must be an array or ArrayObject.', $requestedName)
            );
        }

        $this->serviceDependencies = $this->dependencies[$requestedName];
        if ($this->serviceDependencies !== array_values(array_map('strval', $this->serviceDependencies))) {
            $problem = json_encode(array_map('gettype', $this->serviceDependencies));
            throw new ServiceNotCreatedException(
                'Service message must be an array of strings, ' . $problem . ' given'
            );
        }
        $arguments = array_map([$container, 'get'], $this->serviceDependencies);
        return new $requestedName(...$arguments);
    }
}
