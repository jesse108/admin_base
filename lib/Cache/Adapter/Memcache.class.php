<?php
/**
 * 使用扩展memcache 实现
 * 
 * @author zhaojian jesse_108@163.com
 *
 */
class Cache_Adapter_Memcache implements Cache_Adapter_Model{
	private $_node                  = array();
	private $_nodeData              = array();
	private $_enable                = true;
	private static $_memcache       = array();
	public  static $retryTime       = 1; //链接失败重试次数
	public  static $connectTimeout   = 500;
	
	
	
	public function __construct(){
		if(!class_exists('Memcache',false)){
			$this->_enable = false;
			return false;
		}
	}
	
	public function ini($servers) {
		if(!$this->_enable){
			return false;
		}
		
		foreach ($servers as $index => $server){
			$host = trim($server[0]);
			$port = trim($server[1]);
			$weight = intval($server[2]);
			$weight = $weight > 0 ? $weight : 1;
			$machine = "{$host}:{$port}";
			$this->_node[$machine] = $weight;
		}
	}


	public function get($key) {
		if(!$this->_enable){
			return false;
		}
		$memcache = $this->_getMemcache($key);
		
		if(!$memcache){
			return false;
		}
		$value =  $memcache->get($key);
		
		return $value;
	}


	public function getMulti($keys) {
		if(!$this->_enable){
			return false;
		}
		
		if(!is_array($keys)){
			return false;
		}
		
		$valueArray = array();
		$servers = array();
		
		foreach ($keys as $key){
			$valueArray[$key] = false;
			$server = $this->_lookup($key);
			$servers[$server][] = $key; 
		}
		
		foreach ($servers as $server => $keys){
			$memcache = $this->_getConnectMemcache($server);
			if($memcache){
				$values = $memcache->get($keys);
				foreach ($values as $key => $value){
					$valueArray[$key] = $value;
				}
			}
		}
		
		return $valueArray;
	}


	public function set($key, $value, $expire = 0) {
		if(!$this->_enable){
			return false;
		}
		$memcache = $this->_getMemcache($key);
		
		if(!$memcache){
			return false;
		}
		$result = $memcache->set($key, $value,0,$expire);

		
		return $result;
	}


	public function setMulti($items, $expire = 0) {
		if(!$this->_enable){
			return false;
		}
		
		foreach ($items as $key => $value){
			$this->set($key, $value,$expire);
		}
	}


	public function delete($key, $timeout = 0) {
		if(!$this->_enable){
			return false;
		}
		$memcache = $this->_getMemcache($key);
		
		if(!$memcache){
			return false;
		}
		$result  = $memcache->delete($key,$timeout);
		return $result;
	}


	public function deleteMulti($keys, $timeout = 0) {
		if(!$this->_enable){
			return false;
		}
		
		foreach ($keys as $key){
			$this->delete($key,$timeout);
		}
	}
	
	public function getStatus(){
		if(!$this->_enable){
			return false;
		}
		$memcacheList = $this->_getAllMemcache();
		
		$statusInfo = array();
		foreach ($memcacheList as $key => $memcache){
			if($memcache){
				$statusInfo[$key] = $memcache->getStats();
			} else {
				$statusInfo[$key] = false;
			}
		}
		return $statusInfo;
	}

	public function removeServer($server){
		unset($this->_node[$server]);
	}
	
	
	/**
	 * 通过cache键值 获取对应的memcache 对象
	 * 
	 * @param string $key cache键
	 * @return memcache 对象
	 */
	private function _getMemcache($key)
	{
		if(empty($this->_node)){  //服务器接口为空 
			return false;
		}
		
		$tryTime = self::$retryTime + 1;
		
		for($i=0; $i < $tryTime; $i++){
			$server = $this->_lookup($key);
			$memcache = $this->_getConnectMemcache($server);
			if($memcache){
				return $memcache;
			}
		}
		return false;
	}
	
	/**
	 * 通过 server 获取memcache实例
	 * @param string $server   host:port
	 * @return multitype:
	 */
	private function _getConnectMemcache($server){
		if(!$server){
			return false;
		}
		list($host, $port) = explode(":", $server);
		$_memcache_host_key = $host .'_'. $port;
		if(!self::$_memcache[$_memcache_host_key]){
			$memcache = new Memcache();
			if(!$memcache->connect($host,$port)){
				//链接失败
				self::$_memcache[$_memcache_host_key]='';
				$this->removeServer($server);//摘掉节点
				return false;
			} else {
				//链接成功
				$memcache->setCompressThreshold(409600, 0.2);
				self::$_memcache[$_memcache_host_key] = $memcache;
			}
		}
		return self::$_memcache[$_memcache_host_key];
	}
	
	/**
	 * 查找对应服务器
	 * 
	 * @param string $resource cache的键
	 * @return string 返回对应的服务器信息
	 */
	private function _lookup($resource){
		if(empty($this->_node)){
			return false;
		}
		
		$selectServer = '';
		$selectWeight = null;
		
		foreach ($this->_node as $server => $weight) {
			$weight = intval($weight);
			$weight = $weight > 0 ? $weight : 1;
			$currentWeight = "{$server}_{$resource}";
			$currentWeight = md5($currentWeight);
			$currentWeight = sprintf("%u", crc32($currentWeight));
			$currentWeight = floatval($currentWeight) / $weight;
			if($selectWeight === null || $selectWeight > $currentWeight){
				$selectWeight = $currentWeight;
				$selectServer = $server;
			}
		}
		return $selectServer;	
	}
	
	/**
	 * 加载所有的memcache 服务
	 */
	private function _getAllMemcache(){
		$memcacheArray = array();
		foreach ($this->_node as $key => $weight){
			list($host, $port) = explode(":", $key);
			$_memcache_host_key = $host .'_'. $port;
			if (!self::$_memcache[$_memcache_host_key]){
				$memcache = new Memcache();
				if (!$memcache->connect($host, $port)) {
					self::$_memcache[$_memcache_host_key]='';
				} else {
					$memcache->setCompressThreshold(409600, 0.2);
					self::$_memcache[$_memcache_host_key] = $memcache;
				}
			}
			$memcacheArray[$_memcache_host_key] = self::$_memcache[$_memcache_host_key];
		}
		return $memcacheArray;
	}
	
	public function getServer($key){
		return $this->_lookup($key);
	}
}