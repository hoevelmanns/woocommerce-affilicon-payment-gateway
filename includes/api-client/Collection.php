<?php
/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        Collection.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        10.10.17
 */

namespace Affilicon;

class Collection extends ApiClient implements \Iterator
{
  /**
   * Current index
   * @var int
   */
  protected $intIndex = -1;

  /** @var array  */
  private $items = [];

  /**
   * @param $obj
   * @param null $key
   * @throws KeyHasUseException
   */
  public function addItem($obj, $key = null) {
    if ($key == null) {
      $this->items[] = $obj;
    }
    else {
      if (isset($this->items[$key])) {
        throw new KeyHasUseException($key);
      }
      else {
        $this->items[$key] = $obj;
      }
    }
  }

  /**
   * @param $key
   * @throws KeyInvalidException
   */
  public function deleteItem($key) {
    if (isset($this->items[$key])) {
      unset($this->items[$key]);
    }
    else {
      throw new KeyInvalidException($key);
    }
  }

  /**
   * @param $key
   * @return mixed
   * @throws KeyInvalidException
   */
  public function get($key) {
    if (isset($this->items[$key])) {
      return $this->items[$key];
    }
    else {
      throw new KeyInvalidException("Invalid key $key.");
    }
  }

  /**
   * Return the key of the current element
   */
  public function key()
  {
    return key($this->items);
  }

  /**
   * @return array
   */
  public function keys()
  {
    return array_keys($this->items);
  }

  /**
   * @return int
   */
  public function count()
  {
    return count($this->items);
  }

  /**
   * Go to the first item
   * @return static The collection object
   */
  public function first()
  {
    $this->intIndex = 0;

    return $this;
  }

  /**
   * @param $key
   * @return bool
   */
  public function exists($key)
  {
    return isset($this->items[$key]);
  }

  /**
   * Checks if current position is valid
   */
  public function valid()
  {
    return $this->valid();
  }


  /**
   * Go to the previous item
   * @return static|false The collection object or false if there is no previous item
   */
  public function rewind()
  {
    if ($this->intIndex < 1) {
      return null;
    }

    --$this->intIndex;

    return $this;
  }


  /**
   * Return then current item
   * @return mixed
   */
  public function current()
  {
    if ($this->intIndex < 0) {
      $this->first();
    }

    return $this->items[$this->intIndex];
  }


  /**
   * Go to the next item
   * @return static|boolean The collection object or false if there is no next item
   */
  public function next()
  {
    if (!isset($this->items[$this->intIndex + 1])) {
      return null;
    }

    ++$this->intIndex;

    return $this;
  }


  /**
   * Go to the last item
   * @return static The collection object
   */
  public function last()
  {
    $this->intIndex = count($this->items) - 1;

    return $this;
  }


  /**
   * Reset the item
   * @return static The collection object
   */
  public function reset()
  {
    $this->intIndex = -1;

    return $this;
  }
}