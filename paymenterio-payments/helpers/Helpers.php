<?php
/**
 *
 * Paymenterio Payment PHP SDK
 *
 * @author Paymenterio Development Team
 * @version 1.0.0
 * @license MIT
 * @copyright Paymenterio Sp. z o.o.
 *
 * https://paymenterio.pl
 *
 */
class Helpers
{
    public static function addVarToUrl($url, $key, $value)
    {
        if (strpos($url, '?') === false) {
            return ($url .'?'. $key .'='. $value);
        } else {
            return ($url .'&'. $key .'='. $value);
        }
    }
}
