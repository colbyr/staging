<?php

/**
 * Collection
 */
class Collection
{
  
  /**
   * Type
   *
   * Collection type
   *
   * @var string
   */
  public $type;
  
  /**
   * Items
   *
   * The contents of the collection
   *
   * @var array
   */
  public $items;
  
  /**
   * Constructor
   *
   * Creates a new collection and adds $data
   *
   * @param  array $data
   * @return void
   */
  public function __construct($data=array())
  {
    if(empty($data)){
      $items = array();
    }else{
      if(is_array($data)){
        $this->items = $data;
      }else{
        throw new CollectionException("Collection->__construct() expects an array");
      }
    }
  }

  /**
   * Length
   *
   * Returns the length of the collection
   *
   * @return integer
   */
  public function length()
  {
    return count($this->items);
  }
  
  /**
   * Insert
   *
   * Add an item to the collection
   *
   * @param  anything $item
   * @return void
   */
  public function insert($item)
  {
    array_push($this->items, $item);
  }
  
  /**
   * Insert Array
   *
   * Merge an array of items into the collection
   *
   * @param  array $data
   * @return void
   */
  public function insert_array($data)
  {
    if(is_array($data)){
      $this->items = array_merge($this->items, $data);
    }else{
      throw new CollectionException('Collection->insert_array() expects $param to be of type array[].');
    }
  }
  
  /**
   * Is Empty?
   *
   * Returns true if the collection is empty
   *
   * @return bool
   */
  public function is_empty()
  {
    return count($this->items) === 0;
  }
  
  /**
   * Get
   *
   * Retrieve an element from the collection by its index
   *
   * @param  Integer  $index
   * @return anything
   */
  public function get($index)
  {
    if($index >= count($this->items)){
      throw new CollectionException('Collection->get(): Index ['.$index.'] out of bounds');
    }else{
      return $this->items[$index];
    }
  }
  
  /**
   * Get By
   *
   * Retrieves the first element with the given key
   *
   * @param  string   $key
   * @param  anything $val
   * @return Object
   */
  public function get_by($key, $val)
  {
    $res = null;
    if (!empty($this->items)) {
      foreach($this->items as $item){
        if($item->$key == $val){
          $res = $item;
          break;
        }
      }
    }
    return $res;
  }
  
  /**
   * Get All By
   *
   * gets the all elements with the given key/val
   *
   * @param  string   $key
   * @param  anything $val
   * @return Object
   */
  public function get_all_by($key, $val)
  {
    $res = array();
    foreach($this->items as $item){
      if($item->$key == $val){
        array_push($res, $item);
      }
    }
    return $res;
  }

  /**
   * Set All
   *
   * Sets given key to the given value in all items of the collection
   *
   * @param  string   $key
   * @param  anything $val
   * @return void
   */
  public function set_all($key, $val)
  {
    foreach($this->items as $item){
      $item->$key = $val;
    }
  }
  
  /**
   * Pluck
   *
   * pluck specified key from all items in the collection
   *
   * @param  string $find_key
   * @param  bool   $unique
   * @param  array  $data
   * @return array 
   */
  public function pluck($find_key, $unique=true, $data=array())
  {
    $res = array();
    $data = (empty($data) ? $this->items : $data);
    
    foreach($data as $key => $item)
    {
      array_push($res, $item->$find_key);
      
      if($unique){
        $res = array_unique($res);
      }
    }
    
    return $res;
  }
  
  /**
   * Filter
   *
   * Filters collection by function of $this
   *
   * @param  string $function
   * @param  array  $params
   * @return array
   */
  public function filter($function, $params=array())
  {
    $res = array();
    if(method_exists($this, $function)){
      foreach($this->items as $key=>$item)
      {
        if($this->$function($item, $key, $params)){
          array_push($res, $item);
        }
      }
    }else if(function_exists($function)){
      foreach($this->items as $key=>$item)
      {
        if($function($item, $key, $params)){
          array_push($res, $item);
        }
      }
    }else{
      throw new CollectionException('Iterable method "'.$function.'" does not exist in Collection::'.$this->type.' or in the current scope.');
    }
    return $res;
  }

}

/**
 * Collection Exception
 */
class CollectionException extends Exception{}