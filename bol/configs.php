<?php

/**
 *
 */
class SPPAGELOAD_BOL_Configs
{
    const PLUGINKEY = 'sppageload';
    
    /**
     * Default configurations
     */
    public $defaults = array(
    );
    
    protected static $classInstance = null;
    
    private $configs = null;
    
    private $changes = array();
    
    public static function getInstance() {
        if (null === self::$classInstance) {
            self::$classInstance = new self();
        }
        return self::$classInstance;
    }
    
    protected function __construct() {
        $this->configs = OW::getConfig()->getValues(self::PLUGINKEY);
        if (!is_array($this->configs)) $this->configs = array();
        register_shutdown_function(array(&$this, 'saveConfigs'));
    }
    
    public function get($key) {
        if (!isset($this->configs[$key])) {
            if (isset($this->defaults[$key])) $this->configs[$key] = $this->defaults[$key];
            else throw new Exception("Error Reading Configuration", 1);
        }
        return $this->configs[$key];
    }
    
    public function keyExists($key) {
        return in_array($key, array_keys($this->getValues()));
    }
    
    public function set($key, $value) {
        if (isset($this->configs[$key]) && $this->configs[$key] == $value) return;
        $this->changes[] = $key;
        $this->configs[$key] = $value;
    }
    
    public function searchKey($keyPattern) {
        $matches = array();
        preg_match_all($keyPattern, implode(PHP_EOL, array_keys($this->getValues())), $matches);
        if (is_array($matches) && count($matches) > 0 && is_array($matches[0])) return $matches[0];
        else return false;
    }
    
    public function getValues() {
        return array_merge($this->defaults, $this->configs);
    }
    
    public function saveConfigs() {
        if (!count($this->changes) > 0) return;
        foreach ($this->changes as $key) {
            if (OW::getConfig()->configExists(self::PLUGINKEY, $key)) {
                OW::getConfig()->saveConfig(self::PLUGINKEY, $key, $this->configs[$key]);
            } else {
                OW::getConfig()->addConfig(self::PLUGINKEY, $key, $this->configs[$key]);
            }
        }
    }
}
