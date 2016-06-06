<?php

/**
 * Model class for handling the JSON-storage in plain files
 * - Also handles validation of input data and simple sanitize
 */
class php_json {

	private $_store =  "data/TEST-links.json";
	private $_deleted = "data/TEST-deleted_links.json";
	
	private $_category_store =  "data/TEST-categories.json";
	private $_category_deleted = "data/TEST-deleted_categories.json";
	
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
		
		// does the json categories exist?
		if ( file_exists($this->_category_store) == FALSE ) {
			// create empty
			file_put_contents($this->_category_store, "[]");
		}

		// does the deleted categories exist?
		if ( file_exists($this->_category_deleted) == FALSE ) {
			// create empty
			file_put_contents($this->_category_deleted, "[]");
		}		
	}

	private function _common_get_by_id($id,$data) {
		
		for($n=0; $n<count($data); $n++) {

			// get row
			$row = $data[$n];

			// match id
			if ( $row->id == $id ) {
				// return link
				return $row;
			}
		}	
		
		// not found
		return '"id" was not found';		
	}


	public function generate_id() {
		
		// create a id with time + uniqid as a md5 32-char string
		return md5( uniqid().time() );
	}
	
	public function get($id="") {
		// public function should not be able to get another file
		$data = $this->_get("");
		
		// failed to get?
		if ( !is_array($data) ) {
			return "Storage failed (JSON)";	
		}		
		
		// only return one specific?
		if ( !empty($id) ) {
			
			return $this->_common_get_by_id($id, $data);
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
		$store = json_decode($data);
		
		// failed to decode?
		if ( !is_array($store) ) {
			return "Storage failed (JSON)";	
		}
		
		// else return
		return $store;
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
		
		if ( !isset($new['category_id']) ) {
			$this->_validate_error = 'Missing "category"';
			return false;
		}		
		
		
		// simple sanitize
		$new['id'] = strip_tags($new['id']);
		$new['title'] = strip_tags($new['title']);
		$new['link'] = strip_tags($new['link']);
		$new['category_id'] = strip_tags($new['category_id']);
		
		// more validation
		if ( !is_string($new['id']) ) { 
			$this->_validate_error = '"id" is not a string';
			return false;
		}
		
		if ( strlen($new['id']) != 32 ) {
			$this->_validate_error = '"id" doesn\'t have the expected length';		
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

		if ( !is_string($new['category_id']) ) {
			$this->_validate_error = '"category" is not a string';
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
		
		// failed to get?
		if ( !is_array($data) ) {
			return "Storage failed (JSON)";	
		}		
		
		// append
		array_push($data, $new);
		
		// encode as json
		$data = json_encode($data);
		
		// failed to encode?
		if ( $data === false ) {
			return "Storage failed (JSON)";	
		}
		
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

		// failed to get?
		if ( !is_array($store) ) {
			return "Storage failed (JSON)";	
		}

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
		
		// failed to get?
		if ( !is_array($deleted_store) ) {
			return "Storage failed (JSON)";	
		}
		
		array_push($deleted_store, $old_link);
		$deleted_store = json_encode($deleted_store);

		// failed to encode?
		if ( $deleted_store === false ) {
			return "Storage failed (JSON)";	
		}
		
		file_put_contents($this->_deleted, $deleted_store);

		// update current store
		$new_store = json_encode($new_store);
		
		// failed to encode?
		if ( $new_store === false ) {
			return "Storage failed (JSON)";	
		}
		
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
		
		// failed to get?
		if ( !is_array($store) ) {
			return "Storage failed (JSON)";	
		}		
		
		// match id and then update
		$update_found = false;
		for($n=0; $n<count($store); $n++) {

			// match id
			if ( $store[$n]->id == $upd_link['id'] ) {
				// update
				$store[$n]->title = $upd_link['title'];
				$store[$n]->link = $upd_link['link'];				
				$store[$n]->category_id = $upd_link['category_id'];
				
				// set flag
				$update_found = true;
			
				// exit loop since we're done
				break;
				
			} 
			
		}
		
		// update done?
		if ( $update_found === false ) return '"id" not found';
		
		// save to file
		$store = json_encode($store);

		// failed to encode?
		if ( $store === false ) {
			return "Storage failed (JSON)";	
		}
		
		file_put_contents($this->_store, $store);
	
		// all ok
		return true;	
	}

	public function get_categories($id="") {
		// public function should not be able to get another file
		$data = $this->_get_categories("");
		
		// failed to get?
		if ( !is_array($data) ) {
			return "Storage failed (JSON)";	
		}		
		
		// only return one specific?
		if ( !empty($id) ) {
			
			return $this->_common_get_by_id($id, $data);
		}
		
		// return data
		return $data;
	}

	private function _get_categories($fn="") {
		// default to normal store
		$fn = ( empty($fn) ? $this->_category_store : $fn );

		// read file
		$data = file_get_contents($fn);
	
		// return as php variable
		$store = json_decode($data);
		
		// failed to decode?
		if ( !is_array($store) ) {
			return "Storage failed (JSON)";	
		}
		
		// else return
		return $store;
	}

	private function _validate_and_sanitize_category($new) {
		
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
		
		if ( !isset($new['name']) ) {
			$this->_validate_error = 'Missing "name"';		
			return false;
		}

		
		// simple sanitize
		$new['id'] = strip_tags($new['id']);
		$new['name'] = strip_tags($new['name']);
		
		// more validation
		if ( !is_string($new['id']) ) { 
			$this->_validate_error = '"id" is not a string';
			return false;
		}


		if ( strlen($new['id']) != 32 ) {
			$this->_validate_error = '"id" doesn\'t have the expected length';		
			return false;
		}
		
		// else all ok
		return $new;
	}

	public function append_category($new) {

		// validate
		$new = $this->_validate_and_sanitize_category($new);
		if ( $new === false ) {
			// return error
			return $this->_validate_error;
		}
		
		// read file
		$data = $this->get_categories();
		
		// failed to get?
		if ( !is_array($data) ) {
			return "Storage failed (JSON)";	
		}
		
		// append
		array_push($data, $new);
		
		// encode as json
		$data = json_encode($data);
		
		// failed to encode?
		if ( $data === false ) {
			return "Storage failed (JSON)";	
		}		
		
		// write
		file_put_contents($this->_category_store, $data);
		
		// all ok
		return true;
	}


	public function get_by_category($cat_id) {
		
		// simple sanitize
		$cat_id = strip_tags($cat_id);		
		
		// validate
		if ( !is_string($cat_id) ) { 
			return '"id" is not a string';
		}

		if ( strlen($cat_id) != 32 ) {
			return '"id" doesn\'t have the expected length';		
		}
		
		$links = array();
		
		// read current storage
		$store = $this->get();		
		
		// failed to get?
		if ( !is_array($store) ) {
			return "Storage failed (JSON)";	
		}			
	
		// sort out only for category
		for($n=0; $n<count($store); $n++) {

			// match id
			if ( $store[$n]->category_id == $cat_id ) {
				// add
				array_push($links, $store[$n]);
			} 
			
		}		
		
		// return matched links
		return $links;
	}
}
