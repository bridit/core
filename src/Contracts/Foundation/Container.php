<?php

namespace Brid\Core\Contracts\Foundation;

use DI\Definition\Source\MutableDefinitionSource;
use DI\Proxy\ProxyFactory;
use Psr\Container\ContainerInterface;

interface Container
{

  public function __construct(
    MutableDefinitionSource $definitionSource = null,
    ProxyFactory $proxyFactory = null,
    ContainerInterface $wrapperContainer = null
  );

}