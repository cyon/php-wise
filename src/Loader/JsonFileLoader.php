<?php

namespace Herrera\Wise\Loader;

use Symfony\Component\Config\FileLocatorInterface;

/**
 * A loader for JSON files.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class JsonFileLoader extends AbstractFileLoader
{
    /**
     * {@inheritDoc}
     */
    public function supports($resource, $type = null)
    {
        return (is_string($resource)
            && ('json' === strtolower(pathinfo($resource, PATHINFO_EXTENSION))))
            && ((null === $type) || ('json' === $type));
    }

    /**
     * @override
     */
    protected function doLoad($file)
    {
        $fileContent = file_get_contents((string) $file);
        return json_decode($fileContent, true);
    }
}
