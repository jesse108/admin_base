<?php
/**
 * Cache 管理
 * 
 * @author Jesse jesse_108@163.com
 *
 */
class Cache_Manage{
	const CACHE_TYPE_MEMCACHE = 1;
	const CACHE_TYPE_MEMCACHED = 2;
	const CACHE_TYPE_ZCACHE = 3;
	
	private static $_AdapterList = array();
	
	private $_cacheAdapter = null;
	
	/**
	 * 获取实例 目前只支持 memcache 和 memcached  
	 * 以后由新的cache形式可以再添加
	 * 
	 * @param unknown $cacheType
	 */
	public static function getInstance($cacheConfig = null,$cacheType = 0){
		$cacheConfig = $cacheConfig ? $cacheConfig : Config::Get('cache');
		$cacheType = $cacheType ? $cacheType : self::CACHE_TYPE_MEMCACHE;
		
		if(!self::$_AdapterList[$cacheType]){
		    $ini = false;
			switch ($cacheType){
				case self::CACHE_TYPE_MEMCACHED:
				    $cacheAdapter = new Cache_Adapter_Memcached();
				    $ini = true;
				case self::CACHE_TYPE_MEMCACHE:
				default:
				    $cacheAdapter = $ini ? $cacheAdapter : new Cache_Adapter_Memcache();
				    $memcacheConfig = $cacheConfig['memcache'];
				    $servers = $memcacheConfig['server'];
				    $defaultWeight = intval($memcacheConfig['default_weight']);
				    
				    if($cacheAdapter || !$memcacheConfig || !$servers){
    					foreach ($servers as $serverInfo){
    						list($serverStr,$weight) = explode(' ', $serverInfo);
    						list($host,$port) = explode(':', $serverStr);
    						$weight = $weight ? intval($weight) : intval($defaultWeight);
    						$weight = $weight > 0 ? $weight : 1;
    						$server = array($host,$port,$weight);
    						$memecacheServers[] = $server;
    					}
    					$cacheAdapter->ini($memecacheServers);
				    } else {
				        $cacheAdapter = false;
				    }
					break;
			}
			
			$cacheManage = new Cache_Manage($cacheAdapter);
			self::$_AdapterList[$cacheType] = $cacheManage;
		}
		
		return self::$_AdapterList[$cacheType];
	}
	
	
	
	
	
	
	

	public function __construct($cacheAdapter){
		$this->_cacheAdapter = $cacheAdapter;
	}
	
	public function set($key,$value,$expire = 0){
	    if($this->_cacheAdapter){
	        return $this->_cacheAdapter->set($key, $value,$expire);
	    } else {
	        return false;
	    }
		
	}
	
	public function get($key){
	    if($this->_cacheAdapter){
	        return $this->_cacheAdapter->get($key);
	    } else {
	        return false;
	    }
	}
	
	public function setMulti($items,$expire = 0){
	    if($this->_cacheAdapter){
	        return $this->_cacheAdapter->setMulti($items,$expire);
	    } else {
	        return false;
	    }
		
	}
	
	public function getMulti($keys){
	    if($this->_cacheAdapter){
	        return $this->_cacheAdapter->getMulti($keys);
	    } else {
	        return false;
	    }
		
	}
	
	public function delete($key,$timeout = 0){
	    if($this->_cacheAdapter){
	        return $this->_cacheAdapter->delete($key,$timeout);
	    } else {
	        return false;
	    }
	}
	
	public function deleteMulti($keys,$timeout = 0){
	    if($this->_cacheAdapter){
	        return $this->_cacheAdapter->deleteMulti($keys,$timeout);
	    } else {
	        return false;
	    }
	}
	
	public function getStatus(){
	    if($this->_cacheAdapter){
	        return $this->_cacheAdapter->getStatus();
	    } else {
	        return false;
	    }
	}
	
	
	///////////////工具性方法
	public static function GenKey($key){
		$hash = dirname(__FILE__);
		$key = md5( $hash . $key );
		return $key;
	}
}