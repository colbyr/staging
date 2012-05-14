<?php

require_once(dirname(__FILE__).'/collection.php');
require_once(dirname(__FILE__).'/model.php');

class Models extends Collection
{

  /**
   * Type
   *
   * Model type
   *
   * @var string
   */
  public $type = 'Model';

  /**
   * Model Name
   *
   * @var string
   */
  public $model_name = 'Model';

  /**
   * Model
   *
   * Keep and instance of the model for reference
   *
   * @var Model
   */
  public $model;
  
  /**
   * Delete
   *
   * Items to be deleted
   *
   * @var array
   */
  public $delete = array();

  /**
   * Constructor
   *
   * @param  array  $data
   * @param  string $fk
   * @return void
   */
  public function __construct($data=array(), $fk='')
  {
    parent::__construct($data);
    // we need this since PHP 5.2 does not support late static binding :(
    $this->model = new $this->model_name();
  }

  /**
   * Fill
   *
   * @param  array $data
   * @param  bool  $delete
   * @return void
   */
  public function fill($data, $delete=false)
  {
    if (!empty($data))
    {
      foreach($data as $d)
      {
        if(empty($d['id'])){
          $mn = $this->model_name;
          $this->insert(new $mn($d));
        }else{
          $this->update($d);
        }
      }
    }
    
    if($delete) $this->empty_trash();
  }

  /**
   * Set Foreign Key
   *
   * update foreign keys across all items in the collection
   *
   * @param  string  $fk
   * @param  integer $val
   * @return void
   */
  public function set_foreign_key($fk, $val)
  {
    $this->set_all($fk, $val);
  }

  /**
   * Empty Trash
   *
   * Deletes all unchanged items in the collections
   *
   * @return void
   */
  public function empty_trash()
  {
    foreach($this->items as $k=>$item){
      if(!$item->is_dirty()){
        $this->delete($k);
      }
    }
  }

  /**
   * Update
   *
   * Update a model with an array of data containing an id
   *
   * @param  array $data
   * @return void
   */
  public function update($data)
  {
    $m = $this->get_by('id', $data['id']);
    
    if(!empty($m)) $m->fill($data);
  }

  /**
   * Query
   *
   * populate collection from an sql query
   *
   * @param  string $sql
   * @return array
   */
  public function query($sql)
  {
    global $db;
    $res = $db->query($sql);
      
    $results = array();
    
    if(!empty($res)){
      while($data = array_pop($res)){
        $to_add = new $this->model($data);
        array_push($results, $to_add);
      }
    }
    
    $this->items = array_reverse($results);
  }

  /**
   * All
   *
   * get all models from the database
   *
   * @param  array $order
   * @param  array $limit
   * @return Models
   */
  public function all($order=array(), $limit=array())
  {
    global $db;
    $sql = 'SELECT * FROM `'.$this->model->table().'`';

    if (!empty($order))
    {
      $sql .= ' ORDER BY `' . $order['column'] . '` ' . $order['order'];
    }

    if (!empty($limit))
    {
      $sql .= ' LIMIT ' . $limit['offset'] . ', ' . $limit['limit'];
    }

    $res = $db->query($sql);
    
    $data = array();
	if(!empty($res)){
		while($rec = array_pop($res))
		{
		  //$data[$rec['id']] = new $this->model($rec);
		  array_push($data, new $this->model($rec));
		}
	}
    $this->items = array_reverse($data);
    
    return $this;
  }
  
  /**
   * Row Count
   *
   * return the count of the models from the database
   *
   * @param  array $params
   * @return Models
   */
  public function rowcount($params=array())
  {
    global $db;
    $sql = 'SELECT COUNT(*) AS rowcount FROM `'.$this->model->table().'`';
	if(!(empty($params))) { 
		$sql .= ' WHERE';
        $count = 0;
        foreach($params as $col => $val){
          if($count > 0){
            $sql .= ' AND';
          }
          $sql .= ' `'.$this->model->table().'`.`'.$col.'` = "'.$val.'"';
          $count++;
        }
	}

    $res = $db->query($sql);
	if(!empty($res)){
		$row = array_pop($res);
    	return intval($row["rowcount"]);
	} else {
		throw new CollectionException('Collection->rowcount(): invalid parameters => '.mysql_error());
	}
  }

  /**
   * Where
   *
   * Get models according to $params
   *
   * @param  array $params
   * @param  array $limit
   * @return Models
   */
  public function where($params, $limit=array())
  {
    global $db;
    if(!is_array($params)){
      throw new CollectionException('Collection->where(): $params expected as type array($column => $value).');
    }else{
      $sql = 'SELECT `'.$this->model->table().'`.*';
      if(!empty($this->model->join)){
        foreach($this->model->join as $tbl => $cols){
          for($i = 0; $i < count($cols); $i++){
            $sql .= ', `'.$tbl.'`.`'.$cols[$i].'`';
          }
        }
      }
      $sql .= ' FROM `'.$this->model->table().'`';
      if(!empty($this->model->join)){
        foreach($this->model->join as $tbl => $cols){
          $sql .= ' LEFT OUTER JOIN `'.$tbl.'` ON `'.$tbl.'`.`'.strtolower($this->model->class_name).'_id` = `'.$this->model->table().'`.`id`';
        }
      }
      $sql .= ' WHERE';
      $count = 0;
      foreach($params as $col => $val){
        if($count > 0){
          $sql .= ' AND';
        }
        $sql .= ' `'.$this->model->table().'`.`'.$col.'` = "'.$val.'"';
        $count++;
      }
      
      if (!empty($limit))
      {
        $sql .= ' LIMIT ' . $limit['offset'] . ', ' . $limit['limit'];
      }

      if($this->model->order_by != '')
      {
        $sql .= ' ORDER BY `' . $this->model->order_by . '` ASC';
      }
      
      //echo $sql; exit();
      
      $res = $db->query($sql);
      
      $results = array();
      
      if(!empty($res)){
        while($data = array_pop($res)){
          $to_add = new $this->model($data);
          array_push($results, $to_add);
        }
      }
      
      $this->items = array_reverse($results);
    }
    return $this;
  }

  /**
   * Where In
   *
   * Get all models where $column is in $values
   *
   * @param  string $column
   * @param  array  $values
   * @param  array  $limit
   * @return Models
   */
  public function where_in($column, $values, $limit=array())
  {
    global $db;
    if(!is_array($values)){
      throw new CollectionException('Collection->where_in(): $values expected as type array($value).');
    }else{
      $sql = 'SELECT `'.$this->model->table().'`.*';
      if(!empty($this->model->join)){
        foreach($this->model->join as $tbl => $cols){
          for($i = 0; $i < count($cols); $i++){
            $sql .= ', `'.$tbl.'`.`'.$cols[$i].'`';
          }
        }
      }
      $sql .= ' FROM `'.$this->model->table().'`';
      if(!empty($this->model->join)){
        foreach($this->model->join as $tbl => $cols){
          $sql .= ' LEFT OUTER JOIN `'.$tbl.'` ON `'.$tbl.'`.`'.strtolower($this->model->class_name).'_id` = `'.$this->model->table().'`.`id`';
        }
      }
      $sql .= ' WHERE';
      $count = 0;
      $sql .= ' `'.$this->model->table().'`.`'.$column.'` IN (';
      foreach($values as $val){
        if($count > 0){
          $sql .= ', ';
        }
        $sql .= "'$val'";
        $count++;
      }
      $sql .= ')';

      if (!empty($limit))
      {
        $sql .= ' LIMIT ' . $limit['offset'] . ', ' . $limit['limit'];
      }

      if($this->model->order_by != '')
      {
        $sql .= ' ORDER BY `' . $this->model->order_by . '` ASC';
      }

      $res = $db->query($sql);
      
      $results = array();
      
      if($res) {
        while($data = array_pop($res)){
          $to_add = new $this->model($data);
          array_push($results, $to_add);
        }
      }
      
      $this->items = array_reverse($results);
    }
    return $this;
  }

  /**
   * Delete In
   *
   * Just like "where in", except for deleting things
   *
   * @param  string $column
   * @param  array  $values
   * @return void
   */
  public function delete_in($column, $values)
  {
    global $db;
    if(!is_array($values)){
      throw new CollectionException('Collection->delete_in(): $values expected as type array($value).');
    }else{
      $sql = 'DELETE FROM `'.$this->model->table().'`';
      $sql .= ' WHERE';
      $count = 0;
      $sql .= ' `'.$this->model->table().'`.`'.$column.'` IN (';
      foreach($values as $val){
        if($count > 0){
          $sql .= ', ';
        }
        $sql .= "'$val'";
        $count++;
      }
      $sql .= ')';
      
      $res = $db->query($sql);
    }
  }

  /**
   * With
   *
   * Retrieve related models from the database
   *
   * @param  string|array $includes
   * @param  array        $limit
   * @return Models
   */
  public function with($includes, $limit=array())
  {
    if(is_array($includes)){
      foreach($includes as $function){
        if(method_exists($this, $function)){
          $this->$function($limit);
        }else{
          throw new CollectionException('Call to undefined relation "'.$function.'"');
        }
      }
    }else{
      if(method_exists($this, $includes)){
        $this->$includes($limit);
      }else{
        throw new CollectionException('Call to undefined relation "'.$includes.'"');
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
   * @param  string $key
   * @param  string $plural
   * @return Models
   */
  protected function belongs_to($name, $fk='', $key='', $plural='')
  {
    $lname = strtolower($name);
    $foreign_key = empty($fk) ? $lname.'_id' : $fk;
    $fks = $this->pluck($foreign_key);
    
    if(empty($fks)){
      return array();
    }
    
    $plural = empty($plural) ? $name.'s' : $plural;
    
    $key = empty($key) ? 'id' : $key;
    
    $items = new $plural();
    $items->where_in('id', $fks);
    
    // merge
    foreach($items->items as $key=>$add){
      foreach($this->get_all_by($foreign_key, $add->id) as $add_to){
        $add_to->$lname = $add;
      }
    }
    
    return $this;
  }
  
  /**
   * Has Many
   *
   * defines a "has many" relationship
   *
   * @param  string $name
   * @param  array  $limit
   * @param  string $fk
   * @param  string $key
   * @param  string $plural
   * @return Models
   */
  protected function has_many($name, $limit=array(), $fk='', $key='', $plural='')
  {
    $model = array();
    $lname = strtolower($name);
    $foreign_key = empty($fk) ? strtolower($this->model_name).'_id' : $fk;
    
    $plural = empty($plural) ? $name : $plural;
    
    $key = empty($key) ? 'id' : $key;
    
    $fks = $this->ids();
    
    $items = new $plural();
    $items->where_in($foreign_key, $fks, $limit);
    
    
    foreach($this->items as $key=>$add_to){
      $n = strtolower($plural);
      $arr = $items->get_all_by($foreign_key, $add_to->id);
      
      $collection = new $name($arr);
      
      $add_to->$n = $collection;
    }
    
    return $this;
  }
  
  /**
   * Delete
   *
   * Move Item to trash
   *
   * @param  integer $i
   * @return void
   */
  public function delete($i)
  {
    array_push($this->delete, $this->items[$i]);
    unset($this->items[$i]);
  }
  
  /**
   * Save
   *
   * Save all updated models in the collection
   *
   * @param  bool $save_related
   * @return Models
   */
  public function save($save_related=true)
  {
    foreach($this->items as $model)
    {
      $model->save($save_related);
    }
    
    if(!empty($this->delete)){
      $this->delete_in('id', $this->delete_ids());
      $this->delete = array();
    }
    
    return $this;
  }
  
  /**
   * Ids
   *
   * Convenience method for retrieving all ids in the collection
   *
   * @return array
   */
  protected function ids()
  {
    return $this->pluck('id');
  }
  
  /**
   * Delete
   *
   * Returns ids of all models to be deleted
   *
   * @return array
   */
  protected function delete_ids()
  {
    return $this->pluck('id', true, $this->delete);
  }

  /**
   * JSON
   *
   * Get json representation of the collection
   *
   * @return string
   */
  public function json(){
    return json_encode($this->items);
  }

}