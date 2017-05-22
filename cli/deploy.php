<?php
/**
 * deploy.php
 */
require_once(__DIR__ . '/autoload.php');

$args = new \Jerv\ServerEnvironment\Service\Args($argv);

if ($args->get('-h', false) || $args->get('--help', false)) {
    echo file_get_contents(__DIR__ . '/../docs/deploy-help');
    exit(0);
}

$deploy = new \Jerv\ServerEnvironment\Service\Deploy(
    $args->get('path-app-data', \Jerv\ServerEnvironment\Data\PathData::PATH_DEFAULT),
    $args->get('permissions-app-data-folder', \Jerv\ServerEnvironment\Service\Permissions::DEFAULT_FOLDER),
    $args->get('permissions-app-data-file', \Jerv\ServerEnvironment\Service\Permissions::DEFAULT_FILE)
);

echo $deploy->main(
    $args
);

echo $deploy->getServerOutput(
    $args->get('path-app-config', \Jerv\ServerEnvironment\Data\PathConfig::PATH_DEFAULT),
    $args->get('server-config-filename', \Jerv\ServerEnvironment\Data\Env::SERVER_CONFIG_FILE),
    $args->get('server-config-key', \Jerv\ServerEnvironment\Data\Env::SERVER_CONFIG_KEY),
    $args->get('path-app-data', \Jerv\ServerEnvironment\Data\PathData::PATH_DEFAULT)
);
