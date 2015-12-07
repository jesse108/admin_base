<?php
/**
 * cache 适配接口类
 * 
 * @author zhaojian
 *
 */
interface Cache_Adapter_Model{
	
	/**
	 * 初始化方法 
	 * 
	 * @param array $servers  服务器列表
	 *              $servers = array(
	 *              	array('host',     'port','weight'),
	 *              	array('127.0.0.1', 11211,  12),
	 *              );
	 */
	public function ini($servers);
	
	
	/**
	 * 获取对应键值的缓存
	 * 
	 * @param string $key
	 */
	public function get($key);
	
	
	/**
	 * 批量获取缓存
	 * 
	 * @param array $keys 键列表
	 *              $keys = array(k1,k2,k3);
	 */
	public function getMulti($keys);
	
	/**
	 * 设置缓存
	 * 
	 * @param sting $key 键
	 * @param mix $value 值
	 * @param number $expire 过期时间 单位 s, 如 3600 1小时
	 */
	public function set($key,$value,$expire = 0);
	
	/**
	 * 批量设置缓存
	 * 
	 * @param array $items 键值对列表
	 *              $items = array(
	 *              	k1 => v1,
	 *              	k2 => v2,
	 *              );
	 * @param number $expire
	 */
	public function setMulti($items,$expire = 0);
	
	/**
	 * 删除缓存
	 * 
	 * @param string $key
	 * @param number $timeout 经过这个时间后删除 单位s
	 */
	public function delete($key,$timeout = 0);
	
	/**
	 * 批量删除缓存
	 * 
	 * @param array $keys
	 * 				$keys = array(
	 * 					k1,k2,k3
	 * 				);
	 * @param number $timeout 
	 */
	public function deleteMulti($keys,$timeout = 0);
	
	
	/**
	 * 获取缓存服务器当前的情况
	 */
	public function getStatus();
	
}