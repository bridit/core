<?php

namespace Brid\Core\Contracts\Foundation;

use DI\Definition\Source\MutableDefinitionSource;
use DI\FactoryInterface;
use DI\Proxy\ProxyFactory;
use Invoker\InvokerInterface;
use Psr\Container\ContainerInterface;

interface Container extends ContainerInterface, FactoryInterface, InvokerInterface
{

  public function __construct(
    MutableDefinitionSource $definitionSource = null,
    ProxyFactory $proxyFactory = null,
    ContainerInterface $wrapperContainer = null
  );

}