<?php

namespace AwStudio\Localize;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use AwStudio\Localize\TranslatableRoutes;

class LocalizationServiceProvider extends ServiceProvider
{
    private $locales;
    private $fallback_locale;

    public function __construct()
    {
        $this->locales = config('translatable.locales');
        $this->fallback_locale = config('translatable.fallback_locale');
    }

    public function boot(Request $request)
    {
        $locales = $this->locales;
        Route::macro('trans', function ($route, $controller) use ($locales) {
            if (is_array($controller)) {
                $controller = $controller[0] . '@' . $controller[1];
            }

            $routes = [];
            foreach ($locales as $locale) {
                $routeString = $route;
                preg_match_all('/\__\((.*?)\)/si', $route, $matches);
                foreach ($matches[1] as $param) {
                    $routeString = str_replace(
                        '__(' . $param . ')',
                        __(trim($param), [], $locale),
                        $routeString
                    );
                }

                $routes[] = Route::prefix($locale)
                    ->as("{$locale}.")
                    ->get($routeString, $controller);
            }

            return new TranslatableRoutes($routes);
        });

        if (app()->runningInConsole()) {
            return;
        }
        $locale = $request->segment(1);

        if (!in_array($locale, $this->locales) && $locale === null) {
            /**
             * Set the language to the client's preferred language
             * according to his browser settings if no parameter is given
             * in the current route
             */
            $this->localize($request);
        }
        if (!in_array($locale, $this->locales)) {
            return;
        }

        app()->setLocale($locale);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    private function localize($request)
    {
        $userLangs = preg_split('/,|;/', $request->server('HTTP_ACCEPT_LANGUAGE') ?? '');
        
        $userLangs = collect($userLangs)->map(function ($lng) {
            return substr($lng, 0, 2);
        })->toArray();
        
        foreach ($userLangs as $userLang) {
            if (in_array($userLang, $this->locales)) {
                redirect("/{$userLang}")->send();
            }
        }
        redirect("/{$this->fallback_locale}")->send();
    }
}
