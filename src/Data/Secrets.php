<?php

namespace Jerv\ServerEnvironment\Data;

use Jerv\ServerEnvironment\Exception\ServerException;

/**
 * Class Secrets
 */
class Secrets implements Data
{
    const SECRETS_DEFAULT = '{}';

    const FILENAME = 'secrets.php';

    /**
     * @var bool
     */
    protected static $built = false;

    /**
     * @var array
     */
    protected static $secrets = [];

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
            // we ignore missing secret files
            return;
        }

        $secrets = require($file);

        self::$secrets = json_decode($secrets);
    }

    /**
     * getSecrets
     *
     * @return array
     */
    public static function get()
    {
        self::assertBuilt();

        return self::$secrets;
    }

    /**
     * getValue
     *
     * @param string $key
     *
     * @return null
     */
    public static function getValue($key)
    {
        self::assertBuilt();

        $secrets = self::get();

        if (array_key_exists($key, $secrets)) {
            return $secrets[$key];
        }

        return null;
    }
}
