<?php

namespace Herrera\Wise\Tests\Loader;

use PHPUnit\Framework\TestCase;
use Herrera\Wise\Loader\YamlFileLoader;
use org\bovigo\vfs\vfsStream;
use Symfony\Component\Config\FileLocator;

class YamlFileLoaderTest extends TestCase
{
    private $dir;

    /**
     * @var YamlFileLoader
     */
    private $loader;

    public function testSupports()
    {
        $this->assertTrue($this->loader->supports('test.yml'));
        $this->assertTrue($this->loader->supports('test.yml', 'yaml'));
    }

    public function testDoLoad()
    {
        $directory = [
            'test.yml' => <<<'DATA'
imports:
    - { resource: import.yml }

root:
    number: 123
    imported: %imported.value%
DATA
,
            'import.yml' => <<<'DATA'
imported:
    value: imported value
DATA
        ];
        vfsStream::create($directory, $this->dir);

        $this->assertSame(
            array(
                'imported' => array(
                    'value' => 'imported value'
                ),
                'imports' => array(
                    array('resource' => 'import.yml')
                ),
                'root' => array(
                    'number' => 123,
                    'imported' => 'imported value'
                ),
            ),
            $this->loader->load('test.yml')
        );
    }

    protected function setUp()
    {
        $this->dir = vfsStream::setup('data');
        $this->loader = new YamlFileLoader(new FileLocator($this->dir->url()));
    }
}
