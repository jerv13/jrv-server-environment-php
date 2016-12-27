<?php
/**
 * deploy.php
 */
require_once(__DIR__ . '/autoload.php');

$args = new \Jerv\Server\Service\Args($argv);

\Jerv\Server\Service\ServerFactory::build(
    $args->get('path-server-config'),
    $args->get('path-config'),
    $args->get('path-data')
);

$server = \Jerv\Server\Service\ServerFactory::getInstance();

$deploy = new \Jerv\Server\Service\Deploy($server);

$secrets = $args->get('secrets', '{}');

$secrets = json_decode($secrets, true);

if (!is_array($secrets)) {
    // @todo error
    $secrets = [];
}

$deploy->main(
    $args->get('env', \Jerv\Server\Data\Env::ENV_LOCAL),
    $args->get('env-production', \Jerv\Server\Data\Env::ENV_PROD),
    $args->get('version', \Jerv\Server\Data\Version::VERSION_DEFAULT),
    $secrets
);
