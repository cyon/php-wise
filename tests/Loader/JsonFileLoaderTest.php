<?php

namespace Herrera\Wise\Tests\Loader;

use Herrera\Wise\Loader\JsonFileLoader;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;

class JsonFileLoaderTest extends TestCase
{
    private $dir;

    /**
     * @var JsonFileLoader
     */
    private $loader;

    public function testSupports()
    {
        $this->assertTrue($this->loader->supports('test.json'));
        $this->assertTrue($this->loader->supports('test.json', 'json'));
    }

    public function testDoLoad()
    {
        $directory = [
            'test.json' => <<<'DATA'
{
    "imports": [
        { "resource": "import.json" }
    ],
    "root": {
        "number": 123,
        "imported": "%imported.value%"
    },
    "imported": {
        "value": "imported value"
    }
}
DATA
        ,
            'import.json' => <<<'DATA'
{
    "imported": {
        "value": "imported value"
    }
}
DATA
        ];
        vfsStream::create($directory, $this->dir);

        $this->assertSame(
            array(
                'imported' => array(
                    'value' => 'imported value'
                ),
                'imports' => array(
                    array('resource' => 'import.json')
                ),
                'root' => array(
                    'number' => 123,
                    'imported' => 'imported value'
                ),
            ),
            $this->loader->load('test.json')
        );
    }

    protected function setUp()
    {
        $this->dir = vfsStream::setup('data');
        $this->loader = new JsonFileLoader(new FileLocator($this->dir->url()));
    }
}
