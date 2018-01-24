<?php

namespace Jerv\ServerEnvironment;

use Jerv\ServerEnvironment\Service\ServerFactory;

/**
 * @author    James Jervis
 * @license   License.txt
 * @link      https://github.com/jerv13
 */
class EnvConfig
{
    /**
     * @param $pattern
     *
     * @return array
     */
    protected function glob($pattern)
    {
        return glob($pattern, GLOB_BRACE);
    }

    /**
     * @return string
     * @throws Exception\ServerException
     */
    protected function getPattern()
    {
        $server = ServerFactory::getInstance();

        $env = $server->getEnv();
        $path = $server->getConfigPath();

        return "{$path}/{$env}/*.php";
    }

    /**
     * @return \Generator
     * @throws Exception\ServerException
     */
    public function __invoke()
    {
        $pattern = $this->getPattern();

        foreach ($this->glob($pattern) as $file) {
            yield include $file;
        }
    }
}
