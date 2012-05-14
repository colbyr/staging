<?php 
/** 
 * Smarty plugin 
 * @package Smarty 
 * @subpackage plugins 
 */ 


/** 
 * Smarty escape_quotes modifier plugin. 
 * 
 * Type:     modifier<br /> 
 * Name:     escape_quotes<br /> 
 * Purpose:  Escape both double and single quotes. 
 * @author bjoshua 
 * @link http://www.phpinsider.com/smarty-forum/viewtopic.php?p=22818 
 * @param string $string 
 * @version $Revision: 1.1.1 $ 
 * @return string 
 */ 
function smarty_modifier_escape_quotes($string) { 
   return strtr($string, array('"' => '&quot;', '\'' => '\\\'')); 
} 

?>