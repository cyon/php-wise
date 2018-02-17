<?php

namespace Herrera\Wise\Tests\Util;

use Herrera\Wise\Util\ArrayUtil;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class ArrayUtilTest extends TestCase
{
    public function testFlatten()
    {
        $this->assertSame(
            [
                'one' => 1,
                'sub.two' => 2,
                'sub.sub.three' => 3,
            ],
            ArrayUtil::flatten(
                [
                    'one' => 1,
                    'sub' => [
                        'two' => 2,
                        'sub' => [
                            'three' => 3,
                        ],
                    ],
                ]
            )
        );
    }

    public function testWalkRecursive()
    {
        $expected = $actual = [
            'one' => [
                'two' => [
                    'three' => [
                        'four' => 'eight',
                        'twelve' => 'thirteen',
                    ],
                    'five' => 'nine',
                ],
                'six' => 'ten',
            ],
            'seven' => 'eleven',
        ];

        ArrayUtil::walkRecursive(
            $actual,
            function (&$value, $key, &$array) {
                if ('four' === $key) {
                    unset($array[$key]);

                    $array['changed'] = $value;
                }
            }
        );

        unset($expected['one']['two']['three']['four']);

        $expected['one']['two']['three']['changed'] = 'eight';

        $this->assertSame($expected, $actual);
    }
}
