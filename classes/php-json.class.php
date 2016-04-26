<?php

/**
 * Model class for handling the JSON-storage in plain files
 */
class php_json {

	private $_store =  "data/links.json";
	private $_deleted = "data/deleted_links.json";
	
	function __construct(){
		// does the directory exist?
		if ( file_exists("data") == FALSE ) {
			// create new
			mkdir("data");
		}
		
		// does the json store exist?
		if ( file_exists($this->_store) == FALSE ) {
			// create empty
			file_put_contents($this->_store, "[]");
		}

		// does the deleted exist?
		if ( file_exists($this->_deleted) == FALSE ) {
			// create empty
			file_put_contents($this->_deleted, "[]");
		}
	}

	public function get($id="") {
		// public function should not be able to get another file
		$data = $this->_get("");
		
		// only return one specific?
		if ( !empty($id) ) {
			
			for($n=0; $n<count($data); $n++) {
	
				// get row
				$l = $data[$n];
	
				// match id
				if ( $l->id == $id ) {
					// return link
					return $l;
				}
			}	
			
			// not found
			return '"id" was not found';
			
		}
		
		// return data
		return $data;
	}

	/*public function get_deleted() {
		// NOT IMPLEMENTED YET
		
		// public function should not be able to get another file
		return $this->_get($this->_deleted);	
	} */

	private function _get($fn="") {
		// default to normal store
		$fn = ( empty($fn) ? $this->_store : $fn );

		// read file
		$data = file_get_contents($fn);
	
		// return as php variable
		return json_decode($data);
	}

	public function append($new) {
		// validate new
		if ( !is_array($new) ) return 'Not an array';
		if ( !isset($new['id']) ) return 'Missing "id"';
		if ( !isset($new['title']) ) return 'Missing "title"';		
		if ( !isset($new['link']) ) return 'Missing "link"';
		
		// simple sanitize
		$new['id'] = strip_tags($new['id']);
		$new['title'] = strip_tags($new['title']);
		$new['link'] = strip_tags($new['link']);
		
		// more validation
		if ( !is_string($new['id']) ) return '"id" is not a string';
		if ( !is_string($new['title']) ) return '"title" is not a string';		
		if ( !is_string($new['link']) ) return '"link" is not a string';
		
		// read file
		$data = $this->get();
		
		// append
		array_push($data, $new);
		
		// encode as json
		$data = json_encode($data);
		
		// write
		file_put_contents($this->_store, $data);
		
		// all ok
		return true;
	}

	public function delete($id) {
		// simple sanitize
		$id = strip_tags($id);

		// validate
		if ( !is_string($id) ) return '"id" is not a string';

		// read current storage
		$store = $this->get();

		// save new here
		$new_store = array();

		// match id and save for deleted, also generate new data for current
		$old_link = "";
		for($n=0; $n<count($store); $n++) {

			// get row
			$l = $store[$n];

			// match id
			if ( $l->id == $id ) {
				// save for deleted
				$old_link = $l;
			} else {
				// save for new current
				array_push($new_store, $l);
			}
		}

		// not found?
		if ( empty($old_link) ) return '"id" not found';

		// save to deleted ( get all deleted -> add -> json encode -> save file )
		$deleted_store = $this->_get($this->_deleted);
		array_push($deleted_store, $old_link);
		$deleted_store = json_encode($deleted_store);
		file_put_contents($this->_deleted, $deleted_store);

		// update current store
		$new_store = json_encode($new_store);
		file_put_contents($this->_store, $new_store);
	
		// all ok
		return true;
	}

}
