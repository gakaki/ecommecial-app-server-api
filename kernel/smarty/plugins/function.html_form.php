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
 * @author   Rice
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_html_form($params, $template) {
	
    if (!is_object($params['ref'])) {
        $template->trigger_error("html_form: missing output parameter");
        return;
    }


	$form = $params['ref'];
	$output = $form->getHead();
    $output .= '<ul>';
	$content = $form->getContent();
    $hidden_html = '';

	$content_count = count($content);
	if($content_count > 0){
		$table = '<table>';
		foreach($content as $element){
			if('hidden' == $element->getType()) {
                $hidden_html .= $element->getHtml();
                continue;
            }
            $label = (string)$element->getLabel();
			if(!empty($label)) {
				$label .= '：';
			}
            
			$table .= '<tr>
						<td class="bd-al-right" style=" color:#666; text-align:right;">'.$label.'</td>
						<td class="bd-al-left">'.$element->getHtml();
            if('*' == $element->getTipInfo()){
                $table .= '<samp style=" color:#F00; padding-left:5px; font-size:14px;">'.$element->getTipInfo().'</samp>';
            }else{
                $table .= $element->getTipInfo();
            }
            $table .= '     <td>
						</tr>';
		}
		$table .= '</table>';
		$output .= $table;
        $output .= $hidden_html;
	}
    $output .= '</ul>';
	$output .= $form->getFoot();

    return $output;
}

/* vim: set expandtab: */