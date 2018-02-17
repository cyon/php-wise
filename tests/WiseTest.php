<?php

namespace Herrera\Wise\Tests;

use Herrera\Wise\Loader\LoaderResolver;
use Herrera\Wise\Loader\PhpFileLoader;
use Herrera\Wise\Resource\ResourceCollector;
use Herrera\Wise\Tests\Processor\TestProcessor;
use Herrera\Wise\Wise;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;

/**
 * @coversNothing
 */
class WiseTest extends TestCase
{
    /**
     * @var string
     */
    private $cache;

    /**
     * @var ResourceCollector
     */
    private $collector;

    /**
     * @var string
     */
    private $dir;

    /**
     * @var PhpFileLoader
     */
    private $loader;

    /**
     * @var TestProcessor
     */
    private $processor;

    /**
     * @var Wise
     */
    private $wise;

    protected function setUp()
    {
        $this->root = vfsStream::setup('root');
        $this->cache = vfsStream::url('root/cache');
        $this->dir = vfsStream::newDirectory('data')->at($this->root);

        $this->collector = new ResourceCollector();
        $this->loader = new PhpFileLoader(new FileLocator($this->dir->url()));
        $this->processor = new TestProcessor();
        $this->wise = new Wise(true);

        $this->loader->setResourceCollector($this->collector);
    }

    public function testConstruct()
    {
        $wise = new Wise();
        $this->assertFalse($wise->isDebugEnabled());
    }

    public function testCreate()
    {
        $directory = [
            'test.php' => <<<'PHP'
<?php return array(
    'root' => array(
        'number' => 123
    )
);
PHP
        ];
        vfsStream::create($directory, $this->dir);

        $wise = Wise::create($this->dir->url(), $this->cache, true);
        $expected = [
            'root' => [
                'number' => 123,
            ],
        ];

        $this->assertSame($expected, $wise->load('test.php', 'php'));
        $this->assertFileExists($this->cache . '/test.php.cache');
        $this->assertFileExists($this->cache . '/test.php.cache.meta');

        /** @var $delegator \Symfony\Component\Config\Loader\DelegatingLoader */
        $delegator = $wise->getLoader();

        /** @var $loaders \Herrera\Wise\Loader\LoaderResolver */
        $resolver = $delegator->getResolver();

        /** @var $loader \Herrera\Wise\Loader\AbstractFileLoader */
        foreach ($resolver->getLoaders() as $loader) {
            $this->assertSame(
                $wise->getCollector(),
                $loader->getResourceCollector()
            );
            $this->assertSame($wise, $loader->getWise());
        }
    }

    public function testIsDebugEnabled()
    {
        $this->assertTrue($this->wise->isDebugEnabled());
    }

    /**
     * @expectedException \Herrera\Wise\Exception\LogicException
     * @expectedExceptionMessage No loader has been configured.
     */
    public function testLoadLoaderNotSet()
    {
        $this->wise->load('test');
    }

    /**
     * @expectedException \Herrera\Wise\Exception\LoaderException
     * @expectedExceptionMessage The resource "123" (test) is not supported by the loader.
     */
    public function testLoadLoaderNotSupported()
    {
        $this->wise->setLoader($this->loader);
        $this->wise->load(123, 'test');
    }

    public function testLoad()
    {
        $directory = [
            'test.php' => <<<'PHP'
<?php return array(
    'root' => array(
        'number' => 123
    )
);
PHP
        ];
        vfsStream::create($directory, $this->dir);

        $expected = [
            'number' => 123,
            'enabled' => false,
        ];

        $this->wise->setLoader($this->loader);

        $this->assertSame(
            [
                'root' => [
                    'number' => 123,
                ],
            ],
            $this->wise->load('test.php', 'php')
        );

        $this->wise->setCacheDir($this->cache);
        $this->wise->setCollector($this->collector);
        $this->wise->setProcessor($this->processor);

        $this->assertSame($expected, $this->wise->load('test.php', 'php'));
        $this->assertFileExists($this->cache . '/test.php.cache');
        $this->assertFileExists($this->cache . '/test.php.cache.meta');

        /** @noinspection PhpIncludeInspection */
        $this->assertSame($expected, require $this->cache . '/test.php.cache');

        $meta = unserialize(
            file_get_contents(
                $this->cache . '/test.php.cache.meta'
            )
        );

        $this->assertCount(1, $meta);
        $this->assertInstanceOf(
            'Symfony\\Component\\Config\\Resource\\FileResource',
            $meta[0]
        );
        vfsStream::newFile('test.php')->at($this->dir);
        touch(
            $this->dir->url() . '/test.php',
            filemtime($this->cache . '/test.php.cache') - 1000
        );

        $this->assertSame($expected, $this->wise->load('test.php', 'php'));
    }

    /**
     * @depends testLoad
     */
    public function testLoadFlat()
    {
        $directory = [
            'test.php' => <<<'PHP'
<?php return array(
    'root' => array(
        'number' => 123
    )
);
PHP
        ];
        vfsStream::create($directory, $this->dir);

        $this->wise->setLoader($this->loader);

        $this->assertSame(
            [
                'root.number' => 123,
            ],
            $this->wise->loadFlat('test.php', 'php')
        );
    }

    public function testLoadWithBasicProcessor()
    {
        $directory = [
            'test.php' => <<<'PHP'
<?php return array(
    'root' => array(
        'number' => 123,
    )
);
PHP
        ];
        vfsStream::create($directory, $this->dir);

        $this->wise->setLoader($this->loader);
        $this->wise->setProcessor(new BasicProcessor());
        $this->assertSame(
            [
                'number' => 123,
                'enabled' => false,
            ],
            $this->wise->load('test.php', 'php')
        );
    }

    /**
     * @expectedException \Herrera\Wise\Exception\ProcessorException
     * @expectedExceptionMessage The resource "test.php" (php) is not supported by the processor.
     */
    public function testLoadNoProcessorSupported()
    {
        $directory = [
            'test.php' => <<<'PHP'
<?php return array(
    'root' => array(
        'number' => 123
    )
);
PHP
        ];
        vfsStream::create($directory, $this->dir);

        $this->wise->setLoader($this->loader);
        $this->wise->setProcessor(new NeverSupportedProcessor());

        $this->wise->load('test.php', 'php', true);
    }

    /**
     * @expectedException \Herrera\Wise\Exception\ProcessorException
     * @expectedExceptionMessage No processor registered to handle any resource.
     */
    public function testLoadNoProcessorRegistered()
    {
        $directory = [
            'test.php' => <<<'PHP'
<?php return array(
    'root' => array(
        'number' => 123
    )
);
PHP
        ];
        vfsStream::create($directory, $this->dir);

        $this->wise->setLoader($this->loader);

        $this->wise->load('test.php', 'php', true);
    }

    public function testSetCacheDir()
    {
        $this->wise->setCacheDir($this->cache);

        $this->assertSame($this->cache, $this->wise->getCacheDir());
    }

    public function testSetCollector()
    {
        $this->wise->setLoader($this->loader);

        $this->wise->setCollector($this->collector);

        $this->assertSame($this->collector, $this->wise->getCollector());
        $this->assertSame(
            $this->collector,
            $this->loader->getResourceCollector()
        );
    }

    public function testSetCollectorDelegator()
    {
        $resolver = new LoaderResolver();
        $loader = new DelegatingLoader($resolver);

        $this->wise->setLoader($loader);

        $this->wise->setCollector($this->collector);

        $this->assertSame($this->collector, $resolver->getResourceCollector());
    }

    public function testSetGlobalParameters()
    {
        $this->wise->setGlobalParameters(['value' => 123]);

        $this->assertSame(
            ['value' => 123],
            $this->wise->getGlobalParameters()
        );
    }

    /**
     * @expectedException \Herrera\Wise\Exception\InvalidArgumentException
     * @expectedExceptionMessage The $parameters argument must be an array or array accessible object.
     */
    public function testSetGlobalParametersInvalid()
    {
        $this->wise->setGlobalParameters(true);
    }

    public function testSetLoader()
    {
        $this->wise->setCollector($this->collector);

        $this->wise->setLoader($this->loader);

        $this->assertSame($this->loader, $this->wise->getLoader());
        $this->assertSame(
            $this->collector,
            $this->loader->getResourceCollector()
        );
        $this->assertSame(
            $this->wise,
            $this->loader->getWise()
        );
    }

    public function setSetLoaderDelegator()
    {
        $this->setPropertyValue($this->wise, 'collector', $this->collector);

        $resolver = new LoaderResolver();
        $loader = new DelegatingLoader($resolver);

        $this->wise->setLoader($loader);

        $this->assertSame($this->collector, $resolver->getResourceCollector());
        $this->assertSame($this->wise, $resolver->getWise());
    }

    public function testSetProcessor()
    {
        $this->wise->setProcessor($this->processor);

        $this->assertSame($this->processor, $this->wise->getProcessor());

        $processor = new BasicProcessor();

        $this->wise->setProcessor($processor);

        $this->assertSame($processor, $this->wise->getProcessor());
    }
}
