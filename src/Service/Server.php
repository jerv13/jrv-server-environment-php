<?php

namespace Jerv\ServerEnvironment\Service;

use Jerv\ServerEnvironment\Data\Version;
use Jerv\ServerEnvironment\Exception\ServerException;

/**
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
     * @param string $dataPath
     * @param string $configPath
     * @param bool   $isProduction
     * @param string $env
     * @param array  $envVars
     * @param array  $secrets
     * @param string $version
     *
     * @throws ServerException
     */
    public function __construct(
        string $dataPath,
        string $configPath,
        bool $isProduction = true,
        string $env = 'prod',
        array $envVars = [],
        array $secrets = [],
        string $version = Version::VERSION_DEFAULT
    ) {
        $this->setDataPath($dataPath);
        $this->setConfigPath($configPath);
        $this->production = $isProduction;
        $this->env = $env;
        $this->envVars = $envVars;
        $this->secrets = $secrets;
        $this->version = $version;
    }

    /**
     * @param string $dataPath
     *
     * @return void
     * @throws ServerException
     */
    protected function setDataPath($dataPath)
    {
        $dataPath = realpath($dataPath);

        if (empty($dataPath)) {
            throw new ServerException('Data path cannot be empty');
        }

        $this->dataPath = $dataPath;
    }

    /**
     * @return string
     */
    public function getDataPath()
    {
        return $this->dataPath;
    }

    /**
     * @param string $configPath
     *
     * @return void
     * @throws ServerException
     */
    protected function setConfigPath($configPath)
    {
        $configPath = realpath($configPath);

        if (empty($configPath)) {
            throw new ServerException('Config path cannot be empty');
        }

        $this->configPath = $configPath;
    }

    /**
     * @return string
     */
    public function getConfigPath(): string
    {
        return $this->configPath;
    }

    /**
     * @return bool
     */
    public function isProduction(): bool
    {
        return $this->production;
    }

    /**
     * @return string
     */
    public function getEnv(): string
    {
        return $this->env;
    }

    /**
     * @return array
     */
    public function getEnvVars(): array
    {
        return $this->envVars;
    }

    /**
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
     * @return array
     */
    public function getSecrets(): array
    {
        return $this->secrets;
    }

    /**
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
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @return array
     */
    public function __toArray(): array
    {
        $array = get_object_vars($this);

        return $array;
    }
}
