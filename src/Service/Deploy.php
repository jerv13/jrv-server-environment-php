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
    /**
     * @var array
     */
    protected $serverOptions
        = [
            'env' => Env::FILENAME_ENV,
            'env-production' => Env::FILENAME_ENV_PRODUCTION,
            'version' => Version::FILENAME,
            'secrets' => Secrets::FILENAME,
        ];

    /**
     * @var array
     */
    protected $serverFileJsonOptions
        = [
            'env-file-json' => Env::FILENAME_ENV,
            'env-production-file-json' => Env::FILENAME_ENV_PRODUCTION,
            'version-file-json' => Version::FILENAME,
            'secrets-file-json' => Secrets::FILENAME,
        ];

    /**
     * @var array
     */
    protected $serverFilePhpOptions
        = [
            'env-file' => Env::FILENAME_ENV,
            'env-production-file' => Env::FILENAME_ENV_PRODUCTION,
            'version-file' => Version::FILENAME,
            'secrets-file' => Secrets::FILENAME,
        ];

    /**
     * @var string
     */
    protected $dataPath;

    /**
     * @var int
     */
    protected $dataFolderPermissions = Permissions::DEFAULT_FOLDER;

    /**
     * @var int
     */
    protected $dataFilePermissions = Permissions::DEFAULT_FILE;

    /**
     * @param string $dataPath
     * @param int    $dataFolderPermissions
     * @param int    $dataFilePermissions
     */
    public function __construct(
        $dataPath = PathConfig::PATH_DEFAULT,
        $dataFolderPermissions = Permissions::DEFAULT_FOLDER,
        $dataFilePermissions = Permissions::DEFAULT_FILE
    ) {
        $this->dataPath = $dataPath;
        $this->dataFolderPermissions = $dataFolderPermissions;
        $this->dataFilePermissions = $dataFilePermissions;
    }


    /**
     * @param string $sourceFilePath
     * @param array  $params
     *
     * @return string
     */
    public function getFileContents(string $sourceFilePath, array $params = [])
    {
        $contents = file_get_contents($sourceFilePath);

        return $this->trim($contents);
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function trim(string $value)
    {
        return trim($value);
    }

    /**
     * @param string $message
     *
     * @return string
     */
    protected function getOutput(string $message)
    {
        return $message . "\n";
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
        $contents = json_decode($contents);

        $err = json_last_error();
        if ($err !== JSON_ERROR_NONE) {
            throw new \Exception('Data file contents must be JSON string');
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
     */
    public function buildDataFile(string $filename, string $contents, array $params = [])
    {
        $this->assertValid($contents);

        $contents = "<?php return " . $contents . ";\n";

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

        $gitignoreData = array_merge($gitignoreData, $gitignoreExtras);

        $gitignore = '';

        foreach ($gitignoreData as $gitignoreEntry) {
            $gitignore = $this->trim($gitignoreEntry) . "/n";
        }

        $output .= $this->buildFile(
            '.gitignore',
            $gitignore
        );

        return $output;
    }

    /**
     * @param string $pathConfig
     * @param string $serverConfigFile
     * @param string $serverConfigKey
     * @param string $pathData
     *
     * @return string
     */
    public function getServerOutput(
        $pathConfig = PathConfig::PATH_DEFAULT,
        $serverConfigFile = Env::SERVER_CONFIG_FILE,
        $serverConfigKey = Env::SERVER_CONFIG_KEY,
        $pathData = PathData::PATH_DEFAULT
    ) {
        // Validate by running server build and showing output
        ServerFactory::build(
            $pathConfig,
            $serverConfigFile,
            $serverConfigKey,
            $pathData
        );

        $server = ServerFactory::getInstance();

        $array = $server->__toArray();

        // hide secrets
        $array['secrets'] = '[*** SECRETS ***]';

        return $this->getOutput("Server state: \n" . json_encode($array, JSON_PRETTY_PRINT));
    }

    /**
     * @param Args  $args
     * @param array $params
     *
     * @return string
     */
    public function main(Args $args, array $params = [])
    {
        $output = $this->getOutput('Validating:');
        $argsArray = $args->__toArray();

        foreach ($argsArray as $key => $value) {
            if (array_key_exists($key, $this->serverFilePhpOptions)) {
                $output .= $this->getOutput("Building {$key} from {$this->serverFilePhpOptions[$key]}");
                $output .= $this->copyDataFile($value, $this->serverFilePhpOptions[$key], $params);
                continue;
            }

            if (array_key_exists($key, $this->serverFileJsonOptions)) {
                $output .= $this->getOutput("Building {$key} from {$this->serverFileJsonOptions[$key]}");
                $contents = $this->getFileContents($this->serverFilePhpOptions[$key]);
                $output .= $this->buildDataFile($value, $contents, $params);
                continue;
            }

            if (array_key_exists($key, $this->serverOptions)) {
                $output .= $this->getOutput("Building {$key} from {$this->serverOptions[$key]}");
                $output .= $this->buildDataFile($value, $this->serverOptions[$key], $params);
                continue;
            }
        }

        $output .= $this->buildGitIgnore($args->get('gitignore-entries'));

        return $output;
    }
}
