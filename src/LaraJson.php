<?php

namespace Salih\Composer;

use Illuminate\Support\Facades\File;

class LaraJson
{
    public static function generateStub()
    {
        $stubsDir = base_path("stubs");

        $content =
            <<<STUB
               <?php
               namespace {{ namespace }};

               use {{ namespacedModel }};
               use {{ rootNamespace }}Http\Controllers\Controller;
               use {{ namespacedRequests }}
               use Salih\Composer\LaraJson;

               class {{ class }} extends Controller
               {
                   /**
                    * Display a listing of the resource.
                    *
                    * @return \Illuminate\Http\Response
                    */
                   public function index()
                   {

                   }

                   /**
                    * Show the form for creating a new resource.
                    *
                    * @return \Illuminate\Http\Response
                    */
                   public function create()
                   {

                   }

                   /**
                    * Store a newly created resource in storage.
                    *
                    * @param  \{{ namespacedStoreRequest }}  \$request
                    * @return \Illuminate\Http\Response
                    */
                   public function store({{ storeRequest }} \$request)
                   {

                   }

                   /**
                    * Display the specified resource.
                    *
                    * @param  \{{ namespacedModel }}  \${{ modelVariable }}
                    * @return \Illuminate\Http\Response
                    */
                   public function show({{ model }} \${{ modelVariable }}, \$id)
                   {

                   }

                   /**
                    * Show the form for editing the specified resource.
                    *
                    * @param  \{{ namespacedModel }}  \${{ modelVariable }}
                    * @return \Illuminate\Http\Response
                    */
                   public function edit({{ model }} \${{ modelVariable }}, \$id)
                   {

                   }

                   /**
                    * Update the specified resource in storage.
                    *
                    * @param  \{{ namespacedUpdateRequest }}  \$request
                    * @param  \{{ namespacedModel }}  \${{ modelVariable }}
                    * @return \Illuminate\Http\Response
                    */
                   public function update({{ updateRequest }} \$request, {{ model }} \${{ modelVariable }}, \$id)
                   {

                   }

                   /**
                    * Remove the specified resource from storage.
                    *
                    * @param  \{{ namespacedModel }}  \${{ modelVariable }}
                    * @return \Illuminate\Http\Response
                    */
                   public function destroy({{ model }} \${{ modelVariable }}, \$id)
                   {

                   }
               }

               STUB;

        if (!File::exists($stubsDir)) {
            File::makeDirectory($stubsDir, 0775, true, true);
        }
        File::put("$stubsDir\controller.custom.stub", $content);
    }

    public static function generateController($name)
    {
        $controllerFile = app_path("Http/Controllers/{$name}Controller.php");
        $controllerFunctions = ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'];

        $file = file($controllerFile, FILE_IGNORE_NEW_LINES);

        $cols = LaraJson::getTableColumns($name);

        $validator =
            <<<VALIDATORHEADER
                \$validator=\Illuminate\Support\Facades\Validator::make(\$request->all(), [
                VALIDATORHEADER;

        foreach ($cols as $col) {
            $validator.="'$col' => 'required',";
        }
        $validator.=
        <<<VALIDATORFOOTER
                ]);
                if (\$validator->fails()) {

                    return back()
                        ->withErrors(\$validator)
                        ->withInput();
                }
                VALIDATORFOOTER;

        foreach ($controllerFunctions as $function) {

            $content = match ($function) {
                'index' =>
                <<<INDEX
                    \$items = $name::all();
                    return view('$name/List')->with(['items' => \$items]);
                    INDEX,
                'create' =>
                <<<CREATE
                    return view('$name/Create');
                    CREATE,
                'store' =>
                <<<STORE
                    $validator
                    \$cols = LaraJson::getTableColumns(basename(str_replace('Controller', '', get_class(\$this))));
                    \$data=new $name();
                    foreach(\$request->all() as \$k => \$v)
                        {
                            if(in_array(\$k, \$cols)){
                                \$data->\$k = \$v;
                            }
                        }
                    \$data->save();

                    return redirect("$name/edit/\$data->id")->with(['status'=>'success','text'=>'başarıyla oluşturuldu.']);
                    STORE,
                'show' =>
                <<<SHOW
                    \$item = $name::find(\$id);
                    return view('$name/show',['item'=>\$item]);
                    SHOW,
                'edit' =>
                <<<EDIT
                    \$item = $name::find(\$id);
                    return view('$name/edit',['item'=>\$item]);
                    EDIT,
                'update' =>
                <<<UPDATE
                    $validator
                    \$cols = LaraJson::getTableColumns(basename(str_replace('Controller', '', get_class(\$this))));
                    \$data= $name::find(\$id);
                    foreach(\$request->all() as \$k => \$v)
                        {
                            if(in_array(\$k, \$cols)){
                                \$data->\$k = \$v;
                            }
                        }
                    \$data->save();
                    return redirect('$name')->with(['status'=>'success','text'=>'başarıyla güncellendi.']);
                    UPDATE,
                'destroy' =>
                <<<DESTROY
                    $name::destroy(\$id);
                    return redirect('$name')->with(['status'=>'success','text'=>'başarıyla silindi.']);
                    DESTROY,

            };


            $regex = "~function $function~";
            $result = array_filter($file, function ($item) use ($regex) {
                return preg_match($regex, $item);
            });
            array_splice($file, array_key_first($result) + 3, 0, $content);

        }

        File::put($controllerFile, join("\n", $file));
    }

    public static function generateView($name, $forms)
    {
        $viewDir = base_path("resources\\views\\$name");
        foreach ($forms as $form) {

            $content = '';
            if ($form->type == 'create') {

                $content .=
                    <<<HEADER
                        @extends('Layouts.master')
                        @section('Content')

                            @if (\$errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach (\$errors->all() as \$error)
                                            <li>{{ \$error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            {!! Form::open(['action' => '\App\Http\Controllers\\{$name}Controller@create', 'method' => '$form->method', 'files' => true]) !!}

                            <div class="row mt-3 d-flex justify-content-center">
                        HEADER;

                foreach ($form->fields as $field) {
                    $field->validation = $field->validation == 'required' ? ",'required' =>'true'" : '';
                    $content .= LaraJson::generateInput($field);

                }

                $content .=
                    <<<FOOTER
                             <div class="mb-3 col-3 ">
                                 {{Form::submit('Oluştur',\$attributes=['class'=>'btn btn-success'])}}
                            </div>
                        </div>
                        {!! Form::close() !!}
                    @endsection
                    FOOTER;
            } elseif ($form->type == 'edit') {
                $content .=
                    <<<HEADER
                        @extends('Layouts.master')
                        @section('Content')
                            @if (\$errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach (\$errors->all() as \$error)
                                            <li>{{ \$error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            {!! Form::open(['action' => ['\App\Http\Controllers\\{$name}Controller@update',\$item->id], 'method' => '$form->method', 'files' => true]) !!}
                            <div class="row mt-3 d-flex justify-content-center">
                        HEADER;
                foreach ($form->fields as $field) {
                    $field->validation = $field->validation == 'required' ? ",'required' =>'true'" : '';
                    $content .= LaraJson::generateInput($field);

                }
                $content .=
                    <<<FOOTER
                             <div class="mb-3 col-3 ">
                                 {{Form::submit('Kaydet',\$attributes=['class'=>'btn btn-success'])}}
                            </div>
                        </div>
                        {!! Form::close() !!}
                    @endsection
                    FOOTER;
            }

            if (!File::exists($viewDir)) {
                File::makeDirectory($viewDir, 0775, true, true);
            }
            $type = ucfirst($form->type);
            File::put("$viewDir\\$type.blade.php", $content);
        }
    }

    public static function generateModel($name)
    {
        $modelFile = app_path("Models/{$name}.php");

        $file = file($modelFile, FILE_IGNORE_NEW_LINES);

        $regex = "~$name~";

        $content = <<<MODEL
                        protected \$table ='$name';
                    MODEL;
        $result = array_filter($file, function ($item) use ($regex) {
            return preg_match($regex, $item);
        });
        array_splice($file, array_key_first($result) + 3, 0, $content);

        File::put($modelFile, join("\n", $file));
    }

    public static function generateMigration($name, $file, $fields)
    {
        $migrateFile = base_path("database/migrations/$file.php");

        $file = file($migrateFile, FILE_IGNORE_NEW_LINES);

        $name = strtolower($name);
        $regex = "~$name~";

        $content = '';
        foreach ($fields as $field) {
            if (str_starts_with($field->name, 'confirm'))
                continue;

            $content .=
                <<<MIGRATE
                       \$table->string('$field->name');
                       \r\n
                   MIGRATE;
        }

        $result = array_filter($file, function ($item) use ($regex) {
            return preg_match($regex, $item);
        });
        array_splice($file, array_key_first($result) + 2, 0, $content);

        File::put($migrateFile, join("\n", $file));

    }

    public static function generateRoutes($name)
    {
        $routerDir = './routes';

        $content =
            <<<ROUTER
            Route::controller(\App\Http\Controllers\\{$name}Controller::class)->group(function () {

                Route::get('/$name', 'index');
                Route::get('/$name/create', 'create');
                Route::post('/$name/create', 'store');
                Route::get('/$name/{id}', 'show');
                Route::get('/$name/edit/{id}', 'edit');
                Route::post('/$name/edit/{id}', 'update');
                Route::get('/$name/destroy/{id}', 'destroy');
            });
            ROUTER;

        if (!File::exists($routerDir)) {
            File::makeDirectory($routerDir, 0775, true, true);
            File::put("$routerDir/routes.php", "<?php \r\n");

            $nameSpace = strtolower(str_replace('\\','/',__NAMESPACE__));
            $routesContent =
                <<<ROUTES
                    require base_path('vendor/{$nameSpace}/routes/routes.php');
                    \r\n
                    ROUTES;

            File::append(base_path('routes/web.php'), $routesContent);
        }
        File::append("$routerDir/routes.php", $content);
    }

    private function fieldMapping($field)
    {
        $tableFields = [];

        return $tableFields[$field->type];
    }

    private static function generateInput($field)
    {

        $options='';
        if($field->type=='select')
        {
            $options.='[';

            foreach ($field->value as $key => $value) {
                $options.="'$key'=>'$value',";
            }
            $options.=']';
        }

        $attr ='[';
        foreach ($field->attributes as $key => $value) {
            $attr.="'$key'=>'$value',";
        }

        if(str_contains($field->validation,'required'))
        {
            $attr.="'required'=>'true',";
        }

        $attr.=']';

        $label=
            <<<LABEL
                {{Form::label('$field->name', '$field->name', ['class' => 'form-label','for'=> '$field->name'])}}
                LABEL;

        $input='';
        if($field->type=='text')
        {
            $input=
                <<<INPUT
                    {{Form::text('$field->name','$field->value',\$attributes=$attr)}}
                    INPUT;
        }elseif ($field->type=='password') {
            $input =
                <<<INPUT
                    {{Form::password('$field->name',\$attributes=$attr)}}
                    INPUT;
        }elseif ($field->type=='email') {
            $input =
                <<<INPUT
                    {{Form::email('$field->name','$field->value',\$attributes=$attr)}}
                    INPUT;
        }
        elseif ($field->type=='hidden')
        {
            $input=
                <<<INPUT
                    {{Form::hidden('$field->name','$field->value',\$attributes=$attr)}}
                    INPUT;
        }
        elseif ($field->type=='select')
        {
            $input=
                <<<INPUT
                    {{Form::select('$field->name',$options,'$field->value',\$attributes=$attr)}}
                    INPUT;
        }
        elseif ($field->type=='date')
        {
            $input=
                <<<INPUT
                    {{Form::date('$field->name',\Carbon\Carbon::now())}}
                    INPUT;
        }
        elseif ($field->type=='checkbox')
        {
            foreach ($field->value as $value) {
                $input.=
                    <<<INPUT
                        {{Form::checkbox('$field->name','$value')}}
                        INPUT;
            }
        }
        elseif ($field->type=='radio')
        {
            foreach ($field->value as $value) {
                $input.=
                    <<<INPUT
                        {{Form::radio('$field->name','$value')}}
                        INPUT;
            }
        }

        return
            <<<RETURN
                <div class="mb-3 col-3">
                    $label
                    $input
                </div>
                RETURN;

    }

    public static function getTableColumns($model)
    {
        $cols = \Schema::getColumnListing(app("\App\Models\\$model")->getTable());

        $cols = array_diff($cols, ['id', 'app', 'string', 'number', 'created_at', 'updated_at']);

        $cols = array_values($cols);

        return $cols;
    }

}
