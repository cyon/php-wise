<?php

namespace Herrera\Wise\Tests\Processor;

use Herrera\Wise\Processor\AbstractProcessor;
use Herrera\Wise\Processor\ProcessorResolver;
use PHPUnit\Framework\TestCase;

class AbstractProcessorTest extends TestCase
{
    /**
     * @var AbstractProcessor
     */
    private $processor;

    /**
     * @var ProcessorResolver
     */
    private $resolver;

    public function testProcess()
    {
        $this->assertSame(
            array('enabled' => false),
            $this->processor->process(array())
        );
    }

    public function testSetResolver()
    {
        $this->processor->setResolver($this->resolver);

        $this->assertSame($this->resolver, $this->processor->getResolver());
    }

    protected function setUp()
    {
        $this->processor = new ExampleProcessor();
        $this->resolver = new ProcessorResolver();
    }
}
