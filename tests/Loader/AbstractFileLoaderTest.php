<?php

namespace Herrera\Wise\Tests\Loader;

use ArrayObject;
use Herrera\Wise\Loader\AbstractFileLoader;
use Herrera\Wise\Resource\ResourceCollector;
use Herrera\Wise\Tests\Loader\ExampleFileLoader;
use Herrera\Wise\Wise;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;

/**
 * @covers \Herrera\Wise\Loader\AbstractFileLoader
 */
class AbstractFileLoaderTest extends TestCase
{
    private $dir;

    /**
     * @var ResourceCollector
     */
    private $collector;

    /**
     * @var AbstractFileLoader
     */
    private $loader;

    protected function setUp()
    {
        $this->dir = vfsStream::setup('data');
        $this->collector = new ResourceCollector();
        $this->loader = new ExampleFileLoader(new FileLocator($this->dir->url()));
    }

    public function testGetResourceCollector()
    {
        $this->loader->setResourceCollector($this->collector);

        $this->assertSame(
            $this->collector,
            $this->loader->getResourceCollector()
        );
    }

    public function testGetWise()
    {
        $wise = new Wise();

        $this->loader->setWise($wise);

        $this->assertSame($wise, $this->loader->getWise());
    }

    public function testLoad()
    {
        $data = ['rand' => rand()];
        $directory = [
            'test.php' => '<?php return ' . var_export($data, true) . ';',
        ];
        vfsStream::create($directory, $this->dir);

        $this->loader->setResourceCollector($this->collector);
        $this->assertSame($data, $this->loader->load('test.php'));

        $resources = $this->collector->getResources();

        $this->assertCount(1, $resources);
        $this->assertInstanceOf(
            'Symfony\\Component\\Config\\Resource\\FileResource',
            $resources[0]
        );
    }

    public function testProcessEmpty()
    {
        $this->assertSame([], $this->loader->process(null, 'test.file'));
    }

    /**
     * @expectedException \Herrera\Wise\Exception\ImportException
     * @expectedExceptionMessage The "imports" value is not valid in "test.file".
     */
    public function testProcessInvalidImports()
    {
        $this->loader->process(['imports' => 123], 'test.file');
    }

    /**
     * @expectedException \Herrera\Wise\Exception\ImportException
     * @expectedExceptionMessage One of the "imports" values (#0) is not valid in "test.file".
     */
    public function testProcessInvalidImport()
    {
        $this->loader->process(
            ['imports' => [123]],
            'test.file'
        );
    }

    /**
     * @expectedException \Herrera\Wise\Exception\ImportException
     * @expectedExceptionMessage A resource was not defined for an import in "test.file".
     */
    public function testProcessInvalidImportMissingResource()
    {
        $this->loader->process(
            ['imports' => [[]]],
            'test.file'
        );
    }

    public function testProcess()
    {
        $wise = new Wise();
        $wise->setGlobalParameters(
            [
                'global' => [
                    'value' => 999,
                ],
            ]
        );

        $this->loader->setWise($wise);

        $rand = rand();
        $directory = [
            'one.php' => '<?php return ' . var_export(
                [
                    'imports' => [
                        ['resource' => 'two.php'],
                    ],
                    'global' => '%global.value%',
                    'placeholder' => '%imported.list%',
                    'sub' => [
                        'inline_placeholder' => 'rand: %imported.list.null%%imported.value%',
                    ],
                    '%imported.key%' => 'a value',
                ],
                true
            ) . ';',
            'two.php' => '<?php return ' . var_export(
                [
                    'imported' => [
                        'key' => 'replaced_key',
                        'list' => [
                            'null' => null,
                            'value' => 123,
                        ],
                        'value' => $rand,
                    ],
                ],
                true
            ) . ';',
        ];
        vfsStream::create($directory, $this->dir);

        $this->assertSame(
            [
                'imported' => [
                    'key' => 'replaced_key',
                    'list' => [
                        'null' => null,
                        'value' => 123,
                    ],
                    'value' => $rand,
                ],
                'imports' => [
                    [
                        'resource' => 'two.php',
                    ],
                ],
                'global' => 999,
                'placeholder' => [
                    'null' => null,
                    'value' => 123,
                ],
                'sub' => [
                    'inline_placeholder' => 'rand: ' . $rand,
                ],
                'replaced_key' => 'a value',
            ],
            $this->loader->load('one.php')
        );
    }

    /**
     * @expectedException \Herrera\Wise\Exception\InvalidReferenceException
     * @expectedExceptionMessage The reference "%test.reference%" could not be resolved (failed at "test").
     */
    public function testProcessInvalidReference()
    {
        $this->loader->process(
            [
                'bad_reference' => '%test.reference%',
            ],
            'test.php'
        );
    }

    /**
     * @expectedException \Herrera\Wise\Exception\InvalidReferenceException
     * @expectedExceptionMessage The non-scalar reference "%test.reference%" cannot be used inline.
     */
    public function testProcessNonScalarReference()
    {
        $this->loader->process(
            [
                'bad_reference' => 'bad: %test.reference%',
                'test' => [
                    'reference' => [
                        'value' => 123,
                    ],
                ],
            ],
            'test.php'
        );
    }

    /**
     * @expectedException \Herrera\Wise\Exception\InvalidReferenceException
     * @expectedExceptionMessage The reference "%a.b.c.d%" could not be resolved (failed at "a").
     */
    public function testResolveReferenceInvalid()
    {
        $this->loader->resolveReference('a.b.c.d', []);
    }

    public function testResolveReference()
    {
        $array = [
            'a' => [
                'b' => [
                    'c' => [
                        'd' => 123,
                    ],
                ],
            ],
        ];

        $object = new ArrayObject($array);

        $this->assertSame(
            123,
            $this->loader->resolveReference('a.b.c.d', $array)
        );

        $this->assertSame(
            123,
            $this->loader->resolveReference('a.b.c.d', $object)
        );
    }

    /**
     * @depends testGetResourceCollector
     */
    public function testSetResourceCollector()
    {
        $this->loader->setResourceCollector($this->collector);

        $this->assertSame(
            $this->collector,
            $this->loader->getResourceCollector()
        );
    }

    /**
     * @depends testGetWise
     */
    public function testSetWise()
    {
        $wise = new Wise();

        $this->loader->setWise($wise);

        $this->assertSame($wise, $this->loader->getWise());
    }
}
