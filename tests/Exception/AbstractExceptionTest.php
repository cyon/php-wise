<?php

namespace Herrera\Wise\Tests\Exception;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Herrera\Wise\Exception\AbstractException
 */
class AbstractExceptionTest extends TestCase
{
    public function testFormat()
    {
        $this->assertSame(
            'Test message.',
            Exception::format('%s message.', 'Test')->getMessage()
        );
    }
}
