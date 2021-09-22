<?php

namespace Brid\Core\Foundation;

use Closure;
use Illuminate\Support\Arr;

class Config implements \ArrayAccess
{

  private array $values = [];

  /**
   * Determine if a given offset exists.
   *
   * @param  string  $key
   * @return bool
   */
  public function offsetExists($key)
  {
    return isset($this->values[$key]);
  }

  /**
   * Get the value at a given offset.
   *
   * @param  string  $key
   * @return mixed
   */
  public function offsetGet($key)
  {
    return Arr::get($this->values, $key);
  }

  /**
   * Set the value at a given offset.
   *
   * @param  string  $key
   * @param  mixed  $value
   * @return void
   */
  public function offsetSet($key, $value)
  {
    $this->values[$key] = $value;
  }

  /**
   * Unset the value at a given offset.
   *
   * @param  string  $key
   * @return void
   */
  public function offsetUnset($key)
  {
    unset($this->values[$key]);
  }

  /**
   * Dynamically access container services.
   *
   * @param  string  $key
   * @return mixed
   */
  public function __get($key)
  {
    return $this->values[$key] ?? null;
  }

  /**
   * Dynamically set container services.
   *
   * @param  string  $key
   * @param  mixed  $value
   * @return void
   */
  public function __set($key, $value)
  {
    $this->offsetSet($key, $value);
  }

}