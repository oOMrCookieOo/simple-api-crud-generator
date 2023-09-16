<?php

namespace MrCookie\SimpleApiCrudGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Doctrine\DBAL\Schema\Table;
use MrCookie\SimpleApiCrudGenerator\Traits\CanManipulateFiles;
use ReflectionClass;
use Throwable;
use function Laravel\Prompts\text;

class SimpleApiCrudGeneratorCommand extends Command
{
    use CanManipulateFiles;

    public $signature = 'api-crud:generate {model?}';

    public $description = 'My command';


    public function handle(): int
    {
        $modelInput = (string)str($this->argument('model') ??
            text(
                label: 'What is the model name?',
                placeholder: 'Product',
                required: true
            ))->replace('/', '\\');

        $model = Str::of($modelInput)->afterLast('\\')->studly()->value();


        if (empty($model) || !class_exists("App\\Models\\$model")) {
            $this->error("Model $model not found.");
            return self::FAILURE;
        }
        $reflection = new ReflectionClass("App\\Models\\$model");

        $pluralModel = Str::pluralStudly($model);

        $actionsPath = app_path("Api/Actions/$pluralModel/");

        $apiResourcesPath = app_path("Api/Resources/$pluralModel/");


        $this->copyStubToApp('GetActionClass', "{$actionsPath}Get{$pluralModel}Action.php", [
            'model' => "$model::class",
            'pluralModel' => $pluralModel,
            'actionNameSpace' => "App\\Api\\Actions\\$pluralModel",
            'modelImportPath' => $reflection->getName(),
            'resourceClass' => "{$pluralModel}Resource",
            'resourceImportPath' => "App\\Api\\Resources\\$pluralModel\\{$pluralModel}Resource",
        ]);

        $this->copyStubToApp('UpdateActionClass', "{$actionsPath}Update{$model}Action.php", [
            'model' => $model,
            'actionNameSpace' => "App\\Api\\Actions\\$pluralModel",
            'modelImportPath' => $reflection->getName(),
        ]);

        $this->copyStubToApp('StoreActionClass', "{$actionsPath}Store{$model}Action.php", [
            'model' => $model,
            'actionNameSpace' => "App\\Api\\Actions\\$pluralModel",
            'modelImportPath' => $reflection->getName(),

        ]);

        $this->copyStubToApp('ShowActionClass', "{$actionsPath}Show{$model}Action.php", [
            'model' => "$model::class",
            'pluralModel' => $pluralModel,
            'singleModel' => $model,
            'actionNameSpace' => "App\\Api\\Actions\\$pluralModel",
            'modelImportPath' => $reflection->getName(),
            'resourceClass' => "{$pluralModel}Resource",
            'resourceImportPath' => "App\\Api\\Resources\\$pluralModel\\{$pluralModel}Resource",
        ]);

        $this->copyStubToApp('DeleteActionClass', "{$actionsPath}Delete{$model}Action.php", [
            'model' => $model,
            'actionNameSpace' => "App\\Api\\Actions\\$pluralModel",
            'modelImportPath' => $reflection->getName(),
        ]);


        $resourceArray = $this->generateResourceArray($reflection);


        $this->copyStubToApp('ResourceClass', "{$apiResourcesPath}{$pluralModel}Resource.php", [
            'namespace' => "App\\Api\\Resources\\$pluralModel",
            'resourceClass' => "{$pluralModel}Resource",
            'resourceArray' => $resourceArray
        ]);

        $groupName = strtolower($pluralModel);


        if ($this->routesExist($groupName)) {
            $this->info("Routes for $model already exist. Skipping route generation.");
            return self::SUCCESS;
        }

        file_put_contents(base_path('routes/api.php'), "
        Route::name('$groupName.')->prefix('$groupName')->group(function () {
            Route::get('', App\\Api\\Actions\\$pluralModel\\Get{$pluralModel}Action::class);
            Route::get('{id}', App\\Api\\Actions\\$pluralModel\\Show{$model}Action::class);
            Route::put('{id}', App\\Api\\Actions\\$pluralModel\\Update{$model}Action::class);
            Route::delete('{id}', App\\Api\\Actions\\$pluralModel\\Delete{$model}Action::class);
        });
    ", FILE_APPEND);

        $this->info("Successfully created API routes for $model!");

        return self::SUCCESS;

    }

    private function generateResourceArray($reflection): string
    {
        $table = $this->getModelTable($reflection->getName());

        $resource_array = [];

        foreach ($table->getColumns() as $column) {
            $columnName = $column->getName();

            if (str($columnName)->is([
                'created_at',
                'deleted_at',
                'updated_at',
                'password',
                'email_verified_at',
                '*_token',
            ])) {
                continue;
            }

            $resource_array[] = "'$columnName' => \$this->$columnName";
        }

        return '[' . implode(', ', $resource_array) . ']';
    }

    protected function getModelTable(string $model): ?Table
    {
        $modelClass = $model;
        $model = app($model);

        try {
            return $model
                ->getConnection()
                ->getDoctrineSchemaManager()
                ->listTableDetails($model->getTable());
        } catch (Throwable $exception) {
            $this->components->warn("Unable to read table schema for model [{$modelClass}]: {$exception->getMessage()}");

            return null;
        }
    }

    private function routesExist(string $groupName): bool
    {
        foreach (app('router')->getRoutes()->getRoutesByName() as $name => $route) {
            if (strpos($name, "$groupName.") === 0) {
                return true;
            }
        }

        return false;
    }
}
