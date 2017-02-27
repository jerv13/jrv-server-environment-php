<?php

namespace Jerv\ServerEnvironment\Middleware;

use Jerv\ServerEnvironment\Data\Env;
use Jerv\ServerEnvironment\Data\PathConfig;
use Jerv\ServerEnvironment\Data\PathData;
use Jerv\ServerEnvironment\Service\ServerFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Bootstrap
 *
 * @author    James Jervis
 * @license   License.txt
 * @link      https://github.com/jerv13
 */
class Bootstrap
{
    /**
     * @var string
     */
    protected $pathConfig = null;

    /**
     * @var string
     */
    protected $serverConfigFile = null;

    /**
     * @var string
     */
    protected $serverConfigKey;

    /**
     * @var string
     */
    protected $pathData = null;

    /**
     * Constructor.
     *
     * @param string $pathConfig
     * @param string $serverConfigFile
     * @param string $serverConfigKey
     * @param string $pathData
     */
    public function __construct(
        $pathConfig = PathConfig::PATH_DEFAULT,
        $serverConfigFile = Env::SERVER_CONFIG_FILE,
        $serverConfigKey = Env::SERVER_CONFIG_KEY,
        $pathData = PathData::PATH_DEFAULT
    ) {
        $this->pathConfig = $pathConfig;
        $this->serverConfigFile = $serverConfigFile;
        $this->serverConfigKey = $serverConfigKey;
        $this->pathData = $pathData;
    }

    /**
     * __invoke
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param callable|null     $next
     *
     * @return mixed
     */
    public function __invoke(
        RequestInterface $request,
        ResponseInterface $response,
        callable $next = null
    ) {
        ServerFactory::build(
            $this->pathConfig,
            $this->serverConfigFile,
            $this->serverConfigKey,
            $this->pathData
        );

        return $next($request, $response);
    }
}
