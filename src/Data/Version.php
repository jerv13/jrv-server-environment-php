<?php

namespace Jerv\Server\Data;

use Jerv\Server\Exception\ServerException;

/**
 * Class Version
 */
class Version implements Data
{
    const VERSION_DEFAULT = 'unknown';

    const FILENAME = 'version';

    /**
     * @var bool
     */
    protected static $built = false;

    /**
     * @var array
     */
    protected static $version = self::VERSION_DEFAULT;

    /**
     * assertBuilt
     *
     * @return void
     * @throws ServerException
     */
    protected static function assertBuilt()
    {
        if (!self::$built) {
            throw new ServerException(get_class(self::class) . ' must be built on bootstrap');
        }
    }

    /**
     * build
     *
     * @return void
     */
    public static function build($pathData)
    {
        if (self::$built) {
            return;
        }

        $file = $pathData . '/' . self::FILENAME;

        self::$built = true;

        if (!file_exists($file)) {
            // we ignore missing version files
            return;
        }

        self::$version = trim(fgets(fopen($file, 'r')));
    }

    /**
     * getSecrets
     *
     * @return array
     */
    public static function get()
    {
        self::assertBuilt();

        return self::$version;
    }
}
