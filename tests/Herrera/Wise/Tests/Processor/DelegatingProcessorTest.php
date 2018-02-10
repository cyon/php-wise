<?php

namespace Herrera\Wise\Tests\Processor;

use Herrera\Wise\Processor\DelegatingProcessor;
use Herrera\Wise\Processor\ProcessorResolver;
use PHPUnit\Framework\TestCase;

class DelegatingProcessorTest extends TestCase
{
    /**
     * @var DelegatingProcessor
     */
    private $processor;

    private $resolver;

    public function testConstruct()
    {
        $this->assertNotNull($this->processor->getResolver());

        $this->assertSame(
            $this->processor->getResolver(),
            $this->resolver
        );
    }

    /**
     * @expectedException \Herrera\Wise\Exception\ProcessorException
     * @expectedExceptionMessage The support() method did not find a processor.
     */
    public function testGetConfigTreeBuilderNoneAvailable()
    {
        $this->processor->getConfigTreeBuilder();
    }

    public function testGetConfigTreeBuilder()
    {
        $this->processor->supports(array(), 'example');

        $this->assertInstanceOf(
            'Symfony\\Component\\Config\\Definition\\Builder\\TreeBuilder',
            $this->processor->getConfigTreeBuilder()
        );
    }

    public function testSupports()
    {
        $this->assertFalse($this->processor->supports('test'));
        $this->assertTrue($this->processor->supports(array(), 'example'));
    }

    protected function setUp()
    {
        $this->resolver = new ProcessorResolver(array(new ExampleProcessor()));
        $this->processor = new DelegatingProcessor($this->resolver);
    }
}
