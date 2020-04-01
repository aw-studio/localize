<?php

namespace AwStudio\Localize;

class TranslatableRoutes
{
    protected $routes;

    public function __construct($routes)
    {
        $this->routes = $routes;
    }

    public function __call($method, $params)
    {
        foreach ($this->routes as $index => $route) {
            $this->routes[$index] = $route->$method(...$params);
        }

        return $this;
    }
}
