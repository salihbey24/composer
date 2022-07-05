## Usage

Example code:
```php
$fileContent = json_decode(File::get($request->json->getRealPath()));

        foreach ($fileContent as $item) {

            $name=$item->name;
            $forms = $item->forms;

            $fields=reset($forms)->fields;

            Artisan::call("make:model",[
                'name' => $name,
            ]);

            Artisan::call("make:migration",[
                'name' => "create{$name}_table",
            ]);

            $migrationFile= explode("Created Migration: ",Artisan::output());
            $migrationFile=trim(end($migrationFile));

            Larajson::generateStub();

            Artisan::call("make:controller",[
                '--force' => true,
                '--resource' => true,
                '--model'=>$name,
                'name' => "{$name}Controller",
                '--type' =>'custom'
            ]);

            Larajson::generateMigration($name,$migrationFile,$fields);
            Larajson::generateModel($name);
            Larajson::generateController($name);
            Larajson::generateView($name,$forms);

            Larajson::generateRoutes($name);

        }

        Artisan::call("migrate");
        Artisan::call("optimize");
```