<?php

class image{

	private $path;

	public function __construct($path){
		$this->path = $path;
	}

	public function checkType(){
		//Check the file extension.

		$authorized_ext = array('jpg','jpeg','png','bmp','gif');

		$ext = pathinfo($this->path,PATHINFO_EXTENSION);


		if(in_array($ext,$authorized_ext))
			return TRUE;
		else
			return FALSE;
	}
}