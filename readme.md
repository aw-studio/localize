# Localize – Translated Routes for Fjord Frontends

Build translated routes in the form of `/en/home`, `/de/startseite` made easy.

## Install

```shell
composer require aw-studio/localize
```

## Usage

Make shure to translate your routes within your translation-files in the `resources` directory, e.g.:

```php
// lang/de/routes.php

return [
    'home' => 'startseite'
];
```

You can now simply add translated Routes to your preferred routes file, e.g. `routes\web.php`:

```php
Route::trans('/__(routes.home)', HomeController::class)->name('home');
```

## Translated Routes in Blade

In your blade-files, simply use the `__route()` helper like this:

```php
<a href="{{ __route('home') }}">
    ...
</a>
```

### Switching languages

Use the `__routes()` helper to retrieve an array of routes to all language versions of the current route. If your route uses translated parameters these need to be passed.

```php
@foreach (__routes($routeParameters ?? null) as $route)
    <a href="{{ $route->link }}">
        {{ $route->locale }}
    </a>
@endforeach
```

## Translated Route Parameters

Imagine your route takes a translated slug as a parameter, a blog post for example:

`/en/posts/my-first-post`
`/de/beitraege/mein-erster-beitrag`

Your route should look something like this:

```php
Route::trans('/__(routes.posts)/{slug}', PostController::class)->name('posts.show');
```

In your controller, you can retrieve the post using the `whereHas` method

```php
$post = Post::whereHas('translations', function($query) use($slug) {
        $query->where('slug', $slug)->where('locale', app()->getLocale());
    })
    ->with('translations')
    ->first();
```

In order to retrieve the post's links to all other languages, we have pass the translated route parameters like this:

```php
$slugs = $post->translations->mapWithKeys(function($item) {
    return [$item->locale => $item->slug];
})->toArray();


return view('posts.show')->with([
    'post' => $post,
    'routeParameters' => ['slug' => $slugs]
]);
```

In your view you can now use the `__routes()` function passing it the relevant parameters:

```php
@foreach (__routes($routeParameters ?? null) as $route)
    {{ $route->link }}
@endforeach
```
