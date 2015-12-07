<?php
/**
 * 默认数据库配置, 必须配置 rw:读写  ro:只读两个数据库
 * 若没有 ro 数据库,将只是用rw数据库
 */


$dbConfig = array();
////主数据库
$dbConfig['rw'] = array(
	'host' => 'localhost',
	'user' => 'test',
	'password' => '123456',
	'name' => 'test', //库名
);


$config['db'] = $dbConfig;