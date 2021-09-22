<?php

namespace Brid\Core\Foundation;

use Closure;
use Illuminate\Support\Arr;

class Config implements \ArrayAccess
{

  private array $values = [];

  /**
   * @param string $key
   * @return bool
   */
  public function has(string $key): bool
  {
    return Arr::has($this->values, $key);
  }

  /**
   * @param string $key
   * @param mixed $default
   * @return mixed
   */
  public function get(string $key, mixed $default = null): mixed
  {
    return Arr::get($this->values, $key, $default);
  }

  /**
   * @param string $key
   * @param mixed $value
   */
  public function set(string $key, mixed $value): void
  {
    Arr::set($this->values, $key, $value);
  }

  /**
   * Determine if a given offset exists.
   *
   * @param  string  $key
   * @return bool
   */
  public function offsetExists($key)
  {
    return $this->has($key);
  }

  /**
   * Get the value at a given offset.
   *
   * @param  string  $key
   * @return mixed
   */
  public function offsetGet($key)
  {
    return $this->get($key);
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
    $this->set($key, $value);
  }

  /**
   * Unset the value at a given offset.
   *
   * @param  string  $key
   * @return void
   */
  public function offsetUnset($key)
  {
    Arr::forget($this->values, $key);
  }

  /**
   * Dynamically access container services.
   *
   * @param  string  $key
   * @return mixed
   */
  public function __get($key)
  {
    return $this->get($key);
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
    $this->set($key, $value);
  }

}