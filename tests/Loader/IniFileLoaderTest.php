<?php

namespace Herrera\Wise\Tests\Loader;

use Herrera\Wise\Loader\IniFileLoader;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;

/**
 * @coversNothing
 */
class IniFileLoaderTest extends TestCase
{
    private $dir;

    /**
     * @var IniFileLoader
     */
    private $loader;

    protected function setUp()
    {
        $this->dir = vfsStream::setup('data');
        $this->loader = new IniFileLoader(new FileLocator($this->dir->url()));
    }

    public function testSupports()
    {
        $this->assertTrue($this->loader->supports('test.ini'));
        $this->assertTrue($this->loader->supports('test.ini', 'ini'));
    }

    public function testDoLoad()
    {
        $directory = [
            'test.ini' => <<<'DATA'
[imports]
0[resource] = "import.ini"

[root]
number = 123
imported = "%imported.value%"
DATA
        ,
            'import.ini' => <<<'DATA'
[imported]
value = "imported value"
DATA
        ];
        vfsStream::create($directory, $this->dir);

        $this->assertSame(
            [
                'imported' => [
                    'value' => 'imported value',
                ],
                'imports' => [
                    ['resource' => 'import.ini'],
                ],
                'root' => [
                    'number' => '123',
                    'imported' => 'imported value',
                ],
            ],
            $this->loader->load('test.ini')
        );
    }
}
