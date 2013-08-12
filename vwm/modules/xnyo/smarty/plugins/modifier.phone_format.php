<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty phone_format modifier plugin
 *
 * Type:     modifier<br>
 * Name:     phone_format<br>
 * Purpose:  format phone
 *         
 * @param string
 * @param string
 * @return string
 */
function smarty_modifier_phone_format($string)
{
    preg_match_all("/\d/", $string, $numbers);

    if(empty($numbers[0])){
        return $string;
    }
    $numbers = array_reverse($numbers[0]);
    $phoneNumber = array();
    $i = 0;
    //transform to format using loop for correct number count
    foreach ($numbers as $number) {
        if ($i == 4) {
            $phoneNumber[] = '-';
        }
        if ($i == 7 || $i == 10) {
            $phoneNumber[] = ' ';
        }

        $phoneNumber[] = $number;
        $i++;
    }
    $phoneNumber = array_reverse($phoneNumber);
    $phoneNumber = implode('', $phoneNumber);
    
    return $phoneNumber;
}
?>
