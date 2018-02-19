<?php

namespace Herrera\Wise\Tests\Loader;

use Herrera\Wise\Loader\LoaderResolver;
use Herrera\Wise\Resource\ResourceCollector;
use Herrera\Wise\Tests\Loader\ExampleFileLoader;
use Herrera\Wise\Wise;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;

/**
 * @covers \Herrera\Wise\Loader\LoaderResolver
 * @covers \Herrera\Wise\Resource\ResourceCollector
 */
class LoaderResolverTest extends TestCase
{
    /**
     * @var ResourceCollector
     */
    private $collector;

    /**
     * @var LoaderResolver
     */
    private $resolver;

    /**
     * @var Wise
     */
    private $wise;

    protected function setUp()
    {
        $this->collector = new ResourceCollector();
        $this->resolver = new LoaderResolver();
        $this->wise = new Wise();
    }

    public function testAddLoader()
    {
        $this->resolver->setResourceCollector($this->collector);
        $this->resolver->setWise($this->wise);

        $loader = new ExampleFileLoader(new FileLocator());

        $this->resolver->addLoader($loader);

        $this->assertSame($this->collector, $loader->getResourceCollector());
        $this->assertSame($this->wise, $loader->getWise());
    }

    public function testGetResourceCollector()
    {
        $this->resolver->setResourceCollector($this->collector);

        $this->assertSame(
            $this->collector,
            $this->resolver->getResourceCollector()
        );
    }

    public function testGetWise()
    {
        $this->resolver->setWise($this->wise);

        $this->assertSame($this->wise, $this->resolver->getWise());
    }

    public function testSetResourceCollector()
    {
        $loader = new ExampleFileLoader(new FileLocator());

        $this->resolver->addLoader($loader);
        $this->resolver->setResourceCollector($this->collector);

        $this->assertSame($this->collector, $loader->getResourceCollector());
    }

    public function testSetWise()
    {
        $loader = new ExampleFileLoader(new FileLocator());

        $this->resolver->addLoader($loader);
        $this->resolver->setWise($this->wise);

        $this->assertSame($this->wise, $loader->getWise());
    }
}
