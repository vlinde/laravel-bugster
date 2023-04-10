# AdvancedBugster

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]

#### Auto-detects every log file in the storage/logs folder and sorts all the errors by date.
#### Update ^1.1.2 custom categories and directories were added ( check config file )

## Installation

Via Composer

``` bash
$ composer require vlinde/laravel-bugster
```

Publish vendor for migrations then migrate
``` bash
$ php artisan vendor:publish --tag=migrations
$ php artisan vendor:publish --tag=bugster.config
$ php artisan migrate
```
## Usage

Add function to exception handler found in 'app/Exceptions/Handler.php'

```php
public function renderForConsole($output, Throwable $e) 
{
    $bugster = new BugsterLoadBugs();
    $bugster->saveError($output, $e, null, 'TERMINAL');
    
    parent::renderForConsole($output, $e);
}

public function render($request, Throwable $e) 
{
    $render = parent::render($request, $e);

    $bugster = new BugsterLoadBugs;
    $bugster->saveError($request, $e, $render->getStatusCode());
    
    return $render;
}
```

How to move data to SQL

```bash
$ php artisan bugster:movetosql
```

How to delete older bugs

```bash
$ php artisan bugster:delete
```

You can add these commands to cron
```php
$schedule->command('bugster:movetosql')->daily('00:30');
$schedule->command('bugster:delete')->daily('01:00');
$schedule->command('bugster:notify:statistics')->dailyAt('12:00');
$schedule->command('bugster:count-status-codes')->dailyAt('03:00');
```

## Nova
Register Bugster tool in NovaServiceProvider
```php
use Vlinde\Bugster\Bugster;

public function tools()
{
    return [
        new Bugster;
    ];
}
```

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email author email instead of using the issue tracker.

## Credits

- [Vlinde][link-author]

## License

license. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/vlinde/laravel-bugster.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/vlinde/laravel-bugster.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/vlinde/laravel-bugster/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/vlinde/laravel-bugster
[link-downloads]: https://packagist.org/packages/vlinde/laravel-bugster
[link-travis]: https://travis-ci.org/vlinde/laravel-bugster
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/vlinde
