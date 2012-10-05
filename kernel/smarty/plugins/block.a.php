<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {a} function plugin
 *
 * Type:     function<br>
 * Name:     a<br>
 * Purpose:  handle create html a tag in template<br>
 * @author   Luis Pater <webmaster at idotorg dot org>
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_block_a($params,  $content, $template, &$repeat) {
    //{! a href="/%s_%s_%s.html" content="中文" target="_blank" index1=$array_music.m_name index2=$array_music.m_id index3=$array_music.m_file !}
    if (empty($params['href'])) {
        $template->trigger_error("a: missing href parameter");
        return;
    }

    foreach ($params as $key=>$val) {
        if (strpos($key, "index")!==false) {
            $array_indexs[$key] = $val;
            unset($params[$key]);
        }
    }
    if (count($array_indexs)) {
        ksort($array_indexs);
    }

    $str_href = $params['href'];
    unset($params['content'], $params['href']);

    foreach ($params as $key=>$val) {
        $array_result[] = $key."='".str_replace("'", "\'", $val)."'";
    }

    if (REWRITE && A('RewriteMap')->getMap($str_href)) {
        if (count($array_result)) {
            $str_result = "<a href='".vsprintf(A('RewriteMap')->getMap($str_href), $array_indexs)."' ".implode(" ", $array_result).">".$content."</a>";
        }
        else {
            $str_result = "<a href='".vsprintf(A('RewriteMap')->getMap($str_href), $array_indexs)."'>".$content."</a>";
        }
    }
    else {
        if (count($array_result)) {
            $str_result = "<a href='".vsprintf($str_href, $array_indexs)."' ".implode(" ", $array_result).">".$content."</a>";
        }
        else {
            $str_result = "<a href='".vsprintf($str_href, $array_indexs)."'>".$content."</a>";
        }
    }

    return $str_result;
}

/* vim: set expandtab: */