<?php

declare(strict_types=1);

require 'vendor/autoload.php';

$container = new League\Container\Container();
$container->delegate(
    new League\Container\ReflectionContainer
);

class A {
    public $a = 'hello from a';
}

class B extends A {

    public $b = 'hello from b';
}

$container
    ->add(B::class)
    ->setShared()
    ->addTag(A::class);


var_dump($container->get(A::class));