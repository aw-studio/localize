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
