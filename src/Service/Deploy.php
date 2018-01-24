<?php

namespace Jerv\ServerEnvironment\Service;

use Jerv\ServerEnvironment\Data\Env;
use Jerv\ServerEnvironment\Data\PathConfig;
use Jerv\ServerEnvironment\Data\PathData;
use Jerv\ServerEnvironment\Data\Secrets;
use Jerv\ServerEnvironment\Data\Version;

/**
 * @author James Jervis - https://github.com/jerv13
 */
class Deploy
{
    protected $serverOptionValues
        = [
            'env' => [
                'filename' => Env::FILENAME_ENV,
                'label' => 'environment name',
                'isEnv' => true,
            ],
            'env-production' => [
                'filename' => Env::FILENAME_ENV_PRODUCTION,
                'label' => 'production environment name',
            ],
            'version' => [
                'filename' => Version::FILENAME,
                'label' => 'version',
            ],
            'secrets' => [
                'filename' => Secrets::FILENAME,
                'label' => 'secrets',
            ],
        ];

    protected $serverOptions
        = [
            'env' => 'env',
            'env-production' => 'env-production',
            'version' => 'version',
            'secrets' => 'secrets'
        ];

    protected $serverFileJsonOptions
        = [
            'env-file-json' => 'env',
            'env-production-file-json' => 'env-production',
            'version-file-json' => 'version',
            'secrets-file-json' => 'secrets',
        ];

    protected $serverFilePhpOptions
        = [
            'env-file' => 'env',
            'env-production-file' => 'env-production',
            'version-file' => 'version',
            'secrets-file' => 'secrets',
        ];

    protected $dataPath = PathData::PATH_DEFAULT;
    protected $dataFolderPermissions = Permissions::DEFAULT_FOLDER;
    protected $dataFilePermissions = Permissions::DEFAULT_FILE;
    protected $serverConfigFileName = Env::SERVER_CONFIG_FILE;
    protected $serverConfigKey = Env::SERVER_CONFIG_KEY;
    protected $configPath = PathConfig::PATH_DEFAULT;
    protected $configFolderPermissions = Permissions::DEFAULT_FOLDER;
    protected $configFilePermissions = Permissions::DEFAULT_FILE;

    /**
     * @param string $dataPath
     * @param int    $dataFolderPermissions
     * @param int    $dataFilePermissions
     * @param string $serverConfigFileName
     * @param string $serverConfigKey
     * @param string $configPath
     * @param int    $configFolderPermissions
     * @param int    $configFilePermissions
     */
    public function __construct(
        string $dataPath = PathData::PATH_DEFAULT,
        int $dataFolderPermissions = Permissions::DEFAULT_FOLDER,
        int $dataFilePermissions = Permissions::DEFAULT_FILE,
        string $serverConfigFileName = Env::SERVER_CONFIG_FILE,
        string $serverConfigKey = Env::SERVER_CONFIG_KEY,
        string $configPath = PathConfig::PATH_DEFAULT,
        int $configFolderPermissions = Permissions::DEFAULT_FOLDER,
        int $configFilePermissions = Permissions::DEFAULT_FOLDER
    ) {
        $this->dataPath = $dataPath;
        $this->dataFolderPermissions = $dataFolderPermissions;
        $this->dataFilePermissions = $dataFilePermissions;
        $this->serverConfigFileName = $serverConfigFileName;
        $this->serverConfigKey = $serverConfigKey;
        $this->configPath = $configPath;
        $this->configFolderPermissions = $configFolderPermissions;
        $this->configFilePermissions = $configFilePermissions;
    }

    /**
     * @param string $sourceFilePath
     * @param array  $params
     *
     * @return string
     */
    protected function getFileContents(string $sourceFilePath, array $params = [])
    {
        $contents = file_get_contents($sourceFilePath);

        return $this->trim($contents);
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function trim(string $value)
    {
        return trim($value);
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function concat(string $value)
    {
        $maxlen = 16;
        $len = strlen($value);

        if ($len <= $maxlen) {
            return $value;
        }

        return substr($value, 0, $maxlen) . '...';
    }

    /**
     * @param string $message
     *
     * @return string
     */
    protected function getOutput(string $message)
    {
        $message = $message . "\n";

        return $message;
    }

    /**
     * @param string $option
     *
     * @return array
     * @throws \Exception
     */
    protected function getOptionValue(string $option)
    {
        if (!array_key_exists($option, $this->serverOptionValues)) {
            throw new \Exception("Option {$option} is not valid");
        }

        return $this->serverOptionValues[$option];
    }

    /**
     * @param array  $params
     * @param string $key
     * @param null   $default
     *
     * @return mixed|null
     */
    protected function getParam(array $params, string $key, $default = null)
    {
        if (array_key_exists($key, $params)) {
            return $params[$key];
        }

        return $default;
    }

    /**
     * @param string $contents
     *
     * @return void
     * @throws \Exception
     */
    public function assertValid(string $contents)
    {
        json_decode($contents);

        $err = json_last_error();
        if ($err !== JSON_ERROR_NONE) {
            throw new \Exception("Data file contents must be JSON string, received: {$contents}");
        }
    }

    /**
     * @param string $sourceFilePath
     * @param string $destinationFilename
     * @param array  $params
     *
     * @return string
     */
    public function copyDataFile(string $sourceFilePath, string $destinationFilename, array $params = [])
    {
        $contents = $this->getFileContents($sourceFilePath);

        return $this->buildFile($destinationFilename, $contents);
    }

    /**
     * @param string $filename
     * @param string $contents
     * @param array  $params
     *
     * @return string
     * @throws \Exception
     */
    public function buildDataFile(string $filename, string $contents, array $params = [])
    {
        $this->assertValid($contents);

        $contents = "<?php return '{$contents}';";

        return $this->buildFile($filename, $contents);
    }

    /**
     * @param string $filename
     * @param string $contents
     * @param array  $params
     *
     * @return string
     */
    public function buildFile(string $filename, string $contents, array $params = [])
    {
        $dataPath = $this->dataPath;

        if (!file_exists($dataPath)) {
            mkdir($dataPath, $this->dataFolderPermissions);
        }

        $file = realpath($dataPath . '/' . $filename);

        $output = $this->getOutput('Writing contents to ' . $file);

        file_put_contents($file, $contents);

        chmod($file, $this->dataFilePermissions);

        return $output;
    }

    /**
     * @param string $env
     *
     * @return string
     */
    public function buildConfigFile(string $env)
    {
        $output = '';
        $path = $this->configPath . '/' . $env;

        $this->getOutput("Building config file for {$env}");

        if (!file_exists($path)) {
            mkdir($path, $this->configFolderPermissions);
            $pathOutput = realpath($path);
            $output .= $this->getOutput("Created config folder: {$pathOutput}");
        }

        $file = $path . '/' . Env::SERVER_CONFIG_FILE;

        if (!file_exists($file)) {
            $contents = "<?php return ['{$this->serverConfigKey}' => []];";
            file_put_contents($file, $contents);
            chmod($file, $this->configFilePermissions);
            $fileOutput = realpath($file);
            $output .= $this->getOutput("Created config file: {$fileOutput}");
        }

        return $output;
    }

    /**
     * @param string $gitignoreEntries
     *
     * @return string
     */
    public function buildGitIgnore(string $gitignoreEntries)
    {
        $output = $this->getOutput('Preparing .gitignore ');
        $gitignoreData = [
            Secrets::FILENAME
        ];

        $gitignoreEntries = json_decode($gitignoreEntries);

        if (empty($gitignoreEntries)) {
            $gitignoreEntries = [];
        }

        $gitignoreData = array_merge($gitignoreData, $gitignoreEntries);

        $gitignore = '';

        foreach ($gitignoreData as $gitignoreEntry) {
            $gitignore = $this->trim($gitignoreEntry) . "\n";
        }

        $output .= $this->buildFile(
            '.gitignore',
            $gitignore
        );

        return $output;
    }

    /**
     * @return string
     * @throws \Jerv\ServerEnvironment\Exception\ServerException
     */
    public function getServerOutput()
    {
        $output = '';
        $output .= $this->getOutput("Env config file: {$this->serverConfigFileName}");
        $output .= $this->getOutput("Env config key: {$this->serverConfigKey}");

        // Validate by running server build and showing output
        $server = ServerFactory::build(
            $this->configPath,
            $this->serverConfigFileName,
            $this->serverConfigKey,
            $this->dataPath
        );

        $array = $server->__toArray();

        // hide secrets
        $array['secrets'] = '{*** SECRETS ***}';

        $output .= $this->getOutput("Server state: \n" . var_export($array, true));

        return $output;
    }

    /**
     * @param Args  $args
     * @param array $params
     *
     * @return string
     * @throws \Exception
     */
    public function main(Args $args, array $params = [])
    {
        $output = $this->getOutput('START:');
        $argsArray = $args->__toArray();

        foreach ($argsArray as $key => $value) {
            if (array_key_exists($key, $this->serverFilePhpOptions)) {
                $serverOptions = $this->getOptionValue($this->serverFilePhpOptions[$key]);
                $output .= $this->getOutput(
                    "Building {$serverOptions['label']} using arg {$key} using {$value}"
                );
                $output .= $this->copyDataFile($value, $serverOptions['filename'], $params);
                continue;
            }

            if (array_key_exists($key, $this->serverFileJsonOptions)) {
                $serverOptions = $this->getOptionValue($this->serverFileJsonOptions[$key]);
                $output .= $this->getOutput(
                    "Building {$serverOptions['label']} using arg {$key} using {$value}"
                );
                $contents = $this->getFileContents($value);
                $output .= $this->buildDataFile($serverOptions['filename'], $contents, $params);
                continue;
            }

            if (array_key_exists($key, $this->serverOptions)) {
                $serverOptions = $this->getOptionValue($this->serverOptions[$key]);

                $outputValue = $this->concat($value);

                $output .= $this->getOutput(
                    "Building {$serverOptions['label']} using arg {$key} for {$this->serverOptions[$key]} " .
                    "with data: {$outputValue}"
                );

                $output .= $this->buildDataFile($serverOptions['filename'], $value, $params);
                continue;
            }
        }

        // read env file to create config
        $env = Env::getEnvFromFile($this->dataPath);

        $this->buildConfigFile($env);

        $output .= $this->buildGitIgnore($args->get('gitignore-entries', ''));

        return $output;
    }
}
