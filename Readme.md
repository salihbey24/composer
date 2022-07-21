## Usage

Example usage with console:
```php
php artisan salih:install --path={Json File Path}
php artisan salih --version
```

Example usage with instance:

```php
use Salih\Composer\Console\InstallCommand;


$laraJson = new InstallCommand();
$laraJson->install($request->json->getRealPath());
```