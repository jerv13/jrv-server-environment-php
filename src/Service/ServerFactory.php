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
     * @param string $pathConfig
     * @param string $serverConfigFile
     * @param string $serverConfigKey
     * @param string $pathData
     *
     * @return void
     */
    public static function build(
        $pathConfig = PathConfig::PATH_DEFAULT,
        $serverConfigFile = Env::SERVER_CONFIG_FILE,
        $serverConfigKey = Env::SERVER_CONFIG_KEY,
        $pathData = PathData::PATH_DEFAULT
    ) {

        if (!empty(self::$instance)) {
            return;
        }

        PathConfig::build($pathConfig);
        PathData::build($pathData);

        $pathData = PathData::get();
        $pathConfig = PathConfig::get();

        Env::build(
            $pathConfig,
            $serverConfigFile,
            $serverConfigKey,
            $pathData
        );

        Secrets::build(
            $pathData
        );

        Version::build(
            $pathData
        );

        self::$instance = new Server(
            $pathData,
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
