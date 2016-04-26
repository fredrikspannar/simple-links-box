<?php

/**
 * Model class for handling the JSON-storage in plain files
 */
class php_json {

	private $_store =  "data/links.json";
	private $_deleted = "data/deleted_links.json";
	
	private $_validate_error = "";
	
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

	private function _validate_and_sanitize($new) {
		
		// reset error
		$this->_validate_error = "";
		
		// validate new
		if ( !is_array($new) ) {
			$this->_validate_error = 'Not an array';
			return false;
		}
		
		if ( !isset($new['id']) ) {
			$this->_validate_error = 'Missing "id"';
			return false;
		}
		
		if ( !isset($new['title']) ) {
			$this->_validate_error = 'Missing "title"';		
			return false;
		}
		
		if ( !isset($new['link']) ) {
			$this->_validate_error = 'Missing "link"';
			return false;
		}
		
		// simple sanitize
		$new['id'] = strip_tags($new['id']);
		$new['title'] = strip_tags($new['title']);
		$new['link'] = strip_tags($new['link']);
		
		// more validation
		if ( !is_string($new['id']) ) { 
			$this->_validate_error = '"id" is not a string';
			return false;
		}
		
		if ( !is_string($new['title']) ) {
			$this->_validate_error = '"title" is not a string';		
			return false;
		}
		
		if ( !is_string($new['link']) ) {
			$this->_validate_error = '"link" is not a string';
			return false;
		}
	
		if ( strpos($new['link'], 'http://') !== 0 && strpos($new['link'], 'https://') !== 0 ) {
			$this->_validate_error = '"link" is not a valid URL';
			return false;
		}	
		
		// else all ok
		return $new;
	}

	public function append($new) {

		// validate
		$new = $this->_validate_and_sanitize($new);
		if ( $new === false ) {
			// return error
			return $this->_validate_error;
		}
		
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


	public function update($upd_link) {
		
		// validate
		$upd_link = $this->_validate_and_sanitize($upd_link);
		if ( $upd_link === false ) {
			// return error
			return $this->_validate_error;
		}	
		
		// read current storage
		$store = $this->get();		
		
		// match id and then update
		$update_found = false;
		for($n=0; $n<count($store); $n++) {

			// match id
			if ( $store[$n]->id == $upd_link['id'] ) {
				// update
				$store[$n]->title = $upd_link['title'];
				$store[$n]->link = $upd_link['link'];				
				
				// set flag
				$update_found = true;
			} 
		}
		
		// update done?
		if ( $update_found === false ) return '"id" not found';
		
		// usave to file
		$store = json_encode($store);
		file_put_contents($this->_store, $store);
	
		// all ok
		return true;	
	}

}
