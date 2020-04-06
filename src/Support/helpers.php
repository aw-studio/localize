<?php

if (!function_exists('__route')) {
    function __route($string, ...$parameters)
    {
        return route(app()->getLocale() . '.' . $string, ...$parameters);
    }
}

if (!function_exists('__routes')) {

    /**
     * Returns an array of the language equivalent routes for the current route
     *
     * @return array
     */
    function __routes($routeParameters = null): array
    {
        // dd(Request::route()->parameters);
        $routes = [];
        foreach (config('translatable.locales') as $locale) {

            if (!Request::route()->getName()) {
                continue;
            }

            $routeName = Str::replaceFirst(
                app()->getLocale(),
                $locale,
                Request::route()->getName()
            );


            $routeParams = [];

            foreach ($routeParameters ?? [] as $key => $values) {
                $routeParams[$key] = $values[$locale] ?? null;
            }

            if (empty($routeParams)) {
                $routeParams = Request::route()->parameters;
            }

            $routes[$locale] = (object) [
                'locale' => $locale,
                'name' => $routeName,
                'params' => $routeParams,
                'link' => route($routeName, $routeParams),
                'active' => app()->getLocale() == $locale
            ];
        }


        return $routes;
    }
}


if (!function_exists('isActive')) {
    function isActive($route, $class = "current")
    {
        if (strpos(Request::fullUrl(), $route) === 0 || $route == Request::fullUrl()) {
            return $class;
        }
    }
}
