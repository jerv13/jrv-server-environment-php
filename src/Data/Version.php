<?php

namespace Jerv\ServerEnvironment\Data;

use Jerv\ServerEnvironment\Exception\ServerException;

/**
 * Class Version
 */
class Version implements Data
{
    const VERSION_DEFAULT = '"unknown"';
    const FILENAME = 'version.php';

    protected static $built = false;
    protected static $version = self::VERSION_DEFAULT;

    /**
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

        $version = require($file);

        self::$version = json_decode($version);
    }

    /**
     * @return string
     * @throws ServerException
     */
    public static function get()
    {
        self::assertBuilt();

        return self::$version;
    }
}
