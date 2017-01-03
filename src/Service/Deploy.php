<?php

namespace Jerv\Server\Service;

use Jerv\Server\Data\Env;
use Jerv\Server\Data\Secrets;
use Jerv\Server\Data\Version;

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
     * @var Server
     */
    protected $server;

    /**
     * @var int
     */
    protected $dataFolderPermissions = 0755;

    /**
     * @var int
     */
    protected $dataFilePermissions = 0655;

    /**
     * Constructor.
     *
     * @param Server $server
     * @param int    $dataFolderPermissions
     * @param int    $dataFilePermissions
     */
    public function __construct(
        Server $server,
        $dataFolderPermissions = 0755,
        $dataFilePermissions = 0655
    ) {
        $this->server = $server;
        $this->dataFolderPermissions = $dataFolderPermissions;
        $this->dataFilePermissions = $dataFilePermissions;
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
        $dataPath = $this->server->getDataPath();

        if (!file_exists($dataPath)) {
            mkdir($dataPath, $this->dataFolderPermissions);
        }

        $file = $dataPath . '/' . $filename;

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
        $this->buildDataFile(
            Env::FILENAME_ENV,
            trim($env)
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
        $this->buildDataFile(
            Env::FILENAME_ENV_PRODUCTION,
            trim($evnProduction)
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
        $this->buildDataFile(
            Version::FILENAME,
            trim($version)
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

        $contents = '<?php return ' . $contents . ";\n";

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
        $this->buildDataFile(
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
