<?php

namespace Jerv\Server\Middleware;

use Jerv\Server\Service\ServerFactory;
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
    protected $pathServerConfig = null;

    /**
     * @var string
     */
    protected $pathConfig = null;

    /**
     * @var string
     */
    protected $pathData = null;

    /**
     * Constructor.
     *
     * @param null $pathServerConfig
     * @param null $pathConfig
     * @param null $pathData
     */
    public function __construct(
        $pathServerConfig = null,
        $pathConfig = null,
        $pathData = null
    ) {
        $this->pathServerConfig = $pathServerConfig;
        $this->pathConfig = $pathConfig;
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
            $this->pathServerConfig,
            $this->pathConfig,
            $this->pathData
        );

        return $next($request, $response);
    }
}
