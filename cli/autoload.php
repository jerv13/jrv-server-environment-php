<?php
/**
 * autoload.php
 */
$composerAutoLoad = realpath(__DIR__ . '/../../../../vendor/autoload.php');
if (empty($composerAutoLoad)) {
    echo 'Composer autoload not found and in required';
    exit(1);
}

require __DIR__ . '/../../../../vendor/autoload.php';
