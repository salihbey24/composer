## Usage

Example usage with console:
```php
php artisan salih:install --path={Json File Path}
```

Example usage with instance:
```php
use Salih\Composer\Console\SalihCommand;


$laraJson = new SalihCommand();
$laraJson->install($request->json->getRealPath());
```