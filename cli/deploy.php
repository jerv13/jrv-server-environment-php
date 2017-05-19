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

function getSecrets(
    \Jerv\ServerEnvironment\Service\Args $args
) {
    $secretsFile = $args->get('secrets-file');

    if (!empty($secretsFile)) {
        $secrets = file_get_contents($secretsFile);
        if ($secrets === false) {
            return [];
        }

        return json_decode($secrets, true);
    }

    $secrets = $args->get('secrets', '{}');

    return json_decode($secrets, true);
}

$secrets = getSecrets($args);

if (!is_array($secrets)) {
    // @todo error
    $secrets = [];
}

$deploy->main(
    $args->get('env', \Jerv\ServerEnvironment\Data\Env::ENV_LOCAL),
    $args->get('env-production', \Jerv\ServerEnvironment\Data\Env::ENV_PROD),
    $args->get('version', \Jerv\ServerEnvironment\Data\Version::VERSION_DEFAULT),
    $secrets
);

\Jerv\ServerEnvironment\Service\ServerFactory::build(
    $args->get('path-app-config', \Jerv\ServerEnvironment\Data\PathConfig::PATH_DEFAULT),
    $args->get('server-config-filename', \Jerv\ServerEnvironment\Data\Env::SERVER_CONFIG_FILE),
    $args->get('server-config-key', \Jerv\ServerEnvironment\Data\Env::SERVER_CONFIG_KEY),
    $args->get('path-app-data', \Jerv\ServerEnvironment\Data\PathData::PATH_DEFAULT)
);

$server = \Jerv\ServerEnvironment\Service\ServerFactory::getInstance();

$array = $server->__toArray();

// hide secrets
$array['secrets'] = '[*** SECRETS ***]';

echo "\nServer state: \n" . json_encode($array, JSON_PRETTY_PRINT);

