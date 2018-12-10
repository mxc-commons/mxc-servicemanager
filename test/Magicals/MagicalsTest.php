<?php

namespace ZendTest\ServiceManager\Magicals;

use PHPUnit\Framework\TestCase;
use stdClass;
use Zend\ServiceManager\Exception\ContainerModificationsNotAllowedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\ServiceManager;
use ZendTest\ServiceManager\TestAsset\Magicals\ExplicitFactory;
use ZendTest\ServiceManager\TestAsset\Magicals\ExplicitInvokable;
use ZendTest\ServiceManager\TestAsset\Magicals\InvokableMagical;
use ZendTest\ServiceManager\TestAsset\Magicals\Magical;

class MagicalsTest extends TestCase
{
    public function testMagicalFactoryIsRegistered()
    {
        $services = new ServiceManager([
            'magicals' => [
                Magical::class,
            ],
        ]);
        self::assertInstanceOf(Magical::class, $services->get(Magical::class));
    }

    public function testMagicalInvokableGetsRegistered()
    {
        $services = new ServiceManager([
            'magicals' => [
                InvokableMagical::class,
            ],
        ]);
        self::assertInstanceOf(InvokableMagical::class, $services->get(InvokableMagical::class));
    }

    public function testUnknownClassGetsIgnored()
    {
        $unknown = 'unknown-class';
        $services = new ServiceManager([
            'magicals' => [
                $unknown,
            ],
        ]);
        self::assertFalse($services->has($unknown));
        self::expectException(ServiceNotFoundException::class);
        $services->get($unknown);
    }

    public function testExistingServicesGetRespected()
    {
        $services = new ServiceManager([
            'services' => [
                Magical::class => new stdClass(),
                InvokableMagical::class => new stdClass(),
            ],
            'magicals' => [
                Magical::class,
                InvokableMagical::class,
            ],
        ]);
        self::assertInstanceOf(stdClass::class, $services->get(Magical::class));
        self::assertInstanceOf(stdClass::class, $services->get(InvokableMagical::class));
    }

    public function testExplicitFactoryTakesPrecedence()
    {
        $services = new ServiceManager([
            'factories' => [
                Magical::class => ExplicitFactory::class,
            ],
            'magicals' => [
                Magical::class,
            ],
        ]);
        self::assertInstanceOf(stdClass::class, $services->get(Magical::class));
    }

    public function testExplicitInvokableTakesPrecedence()
    {
        $services = new ServiceManager([
            'invokables' => [
                ExplicitInvokable::class => stdClass::class,
            ],
            'magicals' => [
                ExplicitInvokable::class,
            ],
        ]);
        self::assertInstanceOf(stdClass::class, $services->get(ExplicitInvokable::class));
    }

    public function testAllowOverrideGetsRespected()
    {
        $services = new ServiceManager([
            'services' => [
                Magical::class => new stdClass(),
            ],
        ]);
        self::expectException(ContainerModificationsNotAllowedException::class);
        $services->configure([
            'magicals' => [
                Magical::class,
            ],
        ]);
    }
}
