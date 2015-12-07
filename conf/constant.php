<?php
/**
 * 定义常量
 * 一般来说缓存键值前缀定义放在这里
 */

define('CACHE_PERFIX', 'PHP_BASE'); //默认缓存前缀
define('CACHE_TEST', CACHE_PERFIX.'_0001');



////////////Session
define('SESSION_LOGIN_USER_ID', 'session_0001'); //登陆用户
define('SESSION_LOGIN_ADMIN_ID', 'session_0002'); //登陆admin





//////cookie
define('COOKIE_ADMIN_ID', 'cookie_0001'); //cookie 管理员ID
define('COOKIE_ADMIN_INFO','cookie_0002');

//error code
define("ERROR_CODE_SUCESS",22000);
define("ERROR_CODE_FAIL",22001);