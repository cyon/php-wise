<?php

namespace Herrera\Wise\Tests\Exception;

use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
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
