<?php

class ModelModule extends LibaryModule {
	
	public $table_prefix;
	
	public function __construct()
	{
		$this->table_prefix = N('Config')->DB_PREFIX;
		
	}
	
	
}


