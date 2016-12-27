<?php

namespace Jerv\Server\Service;

/**
 * Class Args
 *
 * @author    James Jervis
 * @license   License.txt
 * @link      https://github.com/jerv13
 */
class Args
{
    const SEPARATOR = '=';

    /**
     * @var array
     */
    protected $args = [];

    /**
     * Constructor.
     *
     * @param array $args
     */
    public function __construct(
        $args = []
    ) {
        $this->buildArgs($args);
    }

    /**
     * buildArgs
     *
     * @param $args
     *
     * @return void
     */
    protected function buildArgs($args)
    {
        foreach ($args as $key => $arg) {
            $this->buildArg($key, $arg);
        }
    }

    /**
     * buildArg
     *
     * @param $key
     * @param $arg
     *
     * @return void
     */
    protected function buildArg($key, $arg)
    {
        $pos = strpos($arg, self::SEPARATOR);

        if ($pos === false) {
            $this->args[$key] = $arg;

            return;
        }

        $len = strlen($arg);

        $key = substr($arg, 0, $pos - 1);
        $value = substr($arg, $pos + 1, $len);

        $this->args[$key] = $value;
    }

    /**
     * get
     *
     * @param string $key
     * @param null   $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $exists = array_key_exists($key, $this->args);
        if ($exists) {
            return $this->args[$key];
        }

        return $default;
    }
}
