# AdvancedBugster

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]

This is where your description should go. Take a look at [contributing.md](contributing.md) to see a to do list.

## Installation

Via Composer

``` bash
$ composer require vlinde/advancedbugster
```

Add redis configuration to database.php in config folder ( more information can be found in config/advancedbugster.php )

Publish vendor for migrations then migrate
``` bash
$ php artisan vendor:publish --tag=migrations
$ php artisan migrate
```
## Usage

Add function to exception handler found in 'app/Exceptions/Handler.php'

```php
$bugster = new \Vlinde\AdvancedBugster\Classes\BugsterLoadBugs();
$bugster->saveError(Request $request,\Exception $exception); 
```

How to move data to SQL

```bash
$ php artisan bugster:movetosql
```

How to generate daily stats from the errors

```bash
$ php artisan bugster:generate:stats
```

How to delete older bugs

```bash
$ php artisan bugster:delete
```

You can add these commands to a daily cron
```php
// found in app/Console/Kernel -> schedule

$schedule->command('bugster:movetosql')->daily('00:30');
$schedule->command('bugster:generate:stats')->daily('00:45');
$schedule->command('bugster:delete')->daily('01:00');
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

[ico-version]: https://img.shields.io/packagist/v/vlinde/advancedbugster.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/vlinde/advancedbugster.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/vlinde/advancedbugster/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/vlinde/advancedbugster
[link-downloads]: https://packagist.org/packages/vlinde/advancedbugster
[link-travis]: https://travis-ci.org/vlinde/advancedbugster
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/vlinde