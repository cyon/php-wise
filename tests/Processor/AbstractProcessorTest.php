<?php

namespace Herrera\Wise\Tests\Processor;

use Herrera\Wise\Processor\AbstractProcessor;
use Herrera\Wise\Processor\ProcessorResolver;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
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

    protected function setUp()
    {
        $this->processor = new ExampleProcessor();
        $this->resolver = new ProcessorResolver();
    }

    public function testProcess()
    {
        $this->assertSame(
            ['enabled' => false],
            $this->processor->process([])
        );
    }

    public function testSetResolver()
    {
        $this->processor->setResolver($this->resolver);

        $this->assertSame($this->resolver, $this->processor->getResolver());
    }
}
