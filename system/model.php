<?php 

class Model
{
  /**
   * Class Name
   *
   * @var string
   */
  public $class_name = 'Model';
  
  /**
   * Table Name
   *
   * Database table name
   *
   * @var string
   */
  public $table_name = '';
  
  /**
   * Order Key
   *
   * default order by key
   *
   * @var string
   */
  public $order_by = '';
  
  /**
   * Attribtues
   *
   * Model attributes from the database
   *
   * @var array
   */
  public $attributes = array();
  
  /**
   * Related
   *
   * like attributes, but for related models
   *
   * @var array
   */
  public $related = array();
  
  /**
   * Required Attributes
   *
   * Attributes checked by is_valid()
   *
   * @var array
   */
  public $required_attributes = array();
  
  /**
   * Missing
   *
   * Attributes not found by is_valid()
   *
   * @var array
   */
  public $missing = array();
  
  /**
   * Changed
   *
   * dirty model attributes
   *
   * @var changed
   */
  public $changed = array();
  
  /**
   * Join
   *
   * Relationships to be automaticall loaded
   *
   * @var array
   */
  public $join = array();
  
  /**
   * Table
   *
   * Returns model's table name
   *
   * @param  string $name
   * @return string
   */
  public function table($name='')
  {
    if(!empty($name)){
      return strtolower($name);
    }else if($this->table_name !== ''){
      return $this->table_name;
    }else{
      return strtolower($this->class_name).'s';
    }
  }
  
  /**
   * Is Valid?
   *
   * Checks for required attributes and adds missing ones to $this->missing
   *
   * @return bool
   */
  public function is_valid()
  {
    $valid = true;
    
    if(!is_array($this->required_attributes))
        throw new ModelException($this->class_name . '->is_valid() expects that ' . $this->class_name . '->required_attributes is an array[]');
    
    if(!$this->is_empty($this->required_attributes)) // if there are requirements, check them!
    {
      foreach($this->required_attributes as $r)
      {
        if($this->is_empty($r)) $valid = false;
      }
    }
    return $valid;
  }
  
  /**
   * Is Dirty?
   *
   * checks if a model has dirty attributes or has not been persisted
   *
   * @return bool
   */
  public function is_dirty()
  {
    $dirty = (!empty($this->changed) || (!isset($this->attributes['id']) || empty($this->attributes['id'])));
    return $dirty;
  }
  
  /**
   * Find
   *
   * Retrieves model from the database by id
   *
   * @param  integer $id
   * @param  bool    $autoload
   * @return Model
   */
  public function find($id, $autoload=true){
      global $db;
      $sql = 'SELECT `'.$this->table().'`.*';
      if(!empty($this->join)){
        foreach($this->join as $tbl => $cols){
          for($i = 0; $i < count($cols); $i++){
            $sql .= ', `'.$tbl.'`.`'.$cols[$i].'`';
          }
        }
      }
      $sql .= ' FROM `'.$this->table().'`';
      if(!empty($this->join)){
        foreach($this->join as $tbl => $cols){
          $sql .= ' LEFT OUTER JOIN `'.$tbl.'` ON `'.$tbl.'`.`'.strtolower($this->class_name).'_id` = `'.$this->table().'`.`id`';
        }
      }

      $sql .= ' WHERE `'.$this->table().'`.`id` = "'.$id.'"';

      $res = $db->query($sql);
      
      if(!($res)){
        return null;
      }
      $row = array_pop($res);
      $this->fill($row);
      
      return $this;
  }
  
  /**
   * With
   *
   * Loads a relations from persistent storage
   *
   * @param  array|string $includes
   * @param  array        $limit
   * @return Model
   */
  public function with($includes, $limit=array())
  {
    if(is_array($includes)){
      foreach($includes as $include)
      {
        if(method_exists($this, $include)){
          $this->$include = $this->$include($limit);
        }else{
          throw new ModelException('Model Exception: Include "'.$include.'" undefined.');
        }
      }
    }else{
      if(method_exists($this, $includes)){
        $this->$includes = $this->$includes($limit);
      }else{
        throw new ModelException('Model Exception: Include "'.$includes.'" undefined.');
      }
    }
    
    return $this;
  }
  
  /**
   * Belongs To
   *
   * defines a "belongs to" relationship
   *
   * @param  string $name
   * @param  string $fk
   * @param  array  $model
   * @return Model
   */
  public function belongs_to($name, $fk='', $model=array())
  {
    $name = ucfirst($name);
    if(empty($data)){
      $foreign_key = empty($fk) ? strtolower($name).'_id' : $fk;
      $model = new $name();
      $model = $model->find($this->$foreign_key);
    }
    return $model;
  }
  
  /**
   * Has One
   *
   * defines a "has one" relationship
   *
   * @param  string $name
   * @param  string $table
   * @param  string $fk
   * @param  array  $model
   * @return Model
   */
  public function has_one($name, $table='', $fk='', $model=array())
  {
  	global $db;
    $name = ucfirst($name);
    if(empty($model)){
      $table = empty($table) ? $this->table($name) : $table ;
      $foreign_key = empty($fk) ? strtolower($this->class_name).'_id' : $fk;
      $sql = 'SELECT * FROM `'.$table.'` WHERE `'.$foreign_key.'` = "'.$this->id.'" LIMIT 0,1';

      $res = $db->query($sql);
      
      if(!($res)){
        $model = null;
      }else{
	    $row = array_pop($res);
        $model = new $name($row);
	  }
    }
    return $model;
  }
  
  /**
   * Has Many
   *
   * defines a "has many" relationship
   *
   * @param  string $name
   * @param  array  $limit
   * @param  string $fk
   * @param  string $id_key
   * @return Collection
   */
  public function has_many($name, $limit=array(), $fk='', $id_key='id')
  {
    $name = ucfirst($name);
    if(empty($data)){
      
      $foreign_key = empty($fk) ? strtolower($this->class_name).'_id' : $fk;
      
      $collection = new $name();
      
      $data = $collection->where(array($foreign_key => $this->$id_key), $limit);
    }
    return $data;
  }
  
  /**
   * Has Many Through
   *
   * defines a "has many through" relationship
   *
   * @param  string $name
   * @param  string $through
   * @param  array  $limit
   * @param  string $fk
   * @param  string $id_key
   * @return Collection
   */
  public function has_many_through($name, $through, $limit=array(), $fk='', $id_key='')
  {
    $name = ucfirst($name);
    if(empty($data)){
      
      $foreign_key = empty($fk) ? strtolower($through).'_id' : $fk;
      $id_key = empty($id_key) ? $foreign_key : $id_key;
      $collection = new $name();
      
      $data = $collection->where(array($foreign_key => $this->$id_key), $limit);
    }
    return $data;
  }

  /**
   * Delete All
   *
   * Removes all relations of specified type from persistent storage
   *
   * @param  string $name
   * @param  stirng $fk
   * @param  string $id_key
   * @return void
   */
  public function delete_all($name, $fk='', $id_key='id')
  {
  	global $db;
    $name = ucfirst($name);
    if(empty($data)){

      $foreign_key = empty($fk) ? strtolower($this->class_name).'_id' : $fk;

      $sql = 'DELETE FROM `' . strtolower($name) . '` WHERE `' . $foreign_key . '` = "' . $this->$id_key . '"';
	  
	  $db->query($sql);
    }
  }

  /**
   * Construct
   *
   * Default Model constructor
   *
   * @param  array $attributes
   * @return void
   */
  public function __construct($attributes = array())
  {
    if(!empty($attributes)){
      $this->fill($attributes);
    }
  }
  
  /**
   * Fill
   *
   * Sets attributes of model
   *
   * @param  array $attributes
   * @param  bool  $delete
   * @return void
   */
  public function fill($attributes, $delete=false)
  {
    foreach($attributes as $key => $val)
    {
      if($this->is_related($key)){
        if(is_a($this->$key, 'Models')){
          $this->$key->fill($val, $delete);
          $this->$key->set_foreign_key(strtolower($this->class_name).'_id', $this->id);
        }else if(is_a($this->$key, 'Model')){
          $this->$key->fill(self::unclean($val), $delete);
        }else{
          $this->$key = self::unclean($val);
        }
      }else{
        $this->$key = self::unclean($val);
      }
    }
  }
  
  /**
   * Is Foreign Key?
   *
   * checks if a key conforms foreign key conventions (thing_id)
   *
   * @param  string $key
   * @return bool
   */
  public function is_foreign_key($key)
  {
    return strpos($key, '_id') && method_exists($this, str_replace('_id', '', $key));
  }
  
  /**
   * Set Foreign Key
   *
   * convenience method for setting a foreign key
   *
   * @param  string   $fk
   * @param  anything $val
   * @return void
   */
  public function set_foreign_key($fk, $val)
  {
    $this->$fk = $val;
  }
  
  /**
   * Set Related Keys
   *
   * convenience method for updating keys in related collections
   *
   * @param  string $fk
   * @param  string $val
   * @return void
   */
  public function set_related_keys($fk='', $val='')
  {
    $fk = (empty($fk)) ? (strtolower($this->class_name).'_id') : $fk;
    $val = (empty($val)) ? $this->id : $val;
    
    if(!empty($this->related))
    {
      foreach($this->related as $rel)
      {
        $rel->set_foreign_key($fk, $val);
      }
    }
  }
  
  /**
   * Is new?
   *
   * checks if model has been persisted
   *
   * @return bool
   */
  public function is_new()
  {
    return empty($this->attributes['id']);
  }
  
  /**
   * Save
   *
   * Saves the Model to the DB
   *
   * @param  bool  $save_related
   * @return Model
   */
  public function save($save_related=true)
  {
  	global $db;
    // valid model
    if(!$this->is_valid()) return false;
    
    if($this->is_new()){
      $sql = '';
      // TODO define insert query
      $sql .= 'INSERT INTO `'.self::clean($this->table()).'`';
      $columns = ' (';
      $values = ' VALUES (';
      $count = 0;
      $this->attributes['created_on'] = date('Y-m-d G:i:s');

      foreach($this->attributes as $attr=>$val){
        if($attr != 'id' || $attr != 'archived'){
          $columns .= ($count > 0 ? ',' : '').' `'.self::clean($attr).'`';
          $values .= ($count > 0 ? ',' : '').' "'.self::clean($val).'"';
          $count++;
        }
      }
      $columns .= ')';
      $values .= ')';
      $sql .= $columns.$values;
      
      $this->changed = array();
	  $res = $db->query($sql);
      $this->id = mysql_insert_id();
      $this->set_related_keys();     
      
    }else{
      if(!empty($this->changed)){
        $sql='';
        $sql .= 'UPDATE `'.$this->table().'` SET ';
        $count = 0;
        foreach($this->changed as $attr){
          if($attr != 'id'){ //ignore the id-- PK shouldn't change
            $sql .= ($count > 0 ? ',' : '').' `'.self::clean($attr).'` = "'.self::clean($this->attributes[$attr]).'"';
            $count++;
          }
        }
        $sql .= ' WHERE `id` = "'.self::clean($this->id).'"';
        $db->query($sql);
        $this->changed = array();
      }
    }
    if($save_related) $this->save_related($save_related);
    
    return $this;
  }
  
  /**
   * Save Related
   *
   * recursively save related models/collections
   *
   * @param  bool $save_related
   * @return void
   */
  public function save_related($save_related=true)
  {
    foreach($this->related as $rel)
    {
      if($rel instanceof Model || $rel instanceof Collection) $rel->save($save_related);
    }
  }
  
  /**
   * Delete
   *
   * Deletes Model from database
   *
   * @return bool
   */
  public function delete()
  {
  	global $db;
    if(empty($this->attributes)){
      throw new ModelException('Model Exception: Cannot delete '.$this->class_name.' that doesn\'t exist.');
    }else{
      $sql = 'DELETE FROM `'.$this->table().'` WHERE `id`="'.self::clean($this->id).'"';
      return $db->query($sql);
    }
  }

  /**
   * Query
   *
   * populate mode from an sql query
   *
   * @param  string $sql
   * @return Model
   */
  public function query($sql)
  {
    global $db;
    $res = $db->query($sql);
    
    if(!($res)){
      return null;
    }
    
    $this->fill($res);
    
    return $this;
  }
  
  /**
   * Is Related?
   *
   * returns true if key maps to a related object
   *
   * @param  string $key
   * @return bool
   */
  public function is_related($key)
  {
    return array_key_exists($key, $this->related) || method_exists($this, $key);
  }
  
  /**
   * Get
   *
   * Magic Method for getting model attributes
   *
   * @param  string   $key
   * @return anything
   */
  public function __get($key){
    if(array_key_exists($key, $this->attributes)){
      return $this->attributes[$key];
    }else if(array_key_exists($key, $this->related)){
      return $this->related[$key];
    }else if(method_exists($this, $key)){
      $this->$key = $this->$key();
      return $this->related[$key];
    }
  }
  
  /**
   * Set
   *
   * Magic Method for setting model attributes
   *
   * @param  string   $key
   * @param  anything $val
   * @return void
   */
  public function __set($key, $val){
    if($this->is_related($key)){
      $this->related[$key] = $val;
    }else{
      if(array_key_exists($key, $this->attributes) && !$this->is_foreign_key($key)) array_push($this->changed, $key);
      $this->attributes[$key] = $val;
    }
  }
  
  /**
   * Call
   *
   * Magic Method for calling model methods
   *
   * @param  string   $method
   * @param  array    $parameters
   * @return anything
   */
  public function __call($method, $parameters){
    if(method_exists($this, $method)){
      return call_user_func_array(array($this, $method), $parameters);
    }else{
      throw new ModelException('Model Exception: method "'.$method.'()" is not defined in class '.$this->class_name);
    }
  }
  
  /**
   * Flatten
   *
   * flattens model, stripping out maintenance attributes
   *
   * @return array
   */
  public function flatten()
  {
    $things = array();
    
    foreach($this->attributes as $key=>$attr){
      $things[$key] = $this->flatten_helper($attr);
    }
    
    foreach($this->related as $key=>$attr){
      if(is_a($attr, 'Models')){
        $arr = array();
        if (!empty($attr->items))
        {
          foreach($attr->items as $subkey=>$subattr){
            array_push($arr, $this->flatten_helper($subattr));
          }
        }
        $things[$key] = $arr;
      }else{
        $things[$key] = $this->flatten_helper($attr);
      }
    }
    
    return $things;
  }
  
  /**
   * Flatten Helper
   *
   * helper function for flattening nested models
   *
   * @param  anything $thing
   * @return anything
   */
  protected function flatten_helper($thing){
    if(is_object($thing) && is_a($thing, 'Model')){
      return $thing->flatten();
    }else{
      return $thing;
    }
  }
  
  /**
   * Is Empty?
   *
   * checks if a key is empty
   *
   * @param  string $key
   * @return bool
   */
  public function is_empty($key='')
  {
    if(empty($key)) 
    {
      return empty($this->attributes);
    }
    else
    {
      return empty($this->attributes[$key]) && empty($this->related[$key]);
    }
  }
  
  /**
   * JSON
   *
   * converts the model to json
   *
   * @return string
   */
  public function json(){
    return json_encode($this->flatten());
  }
  
  /**
   * Clean
   *
   * sanitize a string for DB insertion
   *
   * @param  string $str
   * @return string
   */
  public static function clean($str)
  {
    $str = str_replace("'", '', $str);
    $str = strip_tags($str);
    return mysql_real_escape_string($str);
  }
  
  /**
   * Unclean
   *
   * unsanitize input for display
   *
   * @param  string $str
   * @return string
   */
  public static function unclean($str)
  {
    return stripslashes($str);
  }

}

/**
 * Model Exception
 */
class ModelException extends Exception{}