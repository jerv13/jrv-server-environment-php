<?php

namespace Jerv\ServerEnvironment\Service;

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

    const FLAG_PREFIX = '-';

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
        // Flag
        if (substr($arg, 0, 1) === self::FLAG_PREFIX) {
            $this->args[$arg] = true;

            return;
        }

        $pos = strpos($arg, self::SEPARATOR);

        if ($pos === false) {
            $this->args[$key] = $arg;

            return;
        }

        $len = strlen($arg);

        $key = substr($arg, 0, $pos);
        $value = substr($arg, $pos + 1, $len);

        $this->args[$key] = $value;
    }

    public function has($key)
    {
        array_key_exists($key, $this->args);
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

    /**
     * @param $key
     *
     * @return mixed
     */
    public function getRequired($key)
    {
        $exists = array_key_exists($key, $this->args);
        if (!$exists) {
            echo "{$key} is required";
            exit(1);
        }

        return $this->args[$key];
    }
}
