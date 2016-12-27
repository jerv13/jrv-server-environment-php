<?php

namespace Jerv\Server\Data;

use Jerv\Server\Exception\ServerException;

/**
 * Class PathData
 */
class PathConfig implements Data
{
    const PATH_DEFAULT = __DIR__ . '/../../../../../config';

    /**
     * @var string
     */
    protected static $pathConfig;

    /**
     * assertBuilt
     *
     * @return void
     * @throws ServerException
     */
    protected static function assertBuilt()
    {
        if (empty(self::$pathConfig)) {
            throw new ServerException(get_class(self::class) . ' must be built on bootstrap');
        }
    }

    /**
     * build
     *
     * @param null $pathConfig
     *
     * @return void
     * @throws ServerException
     */
    public static function build($pathConfig = null)
    {
        if (!empty(self::$pathConfig)) {
            // Only build once
            return;
        }

        if (empty($pathConfig)) {
            // Build default
            $pathConfig = realpath(self::PATH_DEFAULT);
        }

        if (empty($pathConfig)) {
            // No Config folder
            throw new ServerException('No config folder defined');
        }

        self::$pathConfig = $pathConfig;
    }

    /**
     * get
     *
     * @return null|string
     * @throws ServerException
     */
    public static function get()
    {
        self::assertBuilt();

        return self::$pathConfig;
    }
}
