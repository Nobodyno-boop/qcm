<?php

namespace Vroom\Container;

interface IContainer
{
    /**
     * Retrieve the container namespace of the current class
     *
     * @return string container namespace
     */
    public static function getContainerNamespace(): string;

    /**
     * Get the current class container with auto-completion friendly
     * @return static
     */
    public static function container(): static;

}