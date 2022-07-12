<?php
/*
* The code below made by MPHH
* For PHP V. 4.2.0
*/
@reset($HTTP_GET_VARS); 
while(list($key, $val) = @each($HTTP_GET_VARS)) 
$$key = $val;
@reset($HTTP_POST_VARS); 
while(list($key, $val) = @each($HTTP_POST_VARS)) 
$$key = $val;
@reset($HTTP_COOKIE_VARS); 
while(list($key, $val) = @each($HTTP_COOKIE_VARS)) 
$$key = $val;
?>