<?php
class ItemTaobaoModelApi extends LibaryModule {
    public function add($array_params,$type) {
		
    	$db				= DB(2);
 		$sth 			= $db->prepare(' insert into top_item_temp ( tu_id,json,status,type,created ) values ( :tu_id,:json,:status,:type,:created ) ' );
 		$array_params['type'] = $type;
 		$rows_effected 	= $sth->execute($array_params);
 		
 		/*
 		try {
 			$db_m			= DB_MONGO(2);
 			$collection		= $db_m->top_item_temp;
   			$collection->insert( $array_params,array("safe" => true) );
		}
		catch (MongoCursorException $e) {
    		echo "error message: ".$e->getMessage()."\n";
    		echo "error code: ".$e->getCode()."\n";
		}catch (Exception $e) {
    		echo  $e;
		}
		*/
	
 		return 			$rows_effected;

    }


}