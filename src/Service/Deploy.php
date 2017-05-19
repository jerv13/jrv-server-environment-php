<?php

namespace Jerv\ServerEnvironment\Service;

use Jerv\ServerEnvironment\Data\Env;
use Jerv\ServerEnvironment\Data\PathConfig;
use Jerv\ServerEnvironment\Data\Secrets;
use Jerv\ServerEnvironment\Data\Version;

/**
 * Class Deploy
 *
 * @author    James Jervis
 * @license   License.txt
 * @link      https://github.com/jerv13
 */
class Deploy
{
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
     * @param string $message
     *
     * @return void
     */
    protected function output($message)
    {
        echo $message . "\n";
    }

    /**
     * buildDataFile
     *
     * @param string $filename
     * @param string $contents
     *
     * @return void
     */
    protected function buildDataFile($filename, $contents)
    {
        $contents = "<?php return " . $contents . ";\n";

        $this->buildFile($filename, $contents);
    }

    /**
     * @param $filename
     * @param $contents
     *
     * @return void
     */
    protected function buildFile($filename, $contents)
    {
        $dataPath = $this->dataPath;

        if (!file_exists($dataPath)) {
            mkdir($dataPath, $this->dataFolderPermissions);
        }

        $file = realpath($dataPath . '/' . $filename);

        $this->output('Writing contents to ' . $file);

        file_put_contents($file, $contents);
    }

    /**
     * buildEnvFile
     *
     * @param string $env
     *
     * @return void
     */
    public function buildEnvFile($env)
    {
        $env = "'" . trim($env) . "'";
        $this->output('Preparing evn ' . substr($env, 0, 4) . '... ');
        $this->buildDataFile(
            Env::FILENAME_ENV,
            $env
        );
    }

    /**
     * buildEnvProductionFile
     *
     * @param string $evnProduction
     *
     * @return void
     */
    public function buildEnvProductionFile($evnProduction)
    {
        $evnProduction = "'" . trim($evnProduction) . "'";
        $this->output('Preparing env production ' . substr($evnProduction, 0, 4) . '... ');
        $this->buildDataFile(
            Env::FILENAME_ENV_PRODUCTION,
            $evnProduction
        );
    }

    /**
     * buildVersionFile
     *
     * @param string $version
     *
     * @return void
     */
    public function buildVersionFile($version)
    {
        $version = "'" . trim($version) . "'";
        $this->output('Preparing version ' . substr($version, 0, 4) . '... ');
        $this->buildDataFile(
            Version::FILENAME,
            $version
        );
    }

    /**
     * buildSecretsFile
     *
     * @param array $secrets
     *
     * @return void
     */
    public function buildSecretsFile(array $secrets)
    {
        $contents = var_export($secrets, true);

        $output = json_encode($secrets);

        $this->output('Preparing secrets ' . substr($output, 0, 10) . '... ');

        $this->buildDataFile(
            Secrets::FILENAME,
            trim($contents)
        );
    }

    /**
     * buildGitIgnore
     *
     * @return void
     */
    public function buildGitIgnore()
    {
        $this->output('Preparing .gitignore ');
        $this->buildFile(
            '.gitignore',
            Secrets::FILENAME
        );
    }

    /**
     * main
     *
     * @param string $env
     * @param string $evnProduction
     * @param string $version
     * @param array  $secrets
     *
     * @return void
     */
    public function main(
        $env = Env::ENV_LOCAL,
        $evnProduction = Env::ENV_PROD,
        $version = Version::VERSION_DEFAULT,
        $secrets = []
    ) {
        $this->buildEnvFile($env);
        $this->buildEnvProductionFile($evnProduction);
        $this->buildVersionFile($version);
        $this->buildSecretsFile($secrets);
        $this->buildGitIgnore();
    }
}
