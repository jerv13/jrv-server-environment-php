<?php

namespace Jerv\Server\Service;

use Interop\Container\ContainerInterface;
use Jerv\Server\Data\Env;
use Jerv\Server\Data\PathConfig;
use Jerv\Server\Data\PathData;
use Jerv\Server\Data\PathServerConfig;
use Jerv\Server\Data\Secrets;
use Jerv\Server\Data\Version;
use Jerv\Server\Exception\ServerException;

/**
 * Class ServerFactory
 *
 * @author    James Jervis
 * @license   License.txt
 * @link      https://github.com/jerv13
 */
class ServerFactory
{
    /**
     * @var Server
     */
    protected static $instance = null;

    /**
     * build - Done on bootstrap
     *
     * @param string|null $pathServerConfig
     * @param string|null $pathConfig
     * @param string|null $pathData
     *
     * @return void
     */
    public static function build(
        $pathServerConfig = null,
        $pathConfig = null,
        $pathData = null
    ) {

        if (!empty(self::$instance)) {
            return;
        }

        PathServerConfig::build($pathServerConfig);
        PathConfig::build($pathConfig);
        PathData::build($pathData);

        $pathData = PathData::get();
        $pathServerConfig = PathServerConfig::get();
        $pathConfig = PathConfig::get();

        Env::build(
            $pathData,
            $pathServerConfig,
            $pathConfig
        );

        Secrets::build(
            $pathData
        );

        Version::build(
            $pathData
        );

        self::$instance = new Server(
            $pathData,
            $pathServerConfig,
            $pathConfig,
            Env::isProduction(),
            Env::get(),
            Env::getServerVars(),
            Secrets::get(),
            Version::get()
        );
    }

    /**
     * getInstance
     *
     * @return Server
     * @throws ServerException
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            throw new ServerException('No _server instance built');
        }

        return self::$instance;
    }

    /**
     * __invoke
     *
     * @param ContainerInterface|null $container
     *
     * @return Server
     */
    public function __invoke($container = null)
    {
        return self::getInstance();
    }
}
