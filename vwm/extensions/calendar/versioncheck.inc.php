<?php
/*--------------------------------------*/                                                                      
# check version of php for super globals
/*--------------------------------------*/
           
$version_info = explode('.', phpversion());
if ($version_info[0] < 4 || ($version_info[0] > 3 && $version_info[1] < 1)) {
    $_POST = $HTTP_POST_VARS;
    $_GET = $HTTP_GET_VARS;
    $_COOKIE = $HTTP_COOKIE_VARS;
}
?>
