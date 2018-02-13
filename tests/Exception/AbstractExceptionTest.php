<?php

namespace Herrera\Wise\Tests\Exception;

use PHPUnit\Framework\TestCase;

class AbstractExceptionTest extends TestCase
{
    public function testFormat()
    {
        $this->assertEquals(
            'Test message.',
            Exception::format('%s message.', 'Test')->getMessage()
        );
    }
}
