<?php
/**
 * 使用扩展 memcached 实现
 * 
 * @author zhaojian jesse_108@!63.com
 *
 */
class Cache_Adapter_Memcached implements Cache_Adapter_Model{
	private $_memcached = null;
	
	public function __construct($servers = null){
		if(!class_exists('Memcached',false)){
			return false;
		}
		$memcached = new Memcached();
	//	$memcached -> setOption(Memcached::OPT_HASH, Memcached::HASH_CRC); //HASH_CRC有bug
		$memcached -> setOption(Memcached::OPT_DISTRIBUTION, Memcached::DISTRIBUTION_CONSISTENT);
		$this->_memcached = $memcached;
	}
	
	
	public function ini($servers) {
		if(!$this->_memcached){
			return false;
		}
		
		$result = $this->_memcached->addServers($servers);
		return $result;
	}


	public function get($key) {
		if(!$this->_memcached){
			return false;
		}
		
		$value = $this->_memcached->get($key);
		return $value;
	}


	public function getMulti($keys) {
		if(!$this->_memcached){
			return false;
		}
		
		$values = $this->_memcached->getMulti($keys);
		$temp = array();
		foreach ($keys as $key){
			$temp[$key] = $values[$key] ? $values[$key] : false; 
		}
		
		return $temp;
	}


	public function set($key, $value, $expire = 0) {
		if(!$this->_memcached){
			return false;
		}
		
		$expire = intval($expire);
		$result = $this->_memcached->set($key, $value,$expire);
		return $result;
	}

	
	public function setMulti($items, $expire = 0) {
		if(!$this->_memcached){
			return false;
		}
		
		$expire = intval($expire);
		$result = $this->_memcached->setMulti($items,$expire);
		return $result;		
	}


	public function delete($key, $time = 0) {
		if(!$this->_memcached){
			return false;
		}
		
		$result = $this->_memcached->delete($key,$time);
		return $result;
	}


	public function deleteMulti($keys, $time = 0) {
		if(!$this->_memcached){
			return false;
		}
		
		$result = $this->_memcached->deleteMulti($keys, $time);
		return $result;
	}

	public function getStatus(){
		if(!$this->_memcached){
			return false;
		}
		$result = $this->_memcached->getStats();
		return $result;
	}
	
}