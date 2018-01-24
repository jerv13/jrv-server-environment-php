<?php

namespace Jerv\ServerEnvironment\Data;

use Jerv\ServerEnvironment\Exception\ServerException;

/**
 * Class Env
 */
class Env implements Data
{
    const ENV_DEV = '"dev"';
    const ENV_LOCAL = '"local"';
    const ENV_STAGE = '"stage"';
    const ENV_PROD = '"prod"';
    const DEFAULT_ENV = self::ENV_PROD;
    const DEFAULT_ENV_PRODUCTION = self::ENV_PROD;
    const FILENAME_ENV = 'env.php';
    const FILENAME_ENV_PRODUCTION = 'env-production.php';
    const VARS_KEY = 'vars';
    const INIT_SET_KEY = 'init-set';
    const SERVER_CONFIG_FILE = '_server.php';
    const SERVER_CONFIG_KEY = '_server';

    protected static $built = false;
    protected static $env = null;
    protected static $envConfigPath = null;
    protected static $production = null;
    protected static $serverConfig = [];
    protected static $vars = [];

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
    protected static function buildEvn($pathData)
    {

        if (!empty(self::$env)) {
            return;
        }

        self::$env = self::getEnvFromFile($pathData);
    }

    /**
     * @return void
     */
    protected static function buildProduction($pathData)
    {
        if (!empty(self::$production)) {
            return;
        }

        $file = realpath($pathData . '/' . self::FILENAME_ENV_PRODUCTION);

        if (!file_exists($file)) {
            self::$production = (self::DEFAULT_ENV_PRODUCTION === self::$env);

            return;
        }

        $productionEnv = require($file);
        $productionEnv = json_decode($productionEnv);
        self::$production = ($productionEnv === self::$env);
    }

    /**
     * @param string $serverConfigFile
     * @param string $serverConfigKey
     *
     * @return void
     * @throws ServerException
     */
    protected static function buildServerConfig(
        $serverConfigFile = self::SERVER_CONFIG_FILE,
        $serverConfigKey = self::SERVER_CONFIG_KEY
    ) {
        if (!empty(self::$serverConfig)) {
            return;
        }

        if (empty(self::$envConfigPath)) {
            throw new ServerException('envConfigPath must be set');
        }

        $file = realpath(self::$envConfigPath . '/' . $serverConfigFile);

        if (!file_exists($file)) {
            throw new ServerException('Server config file not found: ' . $file);
        }

        $serverConfig = require($file);

        self::$serverConfig = $serverConfig[$serverConfigKey];

        self::buildServerVars();
        self::setInit();
    }

    /**
     * @return void
     */
    protected static function buildServerVars()
    {
        $vars = null;

        $serverConfig = self::$serverConfig;

        if (!is_array($serverConfig)) {
            $serverConfig = [];
        }

        if (array_key_exists(self::VARS_KEY, $serverConfig)) {
            $vars = $serverConfig[self::VARS_KEY];
        }

        if (!is_array($vars)) {
            $vars = [];
        }

        self::$vars = $vars;
    }

    /**
     * @return void
     */
    protected static function setInit()
    {
        $initSet = null;

        $serverConfig = self::$serverConfig;

        if (!is_array($serverConfig)) {
            $serverConfig = [];
        }

        if (array_key_exists(self::INIT_SET_KEY, $serverConfig)) {
            $initSet = $serverConfig[self::INIT_SET_KEY];
        }

        if (!is_array($initSet)) {
            return;
        }

        foreach ($initSet as $key => $value) {
            ini_set($key, $value);
        }
    }

    /**
     * @param $pathConfig
     *
     * @return void
     * @throws ServerException
     */
    protected static function buildEvnConfigPath($pathConfig)
    {
        if (empty(self::$env)) {
            throw new ServerException('env must be set');
        }

        if (empty(self::$envConfigPath)) {
            $path = realpath($pathConfig . '/' . self::$env);
            self::$envConfigPath = $path;
        }
    }

    /**
     * @param string $pathConfig
     * @param string $serverConfigFile
     * @param string $serverConfigKey
     * @param string $pathData
     *
     * @return void
     * @throws ServerException
     */
    public static function build(
        $pathConfig = PathConfig::PATH_DEFAULT,
        $serverConfigFile = self::SERVER_CONFIG_FILE,
        $serverConfigKey = self::SERVER_CONFIG_KEY,
        $pathData = PathData::PATH_DEFAULT
    ) {
        self::buildEvn($pathData);
        self::buildProduction($pathData);
        self::buildEvnConfigPath($pathConfig);
        self::buildServerConfig($serverConfigFile, $serverConfigKey);

        self::$built = true;
    }

    /**
     * @return string
     */
    public static function getEnvFromFile($pathData)
    {
        $file = realpath($pathData . '/' . self::FILENAME_ENV);

        if (!file_exists($file)) {
            $env = self::DEFAULT_ENV;

            return json_decode($env);
        }

        $env = require($file);

        return json_decode($env);
    }

    /**
     * @return string
     * @throws ServerException
     */
    public static function get()
    {
        return self::getEnv();
    }

    /**
     * @return string
     * @throws ServerException
     */
    public static function getEnv():string
    {
        self::assertBuilt();

        return self::$env;
    }

    /**
     * @return bool
     * @throws ServerException
     */
    public static function isProduction():bool
    {
        self::assertBuilt();

        return self::$production;
    }

    /**
     * @return array
     * @throws ServerException
     */
    public static function getServerVars():array
    {
        self::assertBuilt();

        return self::$vars;
    }

    /**
     * @return string
     * @throws ServerException
     */
    public static function getEnvConfigPath(): string
    {
        self::assertBuilt();

        return self::$envConfigPath;
    }
}
