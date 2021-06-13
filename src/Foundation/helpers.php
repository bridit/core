<?php

if (! function_exists('app')) {
  /**
   * Get the available application instance.
   *
   * @param  string|null  $abstract
   * @param  array  $parameters
   * @return mixed|\Brid\Core\Foundation\Application
   */
  function app($abstract = null, array $parameters = [])
  {
    if (is_null($abstract)) {
      return \Brid\Core\Foundation\Application::getInstance();
    }

    return \Brid\Core\Foundation\Application::getInstance()->make($abstract, $parameters);
  }
}

if (! function_exists('path')) {
  /**
   * Get the path to the base of the install.
   *
   * @param  string  $path
   * @return string
   */
  function path(string $path = ''): string
  {
    return app()->getBasePath() . \Illuminate\Support\Str::start($path, '/');
  }
}

if (! function_exists('storage_path')) {
  /**
   * Get the path to the storage of the install.
   *
   * @param  string  $path
   * @return string
   */
  function storage_path(string $path = ''): string
  {
    return app()->getStoragePath() .  \Illuminate\Support\Str::start($path, '/');
  }
}

if (! function_exists('config')) {
  /**
   * @param string $key
   * @param mixed|null $default
   * @return mixed
   */
  function config(string $key, mixed $default = null): mixed
  {
    return \Illuminate\Support\Arr::get(app()->get('config') ?? [], $key, $default);
  }
}

if (!function_exists('unaccent')) {

  function unaccent(string $value): string
  {
    return strtr($value, ['Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
      'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
      'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
      'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
      'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y']);
  }

}
