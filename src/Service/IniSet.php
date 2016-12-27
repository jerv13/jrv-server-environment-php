<?php

namespace Jerv\Server\Service;

/**
 * Class IniSet
 *
 * @author    James Jervis
 * @license   License.txt
 * @link      https://github.com/jerv13
 */
class IniSet
{
    /**
     * build
     *
     * @param array $initSet
     *
     * @return void
     */
    public static function build(array $initSet = [])
    {
        foreach ($initSet as $key => $value) {
            ini_set($key, $value);
        }
    }
}
