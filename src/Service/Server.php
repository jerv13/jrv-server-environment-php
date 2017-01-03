<?php

namespace Jerv\Server\Service;

use Jerv\Server\Data\Version;

/**
 * Class Server
 *
 * @author    James Jervis
 * @license   License.txt
 * @link      https://github.com/jerv13
 */
class Server
{
    /**
     * @var string
     */
    protected $dataPath;

    /**
     * @var string
     */
    protected $serverConfigPath;

    /**
     * @var string
     */
    protected $configPath;

    /**
     * @var bool
     */
    protected $production = true;

    /**
     * @var string
     */
    protected $env = 'prod';

    /**
     * @var array
     */
    protected $envVars = [];

    /**
     * @var array
     */
    protected $secrets = [];

    /**
     * @var string
     */
    protected $version = Version::VERSION_DEFAULT;

    /**
     * Constructor.
     *
     * @param string $dataPath
     * @param string $serverConfigPath
     * @param string $configPath
     * @param bool   $isProduction
     * @param string $env
     * @param array  $envVars
     * @param array  $secrets
     * @param string $version
     */
    public function __construct(
        $dataPath,
        $serverConfigPath,
        $configPath,
        $isProduction = true,
        $env = 'prod',
        $envVars = [],
        $secrets = [],
        $version = Version::VERSION_DEFAULT
    ) {
        $this->dataPath = $dataPath;
        $this->serverConfigPath = $serverConfigPath;
        $this->configPath = $configPath;
        $this->production = $isProduction;
        $this->env = $env;
        $this->envVars = $envVars;
        $this->secrets = $secrets;
        $this->version = $version;
    }

    /**
     * getDataPath
     *
     * @return string
     */
    public function getDataPath()
    {
        return $this->dataPath;
    }

    /**
     * getServerConfigPath
     *
     * @return string
     */
    public function getServerConfigPath(): string
    {
        return $this->serverConfigPath;
    }

    /**
     * getConfigPath
     *
     * @return string
     */
    public function getConfigPath(): string
    {
        return $this->configPath;
    }

    /**
     * isProduction
     *
     * @return bool
     */
    public function isProduction(): bool
    {
        return $this->production;
    }

    /**
     * getEnv
     *
     * @return string
     */
    public function getEnv(): string
    {
        return $this->env;
    }

    /**
     * getEnvVars
     *
     * @return array
     */
    public function getEnvVars(): array
    {
        return $this->envVars;
    }

    /**
     * getEnvVar
     *
     * @param string     $key
     * @param null|mixed $default
     *
     * @return mixed|null
     */
    public function getEnvVar($key, $default = null)
    {
        if (array_key_exists($key, $this->envVars)) {
            return $this->envVars[$key];
        }

        return $default;
    }

    /**
     * getSecrets
     *
     * @return array
     */
    public function getSecrets(): array
    {
        return $this->secrets;
    }

    /**
     * getSecret
     *
     * @param $key
     *
     * @return mixed|null
     */
    public function getSecret($key)
    {
        if (array_key_exists($key, $this->secrets)) {
            return $this->secrets[$key];
        }

        return null;
    }

    /**
     * getVersion
     *
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }
}
