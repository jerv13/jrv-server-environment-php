<?php

namespace Jerv\Server\Data;

use Jerv\Server\Exception\ServerException;

/**
 * Class PathData
 */
class PathData implements Data
{
    const PATH_DEFAULT = __DIR__ . '/../../../../../data/_server';

    /**
     * @var string
     */
    protected static $pathData;

    /**
     * assertBuilt
     *
     * @return void
     * @throws ServerException
     */
    protected static function assertBuilt()
    {
        if (empty(self::$pathData)) {
            throw new ServerException(get_class(self::class) . ' must be built on bootstrap');
        }
    }

    /**
     * build
     *
     * @param null $pathData
     *
     * @return void
     */
    public static function build($pathData = null)
    {
        if (!empty(self::$pathData)) {
            // Only build once
            return;
        }

        if (empty($pathData)) {
            // Build default
            $pathData = realpath(self::PATH_DEFAULT);
        }

        self::$pathData = $pathData;
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

        return self::$pathData;
    }
}
