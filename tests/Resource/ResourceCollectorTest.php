<?php

namespace Herrera\Wise\Tests\Resource;

use Herrera\Wise\Resource\ResourceCollector;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Resource\FileResource;

/**
 * @covers \Herrera\Wise\Resource\ResourceCollector
 */
class ResourceCollectorTest extends TestCase
{
    /**
     * @var ResourceCollector
     */
    private $collector;

    /**
     * @var FileResource
     */
    private $resource;

    protected function setUp()
    {
        $this->collector = new ResourceCollector();
        $this->resource = new FileResource(__FILE__);
    }

    public function testAddResource()
    {
        $this->collector->addResource($this->resource);

        $this->assertSame(
            [$this->resource],
            $this->collector->getResources()
        );
    }

    /**
     * @depends testAddResource
     */
    public function testClearResources()
    {
        $this->collector->addResource($this->resource);
        $this->collector->clearResources();

        $this->assertSame(
            [],
            $this->collector->getResources()
        );
    }

    /**
     * @depends testAddResource
     */
    public function testGetResources()
    {
        $this->collector->addResource($this->resource);

        $this->assertSame(
            [$this->resource],
            $this->collector->getResources()
        );
    }
}
