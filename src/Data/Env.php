<?php

namespace Jerv\ServerEnvironment\Data;

use Jerv\ServerEnvironment\Exception\ServerException;

/**
 * Class Env
 */
class Env implements Data
{
    const ENV_DEV = 'dev';

    const ENV_LOCAL = 'local';

    const ENV_STAGE = 'stage';

    const ENV_PROD = 'prod';

    const FILENAME_ENV = 'env';

    const FILENAME_ENV_PRODUCTION = 'env-production';

    const VARS_KEY = 'vars';

    const INIT_SET_KEY = 'init-set';

    const SERVER_CONFIG_FILE = '_server.php';

    const SERVER_CONFIG_KEY = '_server';

    /**
     * @var bool
     */
    protected static $built = false;

    /**
     * @var string
     */
    protected static $env = null;

    /**
     * @var string
     */
    protected static $envConfigPath = null;

    /**
     * @var bool
     */
    protected static $production = null;

    /**
     * @var array
     */
    protected static $serverConfig = [];

    /**
     * @var array
     */
    protected static $vars = [];

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
     * buildEvn
     *
     * @return void
     */
    protected static function buildEvn($pathData)
    {
        if (empty(self::$env)) {
            $file = realpath($pathData . '/' . self::FILENAME_ENV);
            self::$env = trim(fgets(fopen($file, 'r')));
        }
    }

    /**
     * buildProduction
     *
     * @return void
     */
    protected static function buildProduction($pathData)
    {
        if (empty(self::$production)) {
            $file = realpath($pathData . '/' . self::FILENAME_ENV_PRODUCTION);
            $productionEnv = trim(fgets(fopen($file, 'r')));
            self::$production = ($productionEnv === self::$env);
        }
    }

    /**
     * buildServerConfig
     *
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
     * buildServerVars
     *
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
     * setInit
     *
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
     * buildEvnConfigPath
     *
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
     * build
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
     * get
     *
     * @return string
     */
    public static function get()
    {
        return self::getEnv();
    }

    /**
     * getEnv
     *
     * @return string
     */
    public static function getEnv()
    {
        self::assertBuilt();

        return self::$env;
    }

    /**
     * isProduction
     *
     * @return bool
     */
    public static function isProduction()
    {
        self::assertBuilt();

        return self::$production;
    }

    /**
     * getConfig
     *
     * @return array
     */
    public static function getServerVars()
    {
        self::assertBuilt();

        return self::$vars;
    }

    /**
     * getEnvConfigPath
     *
     * @return array
     */
    public static function getEnvConfigPath()
    {
        self::assertBuilt();

        return self::$envConfigPath;
    }
}
