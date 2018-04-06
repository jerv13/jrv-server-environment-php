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

    protected static $built = false;
    protected static $secrets = [];

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
            // we ignore missing secret files
            return;
        }

        $secrets = require($file);

        self::$secrets = json_decode($secrets, true);
    }

    /**
     * @return array
     * @throws ServerException
     */
    public static function get()
    {
        self::assertBuilt();

        return self::$secrets;
    }

    /**
     * @param $key
     *
     * @return mixed|null
     * @throws ServerException
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
