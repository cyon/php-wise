<?php

namespace Herrera\Wise\Tests\Loader;

use Herrera\Wise\Loader\PhpFileLoader;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;

/**
 * @covers \Herrera\Wise\Loader\PhpFileLoader
 */
class PhpFileLoaderTest extends TestCase
{
    private $dir;

    /**
     * @var PhpFileLoader
     */
    private $loader;

    protected function setUp()
    {
        $this->dir = vfsStream::setup('data');
        $this->loader = new PhpFileLoader(new FileLocator($this->dir->url()));
    }

    public function testSupports()
    {
        $this->assertTrue($this->loader->supports('test.php'));
        $this->assertTrue($this->loader->supports('test.php', 'php'));
    }

    public function testDoLoad()
    {
        $directory = [
            'test.php' => <<<'DATA'
<?php return array(
    'imports' => array(
        array('resource' => 'import.php')
    ),
    'root' => array(
        'number' => 123,
        'imported' => '%imported.value%'
    )
);
DATA
        ,
            'import.php' => <<<'DATA'
<?php return array(
    'imported' => array(
        'value' => 'imported value'
    )
);
DATA
        ];

        vfsStream::create($directory, $this->dir);

        $this->assertSame(
            [
                'imported' => [
                    'value' => 'imported value',
                ],
                'imports' => [
                    ['resource' => 'import.php'],
                ],
                'root' => [
                    'number' => 123,
                    'imported' => 'imported value',
                ],
            ],
            $this->loader->load('test.php')
        );
    }
}
