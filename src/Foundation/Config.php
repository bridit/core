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
   * @param  string  $offset
   * @return bool
   */
  public function offsetExists($offset): bool
  {
    return $this->has($offset);
  }

  /**
   * Get the value at a given offset.
   *
   * @param  string  $offset
   * @return mixed
   */
  public function offsetGet($offset): mixed
  {
    return $this->get($offset);
  }

  /**
   * Set the value at a given offset.
   *
   * @param  string  $offset
   * @param  mixed  $value
   * @return void
   */
  public function offsetSet($offset, mixed $value): void
  {
    $this->set($offset, $value);
  }

  /**
   * Unset the value at a given offset.
   *
   * @param  string  $offset
   * @return void
   */
  public function offsetUnset($offset): void
  {
    Arr::forget($this->values, $offset);
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