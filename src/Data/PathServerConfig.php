<?php

namespace Jerv\Server\Data;

use Jerv\Server\Exception\ServerException;

/**
 * Class PathServerConfig
 */
class PathServerConfig implements Data
{
    const PATH_DEFAULT = __DIR__ . '/../../../../../config/_server';

    /**
     * @var string
     */
    protected static $pathServerConfig;

    /**
     * assertBuilt
     *
     * @return void
     * @throws ServerException
     */
    protected static function assertBuilt()
    {
        if (empty(self::$pathServerConfig)) {
            throw new ServerException(get_class(self::class) . ' must be built on bootstrap');
        }
    }

    /**
     * build
     *
     * @param null $pathServerConfig
     *
     * @return void
     * @throws ServerException
     */
    public static function build($pathServerConfig = null)
    {
        if (!empty(self::$pathServerConfig)) {
            // Only build once
            return;
        }

        if (empty($pathServerConfig)) {
            // Build default
            $pathServerConfig = realpath(self::PATH_DEFAULT);
        }

        if (empty($pathServerConfig)) {
            // No Config folder
            throw new ServerException('No _server config folder defined');
        }

        self::$pathServerConfig = $pathServerConfig;
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

        return self::$pathServerConfig;
    }
}
