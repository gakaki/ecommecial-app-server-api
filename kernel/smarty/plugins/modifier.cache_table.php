<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage PluginsModifier
 */

/**
 * Smarty cache table modifier plugin
 *
 * Type:     modifier<br>
 * Name:     cache table<br>
 * Purpose:  load data from cache table
 *
 * @author Luis Pater <webmaster at idotorg dot org>
 * @param integer $
 * @param string $
 * @param string $
 * @return string
 */
function smarty_modifier_cache_table($int_id, $str_table, $str_default) {
    $array_result = cacheTable($str_table);
    if ($array_result!=false) {
        if (isset(N("Config")->TABLE_PREFIX[$str_table])) {
            if (isset($array_result[$int_id])) {
                return $array_result[$int_id][N("Config")->TABLE_PREFIX[$str_table]."_name"];
            }
            return $str_default;
        }
        return $int_id;
    }
    return $int_id;
}

?>