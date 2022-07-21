## Usage
To get version:
```php
php artisan salih:version
```

Example usage with console:
```php
php artisan salih:install --path={Json File Path}
```

Example usage with instance:

```php
use Salih\Composer\Console\InstallCommand;


$laraJson = new InstallCommand();
$laraJson->install($request->json->getRealPath());
```