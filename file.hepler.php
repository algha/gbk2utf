<?php

/**
 * @package      [classes]
 * @file         [file.helper.php]
 * @author		 algha  [<irada@live.cn>] 
 * @date         Oct 11, 2013  5:29:18 AM
 * @version      version 1.0
 * @copyright    Copyright (c) 2011-2013 ijadkar Inc . (http://ijadkar.cn)
 * @todo		 todo
 */
 
define("DS",DIRECTORY_SEPARATOR);

class file {
	public  $root;
	public  $folder;
	public  $extension = array("jpg","gif","png","ico","swf","dll");
	private $handle;
	
  	function __construct($folder=""){
  		ini_set("max_execution_time", "240");
  		$this->root = substr(dirname(__FILE__),0,-7)."gbk".DS;
   		$this->folder = $this->root.$folder;
  	}
  	
  	public function get_folder(){
  		return $this->folder;
  	}
  	
  	public function set_folder($folder) {
 		$this->folder = $this->root.$folder;
  	} 	
	
	function read_all($root){
		$files = array();
		 if ($handle = opendir($root)) {
		 	while (false !== ($file = readdir($handle))) { 
		        if ($file != '.' && $file != '..') { 
			        if(is_file($root.DS.$file)){
			        	$files[]  = $root.DS.$file;
			        }else if(is_dir($root.DS.$file)){
			        	$files[$file] = $this->read_all($root.DS.$file);
			        }
		        }  
		 	}
		 	 closedir($handle); 
		 }
		 return $files; 
	}
  	 
	function file_to_array($data) {
		$v = "";
 	 	foreach ($data as $key => $value) { 
 	 		if(!is_array($value)){
 	 			$v .= $value."\n";
 	 		}	elseif(is_array($value)){
 	 			$v .= $this->file_to_array($value);
 	 		}		
 	 	} 
 	 	return $v;
	}
	
	function get_files($folder){
  		return explode("\n", $this->file_to_array($this->read_all($this->get_folder().$folder)));
  	}
	
	function get_extension($folder){
		$paths = array();
		foreach ($this->get_files($folder) as $_files){
			if($_files == ""){
				continue;
			}
			$info = pathinfo($_files);
			if(in_array($info["extension"], $paths)){
				continue;
			}
			$paths[] = $info["extension"];
		}
		$paths = $this->remove_array($paths);
		return $paths;
	}
	
	function remove_array($data=array()){
		foreach ($data as $key=>$val){
			if(in_array($val, $this->extension)){
				unset($data[$key]);
				continue;
			}
			$data[$key] = $val;
		}
		return $data;
	}
	
	function to_utf($folder) {
		$files = $this->get_files($folder);
		array_pop($files);
			foreach ($files as $_files){
				$info = pathinfo($_files);
				if(in_array($info["extension"], $this->get_extension($folder))){
				$data = $this->read($_files);
				$data = $this->format($data);
				$data = $this->bom_killer($data);
				$this->reset($_files, $data);
			}
		}
	}
	
	function bom_killer($data){
		$bom[1] = substr($data,0, 1);
		$bom[2] = substr($data,1, 1);
		$bom[3] = substr($data,2, 1);
		if(ord($bom[1])==239 && ord($bom[2])==187 && ord($bom[3]) == 191){
			$data = substr($data, 3);
		}
		return $data;
	}
	
	function format($str){
		return @iconv("GB2312", "UTF-8", $str);
	}
	
	function reset($_file,$data){
		$handle = fopen($_file, "w");
		if($file = fwrite($handle, $data)){
			echo $_file." namlik hujjet yizip boldi.<br />";
		}
		fclose($handle);
	}
	
	function read($file){
		$data = "";
		$handle = fopen($file, "r");
	 	while (!feof($handle)) {
			$data .= fgets($handle);
		 }
		fclose($handle);
		return $data;
	}
	
}
