<?php

namespace Jerv\Server\Data;

use Jerv\Server\Exception\ServerException;

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
            $file = $pathData . '/' . self::FILENAME_ENV;
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
            $file = $pathData . '/' . self::FILENAME_ENV_PRODUCTION;
            $productionEnv = trim(fgets(fopen($file, 'r')));
            self::$production = ($productionEnv === self::$env);
        }
    }

    /**
     * buildServerConfig
     *
     * @param string $pathServerConfig
     *
     * @return void
     * @throws ServerException
     */
    protected static function buildServerConfig($pathServerConfig)
    {
        if (!empty(self::$serverConfig)) {
            return;
        }

        $file = $pathServerConfig . '/' . self::$env . '.php';
        if (!file_exists($file)) {
            throw new ServerException('Server config file not found: ' . $file);
        }
        self::$serverConfig = require($file);

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
            $initSet = $serverConfig[self::VARS_KEY];
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
     * @return void
     */
    protected static function buildEvnConfigPath($pathConfig)
    {
        if (empty(self::$envConfigPath)) {
            $path = $pathConfig . '/' . self::$env;
            self::$envConfigPath = $path;
        }
    }

    /**
     * build
     *
     * @param $pathData
     * @param $pathServerConfig
     * @param $pathConfig
     *
     * @return void
     */
    public static function build(
        $pathData,
        $pathServerConfig,
        $pathConfig
    ) {
        self::buildEvn($pathData);
        self::buildProduction($pathData);
        self::buildServerConfig($pathServerConfig);
        self::buildEvnConfigPath($pathConfig);

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
