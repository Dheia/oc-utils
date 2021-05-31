<?php namespace Waka\Utils\Console;

use October\Rain\Scaffold\GeneratorCommand;
use October\Rain\Support\Collection;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Twig;

class CreateModelController extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'waka:mc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new model and controller.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Model';

    /**
     * A mapping of stub to generated file.
     *
     * @var array
     */

    protected $controllerPhpStubs = [

        'controller/config_form.stub' => 'controllers/{{lower_ctname}}/config_form.yaml',
        'controller/config_list.stub' => 'controllers/{{lower_ctname}}/config_list.yaml',
        'controller/config_btns.stub' => 'controllers/{{studly_ctname}}/config_btns.yaml',
        'controller/controller.stub' => 'controllers/{{studly_ctname}}.php',

    ];
    protected $controllerHtmStubs = [
        'controller/_list_toolbar.stub' => 'controllers/{{lower_ctname}}/_list_toolbar.htm',
        'controller/create.stub' => 'controllers/{{lower_ctname}}/create.htm',
        'controller/index.stub' => 'controllers/{{lower_ctname}}/index.htm',
        'controller/preview.stub' => 'controllers/{{lower_ctname}}/preview.htm',
        'controller/update.stub' => 'controllers/{{lower_ctname}}/update.htm',
        'controller/sidebar_info.stub' => 'controllers/{{lower_ctname}}/_sidebar_info.htm',

    ];
    protected $modelYamlstubs = [
        'model/fields.stub' => 'models/{{lower_name}}/fields.yaml',
        'model/columns.stub' => 'models/{{lower_name}}/columns.yaml',
    ];

    protected $stubs = [];

    public $pluginObj = [];

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        $this->vars = $this->processVars($this->prepareVars());

        if ($this->maker['model']) {
            /**/trace_log('on fait le modele');
            $this->stubs['model/model.stub'] = 'models/{{studly_name}}.php';
        }
        if ($this->maker['update']) {
            /**/trace_log('on fait le migrateur du modele');
            $this->stubs['model/create_table.stub'] = 'updates/create_{{snake_plural_name}}_table.php';
            //trace_log($this->version);
            if ($this->version) {
                $this->stubs['model/create_update.stub'] = 'updates/create_{{snake_plural_name}}_table_u{{ version }}.php';
            }
        }
        if ($this->maker['lang_field_attributes'] || $this->maker['only_langue']) {
            $this->stubs['model/temp_lang.stub'] = 'lang/fr/{{lower_name}}.php';
        }
        if ($this->maker['lang_field_attributes'] || $this->maker['only_attribute']) {
            if (!$this->config['no_attributes_file'] ?? false) {
                $this->stubs['model/attributes.stub'] = 'models/{{lower_name}}/attributes.yaml';
            }
        }
        if ($this->maker['lang_field_attributes']) {
            /**/trace_log('on fait les langues fields et attributs');
            if ($this->fields_create) {
                $this->stubs['model/fields_create.stub'] = 'models/{{lower_name}}/fields_create.yaml';
            }
            $this->stubs = array_merge($this->stubs, $this->modelYamlstubs);

            if ($this->config['use_tab']) {
                unset($this->stubs['model/fields.stub']);
                $this->stubs['model/fields_tab.stub'] = 'models/{{lower_name}}/fields.yaml';
            }
            // if ($this->config['belong'] && $this->yaml_for) {
            //     foreach ($this->config['belong'] as $relation) {
            //         $this->makeOneStub('model/fields.stub', 'models/' . strtolower($this->w_model) . '/fields_for_' . $relation['relation_name'] . '.yaml', $this->vars);
            //         $this->makeOneStub('model/columns.stub', 'models/' . strtolower($this->w_model) . '/columns_for_' . $relation['relation_name'] . '.yaml', $this->vars);
            //     }
            // }
        }

        if ($this->maker['controller']) {
            /**/trace_log('on fait le controlleur et les configs');
            $this->stubs = array_merge($this->stubs, $this->controllerPhpStubs);
            if ($this->config['behav_duplicate'] ?? false) {
                $this->stubs['controller/config_duplicate.stub'] = 'controllers/{{lower_ctname}}/config_duplicate.yaml';
            }
            if ($this->config['side_bar_attributes'] ?? false) {
                $this->stubs['controller/config_attributes.stub'] = 'controllers/{{lower_ctname}}/config_attributes.yaml';
            }
            if ($this->config['side_bar_info'] ?? false) {
                $this->stubs['controller/config_sidebar_info.stub'] = 'controllers/{{lower_ctname}}/config_sidebar_info.yaml';
            } else {
                unset($this->stubs['controller/sidebar_info.stub']);
            }
            if ($this->config['behav_lots'] ?? false) {
                $this->stubs['controller/config_lots.stub'] = 'controllers/{{lower_ctname}}/config_lots.yaml';
            }
            if ($this->config['behav_workflow'] ?? false) {
                $this->stubs['controller/config_workflow.stub'] = 'controllers/{{lower_ctname}}/config_workflow.yaml';
            }
            if ($this->config['filters'] ?? false) {
                $this->stubs['controller/config_filter.stub'] = 'controllers/{{lower_ctname}}/' . $this->config['filters'] . '.yaml';
            }
            //trace_log("--MORPHMANY--");
            //trace_log($this->config['morphmany'] ?? null);
            $relationConfigExiste = 0;
            if (($this->config['many'] || $this->config['morphMany']) && $this->yaml_for) {
                foreach ($this->config['many'] as $relation) {
                    if($relation['createInController']) {
                        $relationConfigExiste++;
                        $this->makeOneStub('controller/_field_relation.stub', 'controllers/' . strtolower($this->w_model) . 's/_field_{{relation_name}}.htm', $relation);
                        if ($relation['createYamlRelation']) {
                            $this->makeOneStub('model/fields_for.stub', 'models/' . $relation['singular_name'] . '/fields_for_' . strtolower($this->w_model) . '.yaml', []);
                            $this->makeOneStub('model/columns_for.stub', 'models/' . $relation['singular_name'] . '/columns_for_' . strtolower($this->w_model) . '.yaml', []);
                        }
                    }
                    
                }
                foreach ($this->config['morphMany'] as $relation) {
                    if($relation['createInController']) {
                        $relationConfigExiste++;
                        $this->makeOneStub('controller/_field_relation.stub', 'controllers/' . strtolower($this->w_model) . 's/_field_{{relation_name}}.htm', $relation);
                        if ($relation['createYamlRelation']) {
                            $this->makeOneStub('model/fields_for.stub', 'models/' . $relation['singular_name'] . '/fields_for_' . strtolower($this->w_model) . '.yaml', []);
                            $this->makeOneStub('model/columns_for.stub', 'models/' . $relation['singular_name'] . '/columns_for_' . strtolower($this->w_model) . '.yaml', []);
                        }
                    }
                }
            }
             //trace_log("--BELONG--");
            //trace_log($this->config['belong'] ?? null);
            if (($this->config['belong'] || $this->config['oneThrough']) && $this->yaml_for) {
                foreach ($this->config['belong'] as $relation) {
                    if($relation['createInController']) {
                        $relationConfigExiste++;
                        $this->makeOneStub('controller/_field_relation.stub', 'controllers/' . strtolower($this->w_model) . 's/_field_{{relation_name}}.htm', $relation);
                        if ($relation['createYamlRelation']) {
                            $this->makeOneStub('model/fields_for.stub', 'models/' . $relation['singular_name'] . '/fields_for_' . strtolower($this->w_model) . '.yaml', []);
                            $this->makeOneStub('model/columns_for.stub', 'models/' . $relation['singular_name'] . '/columns_for_' . strtolower($this->w_model) . '.yaml', []);
                        }
                    }
                }
                foreach ($this->config['oneThrough'] as $relation) {
                    if($relation['createInController']) {
                        $relationConfigExiste++;
                        $this->makeOneStub('controller/_field_relation.stub', 'controllers/' . strtolower($this->w_model) . 's/_field_{{relation_name}}.htm', $relation);
                        if ($relation['createYamlRelation']) {
                            $this->makeOneStub('model/fields_for.stub', 'models/' . $relation['singular_name'] . '/fields_for_' . strtolower($this->w_model) . '.yaml', []);
                            $this->makeOneStub('model/columns_for.stub', 'models/' . $relation['singular_name'] . '/columns_for_' . strtolower($this->w_model) . '.yaml', []);
                        }
                    }
                }
            }
            if($relationConfigExiste) {
                $this->stubs['controller/config_relation.stub'] = 'controllers/{{lower_ctname}}/config_relation.yaml';
            }
            
        }
        if ($this->maker['html_file_controller']) {
            $this->stubs = array_merge($this->stubs, $this->controllerHtmStubs);
            if ($this->config['side_bar_attributes'] || $this->config['side_bar_info']) {
                unset($this->stubs['controller/update.stub']);
                $this->stubs['controller/update_sidebar.stub'] = 'controllers/{{lower_ctname}}/update.htm';
            }
            if ($this->config['behav_reorder']) {
                $this->stubs['controller/reorder.stub'] = 'controllers/{{lower_ctname}}/reorder.htm';
                $this->stubs['controller/config_reorder.stub'] = 'controllers/{{lower_ctname}}/config_reorder.yaml';
            }
        }

        if ($this->maker['excel']) {
            $this->stubs['imports/import.stub'] = 'classes/imports/{{studly_ctname}}Import.php';
        }

        $this->makeStubs();

        $this->info($this->type . 'created successfully.');
    }

    /**
     * Prepare variables for stubs.
     *
     * return @array
     */
    protected function prepareVars()
    {
        //trace_log("start");
        $pluginCode = $this->argument('plugin');

        $parts = explode('.', $pluginCode);
        $this->w_plugin = array_pop($parts);
        $this->w_author = array_pop($parts);

        $this->w_model = $this->argument('model');

        // $values = $this->ask('Coller des valeurs excels ', true);
        // trace_log($values);

        $fileName = 'start';

        if ($this->argument('src')) {
            $fileName = $this->argument('src');
        }
        $startPath = null;
        //trace_log($this->w_author);
        if($this->w_author == 'waka') {
            $startPath = env('SRC_WAKA');
        } 
        if($this->w_author == 'wcli') {
            //trace_log(env('SRC_WCLI','merde'));
            $startPath = env('SRC_WCLI');
        }

        $filePath =  $startPath.'/'.$fileName.'.xlsx';

        $this->maker = [
            'model' => true,
            'lang_field_attributes' => true,
            'only_langue' => false,
            'only_attribute' => false,
            'update' => true,
            'controller' => true,
            'html_file_controller' => true,
            'excel' => true,

        ];
        $this->version = null;
        $this->yaml_for = true;

        if ($this->option('option')) {
            $this->maker = [
                'model' => false,
                'lang_field_attributes' => false,
                'only_langue' => false,
                'only_attribute' => false,
                'update' => false,
                'controller' => false,
                'html_file_controller' => false,
                'excel' => false,

            ];
            $types = $this->choice('Database type', ['model', 'lang_field_attributes', 'only_langue', 'only_attribute', 'update', 'controller', 'html_file_controller', 'excel'], 0, null, true);
            //trace_log($types);
            foreach ($types as $type) {
                $this->maker[$type] = true;
                if ($type == 'update') {
                    $this->version = $this->ask('version');
                }
                if ($type == 'lang_field_attributes') {
                    $this->yaml_for = $this->ask('yaml_for');
                }
                if ($type == 'controller') {
                    $this->yaml_for = $this->ask('yaml_for');
                }
            }
        }

        //trace_log($this->maker);

        $importExcel = new \Waka\Utils\Classes\Imports\ImportModelController($this->w_model);
        \Excel::import($importExcel, $filePath);
        $rows = new Collection($importExcel->data->data);
        $this->config = $importExcel->config->data;

        // $relationName = null;
        // $pluginRelationName = null;

        $rows = $rows->map(function ($item, $key) {
            $trigger = $item['trigger'] ?? null;
            if ($trigger) {
                if (starts_with($trigger, '!')) {
                    $item['trigger'] = [
                        'field' => str_replace('!', "", $trigger),
                        'action' => 'hide',
                    ];
                } else {
                    $item['trigger'] = [
                        'field' => $trigger,
                        'action' => 'show',
                    ];
                }
            }
            $options = $item['field_options'] ?? null;
            if ($options) {
                $array = explode(',', $options);
                $item['field_options'] = $array;
            }

            $model_opt = $item['model_opt'] ?? null;
            if ($model_opt) {
                $arrayOpt = explode(',', $model_opt);
                $item['append'] = in_array('append', $arrayOpt);
                $item['json'] = in_array('json', $arrayOpt);
                $item['getter'] = in_array('getter', $arrayOpt);
                $item['purgeable'] = in_array('purgeable', $arrayOpt);
            }
            $options = $item['c_field_opt'] ?? null;
            if ($options) {
                $array = explode(',', $options);
                $item['c_field_opt'] = $array;
            }

            $item = $this->getRelations($item);

            $field_type = $item['field_type'] ?? null;

            return $item;
        });

        $this->config['belong'] = $rows->where('belong', '!=', null)->pluck('belong')->toArray();
        $this->config['oneThrough'] = $rows->where('oneThrough', '!=', null)->pluck('oneThrough')->toArray();
        $this->config['belongsMany'] = $rows->where('belongsMany', '!=', null)->pluck('belongsMany')->toArray();
        $this->config['many'] = $rows->where('many', '!=', null)->pluck('many')->toArray();
        $this->config['manyThrough'] = $rows->where('manyThrough', '!=', null)->pluck('manythrough')->toArray();
        $this->config['morphMany'] = $rows->where('morphMany', '!=', null)->pluck('morphmany')->toArray();
        $this->config['morphOne'] = $rows->where('morphOne', '!=', null)->pluck('morphone')->unique('relation_name')->toArray();
        $this->config['attachOne'] = $rows->where('attachOne', '!=', null)->pluck('attachOne')->toArray();
        $this->config['attachMany'] = $rows->where('attachMany', '!=', null)->pluck('attachMany')->toArray();
        $this->config['lists'] = $rows->where('lists', '!=', null)->unique('lists')->pluck('lists')->toArray();

        //trace_log($rows->toArray());
        //trace_log($this->config);

        $trads = $rows->where('name', '<>', null)->toArray();

        $dbs = $rows->where('type', '<>', null)->where('version', '==', null)->toArray();
        $dbVersion = $rows->where('type', '<>', null)->where('version', '==', $this->version)->toArray();
        //trace_log($dbs);

        $columns = $rows->where('column', '<>', null)->sortBy('column')->toArray();
        $fields = $rows->where('field', '<>', null)->sortBy('field')->toArray();
        $this->fields_create = $rows->where('c_field', '<>', null);
        if ($this->fields_create) {
            $this->fields_create = $this->fields_create->sortBy('c_field');
            $this->fields_create = $this->fields_create->map(function ($item, $key) {
                $item['field_options'] = $item['c_field_opt'];
                return $item;
            });
            $this->fields_create = $this->fields_create->toArray();
        }
        //trace_log($this->fields_create);
        //trace_log($fields);
        $attributes = $rows->where('attribute', '<>', null)->toArray();

        //Recherche des tables dans la config
        $tabs = [];
        foreach ($this->config as $key => $value) {
            if (starts_with($key, 'tab::')) {
                $key = str_replace('tab::', "", $key);
                $tabs[$key] = $value;
            }
        }

        //Construction d'un array errors à partir de config, il sera utiliser dans le fichier de lang du midele
        $errors = [];
        foreach ($this->config as $key => $value) {
            if (starts_with($key, 'e.')) {
                $key = str_replace('e.', "", $key);
                $errors[$key] = $value;
            }
        }

        $excels = $rows->where('excel', '<>', null)->toArray();

        $titles = $rows->where('title', '<>', null)->pluck('name', 'var')->toArray();
        $appends = $rows->where('append', '<>', null)->pluck('name', 'var')->toArray();
        $dates1 = $rows->where('type', '==', 'date');
        $dates2 = $rows->where('type', '==', 'timestamp');
        $dates = $dates1->merge($dates2)->pluck('name', 'var')->toArray();
        $requireds = $rows->where('required', '<>', null)->pluck('required', 'var')->toArray();
        $jsons = $rows->where('json', '<>', null)->pluck('json', 'var')->toArray();
        $getters = $rows->where('getter', '<>', null)->pluck('json', 'var')->toArray();
        $purgeables = $rows->where('purgeable', '<>', null)->pluck('purgeable', 'var')->toArray();

        //trace_log($errors);

        $all = [
            'name' => $this->w_model,
            'ctname' => $this->w_model . 's',
            'author' => $this->w_author,
            'plugin' => $this->w_plugin,
            'configs' => $this->config,
            'trads' => $trads,
            'dbs' => $dbs,
            'dbVersion' => $dbVersion,
            'version' => $this->version,
            'columns' => $columns,
            'fields' => $fields,
            'fields_create' => $this->fields_create,
            'attributes' => $attributes,
            'titles' => $titles,
            'appends' => $appends,
            'dates' => $dates,
            'requireds' => $requireds,
            'jsons' => $jsons,
            'getters' => $getters,
            'purgeables' => $purgeables,
            'excels' => $excels,
            'tabs' => $tabs,
            'errors' => $errors,

        ];
        //trace_log($this->config);
        //trace_log($all);

        return $all;
    }

    public function getRelations($item)
    {
        $relation = $item['relation'] ?? null;
        if (!$relation) {
            return $item;
        }
        $array = explode('::', $relation);
        $type = $array[0];
        //trace_log($type);
        $relationClass = $this->getRelationClass($array[1], $item['var']);
        //trace_log('getRelationClass : '.$item['var']. ' :  '.$relationClass);
        $createYamlRelation = $this->createYamlRelation($array[1], $item['var']);
        $relationPath = $this->getRelationPath($array[1], $item['var'], $createYamlRelation);
        //trace_log('getRelationPath : '.$item['var']. ' :  '.$relationPath);
        $relationDetail = $this->getRelationDetail($array[1], $item['var']);
        $options = $this->getRelationOptions($array[2] ?? null);
        
        $userRelation = $relationClass == 'Backend\Models\User' ? true : false;
        if ($type == 'belong') {
            $fieldType = $item['field_type'] ?? null;
            $createRelationInController = $fieldType == 'partial_relation';
            $item['belong'] = [
                'relation_name' => $item['var'],
                'singular_name' => str_singular(camel_case($item['var'])),
                'relation_class' => $relationClass,
                'relation_path' => $relationPath,
                'options' => $options,
                'userRelation' => $userRelation,
                'createYamlRelation' => $createYamlRelation,
                'detail' => $relationDetail,
                'createInController' =>  $createRelationInController,

            ];
        }
        if ($type == 'oneThrough') {
            $fieldType = $item['field_type'] ?? null;
            $createRelationInController = $fieldType == 'partial_relation';
            $item['oneThrough'] = [
                'relation_name' => $item['var'],
                'singular_name' => str_singular(camel_case($item['var'])),
                'relation_class' => $relationClass,
                'relation_path' => $relationPath,
                'options' => $options,
                'userRelation' => $userRelation,
                'createYamlRelation' => $createYamlRelation,
                'detail' => $relationDetail,
                'createInController' =>  $createRelationInController,

            ];
        }
        if ($type == 'belongsMany') {
            $fieldType = $item['field_type'] ?? null;
            $createRelationInController = $fieldType != 'taglist' && $fieldType != 'dropdown';
            $item['belongsMany'] = [
                'relation_name' => $item['var'],
                'singular_name' => str_singular(camel_case($item['var'])),
                'relation_class' => $relationClass,
                'relation_path' => $relationPath,
                'options' => $options,
                'userRelation' => $userRelation,
                'createYamlRelation' => $createYamlRelation,
                'detail' => $relationDetail,
                'createInController' =>  $createRelationInController,
            ];
        }
        if ($type == 'morphMany') {
            $fieldType = $item['field_type'] ?? null;
           $createRelationInController = $fieldType != 'taglist' && $fieldType != 'dropdown';
            $item['morphMany'] = [
                'relation_name' => $item['var'],
                'singular_name' => str_singular(camel_case($item['var'])),
                'relation_path' => $relationPath,
                'relation_class' => $relationClass,
                'options' => $options,
                'createYamlRelation' => $createYamlRelation,
                'detail' => $relationDetail,
                'createInController' =>  $createRelationInController,
            ];
        }
        if ($type == 'morphOne') {
            $fieldType = $item['field_type'] ?? null;
            $createRelationInController = $fieldType == 'partial_relation';
            $item['morphOne'] = [
                'relation_name' => $this->getRelationKeyVar($array[1], $item['var']),
                'relation_class' => $relationClass,
                'singular_name' => str_singular(camel_case($item['var'])),
                'relation_path' => $relationPath,
                'options' => $options,
                'userRelation' => $userRelation,
                'createYamlRelation' => $createYamlRelation,
                'detail' => $relationDetail,
                'createInController' =>  $createRelationInController,
            ];
        }
        if ($type == 'many') {
            $fieldType = $item['field_type'] ?? null;
            $createRelationInController = $fieldType != 'taglist' && $fieldType != 'dropdown';
            $item['many'] = [
                'relation_name' => $item['var'],
                'singular_name' => str_singular(camel_case($item['var'])),
                'relation_path' => $relationPath,
                'relation_class' => $relationClass,
                'options' => $options,
                'createYamlRelation' => $createYamlRelation,
                'detail' => $relationDetail,
                'createInController' =>  $createRelationInController,
            ];
        }
        if ($type == 'manyThrough') {
            $fieldType = $item['field_type'] ?? null;
            $createRelationInController = $fieldType != 'taglist' && $fieldType != 'dropdown';
            $item['manyThrough'] = [
                'relation_name' => $item['var'],
                'singular_name' => str_singular(camel_case($item['var'])),
                'relation_path' => $relationPath,
                'relation_class' => $relationClass,
                'options' => $options,
                'createYamlRelation' => $createYamlRelation,
                'detail' => $relationDetail,
                'createInController' =>  $createRelationInController,
            ];
        }
        if ($type == 'attachMany') {
            $item['attachMany'] = [
                'relation_name' => $item['var'],
                'relation_class' => $relationClass,
            ];
        }
        if ($type == 'attachOne') {
            $item['attachOne'] = [
                'relation_name' => $item['var'],
                'relation_class' => $relationClass,
            ];
        }
        return $item;
    }

    public function getRelationKeyVar($value, $key)
    {
        $parts = explode('.', $value);
        $r_author = $parts[0];
        $r_plugin = $parts[1];
        $r_model = $parts[2] ?? camel_case(str_singular($key));
        return $r_model;
    }

    public function createYamlRelation($value, $key)
    {
        $returnVar = true;
        $noYaml = $this->config['no_yaml_for'] ?? false;
        $yamlInModel = $this->config['yaml_in_model'] ?? false;
        $noYaml = explode(",", $noYaml);
        $yamlInModel = explode(",", $yamlInModel);
        //trace_log($key);
        if (in_array($key, $noYaml)) {
            $returnVar = false;
        }
        if (in_array($key, $yamlInModel)) {
            $returnVar = 'inModel';
        }
        //trace_log('returnVar : '.$returnVar);
        return $returnVar;
    }

    public function getRelationClass($value, $key)
    {
        if ($value == 'self') {
            return ucfirst($this->w_author) . '\\' . ucfirst($this->w_plugin) . '\\Models\\' . ucfirst(camel_case(str_singular($key)));
        } elseif ($value == 'user') {
            return 'Backend\Models\User';
        } elseif ($value == 'cloudi') {
            return 'Waka\Cloudis\Models\CloudiFile';
        } elseif ($value == 'file') {
            return 'System\Models\File';
        } else {
            $parts = explode('.', $value);
            $r_author = $parts[0];
            $r_plugin = $parts[1];
            $r_model = $parts[2] ?? camel_case(str_singular($key));
            return ucfirst($r_author) . '\\' . ucfirst($r_plugin) . '\\Models\\' . ucfirst($r_model);
        }
    }

    public function getRelationDetail($value, $key)
    {
        if ($value == 'self') {
            return [
                'author' => strtolower($this->w_author),
                'plugin' => strtolower($this->w_plugin),
                'model' => strtolower(camel_case($key)),
            ];
        } elseif ($value == 'user') {
            return [
                'author' => null,
                'Backend' => 'Backend',
                'model' => 'user',
            ];
        } elseif ($value == 'cloudi') {
            return [
                'author' => 'waka',
                'Backend' => 'cloudi',
                'model' => 'cloudifile',
            ];
        } elseif ($value == 'file') {
            return [
                'author' => null,
                'Backend' => 'system',
                'model' => 'file',
            ];
        } else {
            $parts = explode('.', $value);
            $r_model = $parts[2] ?? $key;
            return [
                'author' => strtolower($parts[0]),
                'plugin' => strtolower($parts[1]),
                'model' => strtolower($r_model),
            ];
        }
    }

    public function getRelationPath($value, $key, $createYamlRelation)
    {
        //trace_log('getRelationPath : '.$value.' key '.$key.' createYamlRelation : '.$createYamlRelation);
        if ($value == 'self') {
            //trace_log('self');
            return '$/' . strtolower($this->w_author) . '/' . strtolower($this->w_plugin) . '/models/' . strtolower(camel_case(str_singular($key)));
        } else if ($value == 'user') {
             //trace_log('user');
            return '$/' . strtolower($this->w_author) . '/' . strtolower($this->w_plugin) . '/models/' . strtolower(str_singular($key));
        } 
        // else if ($createYamlRelation == 'inModel') {
        //     //trace_log('createYamlRelation = inModel');
        //     return '$/' . strtolower($this->w_author) . '/' . strtolower($this->w_plugin) . '/models/' . strtolower(str_singular($key));
        // } 
        else {
            //trace_log('plugin externe-------------------');
            $parts = explode('.', $value);
            $r_plugin = array_pop($parts);
            $r_author = array_pop($parts);
            return '$/' . strtolower($r_author) . '/' . strtolower($r_plugin) . '/models/' . strtolower(camel_case(str_singular($key)));
        }
    }

    public function getRelationOptions($value)
    {
        if (!$value) {
            return null;
        }
        $parts = explode(',', $value);

        $options = [];

        //travail sur les deifferents coules key attribute
        foreach ($parts as $part) {
            $key_att = explode('.', $part);
            //trace_log($key_att);
            $options[$key_att[0]] = $key_att[1];
        }
        return $options;
    }

    protected function processVars($vars)
    {

        $cases = ['upper', 'lower', 'snake', 'studly', 'camel', 'title'];
        $modifiers = ['plural', 'singular', 'title'];

        foreach ($vars as $key => $var) {
            if (!is_array($var) && $var) {
                /*
                 * Apply cases, and cases with modifiers
                 */
                foreach ($cases as $case) {
                    $primaryKey = $case . '_' . $key;
                    $vars[$primaryKey] = $this->modifyString($case, $var);

                    foreach ($modifiers as $modifier) {
                        $secondaryKey = $case . '_' . $modifier . '_' . $key;
                        $vars[$secondaryKey] = $this->modifyString([$modifier, $case], $var);
                    }
                }

                /*
                 * Apply modifiers
                 */
                foreach ($modifiers as $modifier) {
                    $primaryKey = $modifier . '_' . $key;
                    $vars[$primaryKey] = $this->modifyString($modifier, $var);
                }
            } else {
                $vars[$key] = $var;
            }
        }

        return $vars;
    }

    /**
     * Make a single stub.
     *
     * @param string $stubName The source filename for the stub.
     */
    public function makeOneStub($stubName, $destinationName, $tempVar)
    {

        $sourceFile = $this->getSourcePath() . '/' . $stubName;
        $destinationFile = $this->getDestinationPath() . '/' . $destinationName;
        $destinationContent = $this->files->get($sourceFile);

        /*
         * Parse each variable in to the destination content and path
         */
        $destinationContent = Twig::parse($destinationContent, $tempVar);
        $destinationFile = Twig::parse($destinationFile, $tempVar);

        $this->makeDirectory($destinationFile);

        /*
         * Make sure this file does not already exist
         */
        if ($this->files->exists($destinationFile) && !$this->option('force')) {
            throw new \Exception('Stop everything!!! This file already exists: ' . $destinationFile);
        }

        $this->files->put($destinationFile, $destinationContent);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['plugin', InputArgument::REQUIRED, 'The name of the plugin. Eg: RainLab.Blog'],
            ['model', InputArgument::REQUIRED, 'The name of the model. Eg: Post'],
            ['src', InputArgument::REQUIRED, 'The name of the model. Eg: Post'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Overwrite existing files with generated ones.'],
            ['v', null, InputOption::VALUE_REQUIRED, 'Crée un update de version'],
            ['option', null, InputOption::VALUE_NONE, 'Crée uniquement le model'],
            ['file', null, InputOption::VALUE_REQUIRED, 'Fichier'],
        ];
    }
}
