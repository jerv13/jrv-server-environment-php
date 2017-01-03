<?php

namespace Jerv\Server;

use Jerv\Server\Service\ServerFactory;

/**
 * Class EnvConfig
 *
 * @author    James Jervis
 * @license   License.txt
 * @link      https://github.com/jerv13
 */
class EnvConfig
{
    /**
     * glob
     *
     * @param $pattern
     *
     * @return array
     */
    protected function glob($pattern)
    {
        return glob($pattern, GLOB_BRACE);
    }

    /**
     * getPattern
     *
     * @return string
     */
    protected function getPattern()
    {
        $server = ServerFactory::getInstance();

        $env = $server->getEnv();
        $path = $server->getConfigPath();

        return "{$path}/{$env}/*.php";
    }

    /**
     * __invoke
     *
     * @return array
     */
    public function __invoke()
    {
        $pattern = $this->getPattern();

        foreach ($this->glob($pattern) as $file) {
            yield include $file;
        }
    }
}
