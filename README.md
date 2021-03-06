# AutoTune for Composer

<img src="http://upr.io/xpnegd.jpg" style="width: 100%" />

## Why use AutoTune for Composer?

AutoTune is great for library developers.

Often you're working on a library and the calling application at the same time.

Before AutoTune there were 2 things you could do to test your library in your application:

1. Commit new version of the library for all changes, wait for packagist to re-index, and update your composer.lock in the calling application and test.
2. Add a "repository" to your calling application's composer.json (which you shouldn't forget to remove during commit, and put back after)

Both are cumbersome. This is where AutoTune comes in!

## How does autotune work?

AutoTune expects to find a `autotune.json` in your calling application's path, next to composer.json. Here's an example:

```json
{
    "autoload": {
        "psr-4": {
            "Monolog\\": "~/git/monolog/monolog/src/Monolog"
        }
    }
}
```

If this file is found, it will read the contents, and override any psr-0 or psr-4 namespaces with the local version as defined in your autotune.json file.

You can add the autotune.json to your `.gitignore` file, so that you can keep your code directory clean and in sync with the remote repository.

## Making your own application ready for AutoTune

Making your own application ready for AutoTune takes 3 simple steps:

### 1. Include `linkorb/autotune` from Packagist in your composer.json file

```json
require-dev": {
   "linkorb/autotune": "~1.0"
}
```

Then run `composer update`

### 2. Initialize AutoTune in your app

Somewhere in your application, you're including `vendor/autoload.php`. Sometimes it's in `web/index.php` or `bin/console`. Find this location, and modify add these lines:

```php
$loader = require_once __DIR__.'/../vendor/autoload.php';
if (class_exists('AutoTune\Tuner')) {
    \AutoTune\Tuner::init($loader);
}
```
Wrapping the call to `init` in the `class_exists` block ensures autotune is only used if AutoTune is installed in your (development) environment (installed from the require-dev block in composer.json). In production environments it won't be called if you install your dependencies with `--no-dev`)

### 3. Add an `autotune.json` file to your project root.

Example content:

```json
{
    "autoload": {
        "psr-4": {
            "Monolog\\": "~/git/monolog/monolog/src/Monolog"
        }
    }
}
```

Ideally you'd add the `autotune.json` to your `.gitignore` file.

### Done

Whenever your application is doing something like the following, it will load the "local" version of a library, instead of the one in your `vendor/` directory.

```php
$logger = new \Monolog\Logger('example');
```

So from now on, no changes are required to your main application. Everything is managed by your local `autotune.json` file.

## License

MIT (see [LICENSE.md](LICENSE.md))

## Brought to you by the LinkORB Engineering team

<img src="http://www.linkorb.com/d/meta/tier1/images/linkorbengineering-logo.png" width="200px" /><br />
Check out our other projects at [linkorb.com/engineering](http://www.linkorb.com/engineering).

Btw, we're hiring!
