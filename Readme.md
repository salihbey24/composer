## Usage

Example code for LaraJsonStatic:
```php
LaraJsonStatic::generate($request->json->getRealPath());
```

Example code for LaraJson:
```php
$laraJson = new LaraJson();
$laraJson->generate($request->json->getRealPath());
```