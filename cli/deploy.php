<?php
/**
 * deploy.php
 */
require_once(__DIR__ . '/autoload.php');

$args = new \Jerv\ServerEnvironment\Service\Args($argv);

\Jerv\ServerEnvironment\Service\ServerFactory::build(
    $args->get('path-server-config'),
    $args->get('path-config'),
    $args->get('path-data')
);

$server = \Jerv\ServerEnvironment\Service\ServerFactory::getInstance();

$deploy = new \Jerv\ServerEnvironment\Service\Deploy($server);

$secrets = $args->get('secrets', '{}');

$secrets = json_decode($secrets, true);

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
