<?php

class munin {
	
	static function graph(Closure $block) {
	
		$graph = new MuninGraph;
		$block($graph);
		
		echo $graph;
		
	}

}

class MuninGraph {
	
	static $pre_process = array('scale');
	
	public $config = "";
	public $collectors = array();
	
	public function collector($name, $label=null) {
		$collector = new MuninCollector($this, $name, $label);
		$this->collectors[] = $collector;
		
		return $collector;
	}
	
	public function collect(Closure $block) {
		if (static::in_config_mode()) return;
		
		call_user_func_array($block, $this->collectors);
		
	}
	
	static public function in_config_mode() {
		return in_array("config", $_SERVER['argv']);
	}
	
	public function __set($key, $value) {
		
		if (in_array($key, static::$pre_process)) {
			$value = $this->{$key}($value);
		}
		
		$this->add_config("graph_{$key} $value");
	}
	
	public function __toString() {
		if (! static::in_config_mode()) return "";
		$config = $this->config;
		
		foreach($this->collectors as $collector) {
			$config .= $collector;
		}
		
		return $config;
	}
	
	public function add_config($string) {
		$this->config .= $string . "\n";
	}
	
	private function scale($value) {
		if ($value === true || $value === "yes") {
			return "yes";
		}
		
		return "no";
		
	}
	
}

class MuninCollector {
	
	public $graph = null;
	public $name  = null;
	public $config = "";
	
	public function __construct($graph, $name, $label=null) {
		$this->graph = $graph;
		$this->name  = $name;
		
		if ($label === null) $label = $name;
		$this->label = $label;
	}
	
	public function __set($key, $value) {
		
		if ($key === "value") {
			echo "{$this->name}.{$key} $value\n";
		}
		
		$this->add_config($key, $value);
	}
	
	public function add_config($key, $value) {
		$this->config .= "{$this->name}.{$key} $value\n";
	}
	
	public function __toString() {
		return $this->config;
	}
	
}

?>