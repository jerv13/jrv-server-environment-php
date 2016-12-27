<?php

namespace Jerv\Server;

use Jerv\Server\Service\Server;
use Jerv\Server\Service\ServerFactory;

/**
 * Class ModuleConfig
 *
 */
class ModuleConfig
{
    /**
     * __invoke
     *
     * @return array
     */
    public function __invoke()
    {
        return [
            'dependencies' => [
                'factories' => [
                    Server::class => ServerFactory::class
                ],
            ],
        ];
    }
}
