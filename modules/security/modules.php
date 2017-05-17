<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 10/9/2016
 * Time: 11:04 PM
 */

$view_name = "modules";
require_once $_SERVER['DOCUMENT_ROOT'] . '/sfrs/include/header.php';

$transport = new \Kendo\Data\DataSourceTransport();

$create = new \Kendo\Data\DataSourceTransportCreate();
$create->url('modules-service.php?type=create')
    ->contentType('application/json')
    ->type('POST');

$read = new \Kendo\Data\DataSourceTransportRead();
$read->url('modules-service.php?type=read')
    ->contentType('application/json')
    ->type('POST');

$update = new \Kendo\Data\DataSourceTransportUpdate();
$update->url('modules-service.php?type=update')
    ->contentType('application/json')
    ->type('POST');

$destroy = new \Kendo\Data\DataSourceTransportDestroy();
$destroy->url('modules-service.php?type=destroy')
    ->contentType('application/json')
    ->type('POST');

// Configure the transport. Send all data source parameters as JSON using the parameterMap setting.
$transport->create($create)
    ->read($read)
    ->update($update)
    ->destroy($destroy)
    ->parameterMap('function(data) {
                  return kendo.stringify(data);
              }');

// Create the schema model
$model = new \Kendo\Data\DataSourceSchemaModel();

$moduleIdField = new \Kendo\Data\DataSourceSchemaModelField('idmodule');
$moduleIdField->type('number')
    ->editable(false)
    ->nullable(true);


$nameField = new \Kendo\Data\DataSourceSchemaModelField('name');
$nameField->type('string')
    ->validation(array('required' => true));

$view_nameField = new \Kendo\Data\DataSourceSchemaModelField('view_name');
$view_nameField->type('string')
    ->validation(array('required' => true));

$is_security_moduleField = new \Kendo\Data\DataSourceSchemaModelField('is_security_module');
$is_security_moduleField->type('boolean');
    //->validation(array('required' => true));

$urlField = new \Kendo\Data\DataSourceSchemaModelField('url');
$urlField->type('string')
    ->validation(array('required' => true));

$model->id('idmodule')
    ->addField($moduleIdField, $nameField, $view_nameField, $is_security_moduleField, $urlField);

// Create the schema
$schema = new \Kendo\Data\DataSourceSchema();

// Set its model
$schema->model($model)
    ->data('data')
    ->errors('errors')
    ->total('total');

// Create the data source
$dataSource = new \Kendo\Data\DataSource();

// Specify the schema and data
$dataSource->transport($transport)
    ->batch(true)
    ->serverPaging(false)
    ->pageSize(5)
    ->serverSorting(false)
    ->schema($schema);

$grid = new \Kendo\UI\Grid('grid');

$nameColumn = new \Kendo\UI\GridColumn();
$nameColumn->field('name')
    ->width('15%')
    ->title('Nombre del módulo');

$view_nameColumn = new \Kendo\UI\GridColumn();
$view_nameColumn->field('view_name')
    ->width('15%')
    ->title('Código de la vista');

$is_security_moduleColumn = new \Kendo\UI\GridColumn();
$is_security_moduleColumn->field('is_security_module')
    ->template('#: is_security_module == 1 ? "Si" : "No" #')
    ->width('15%')
    ->title('Módulo de seguridad');

$urlColumn = new \Kendo\UI\GridColumn();
$urlColumn->field('url')
    ->width('15%')
    ->title('URL');

$command = new \Kendo\UI\GridColumn();
$command->addCommandItem('edit')
    ->addCommandItem('destroy')
    ->title('&nbsp;')
    ->width('20%');

$grid->addColumn($nameColumn, $view_nameColumn, $is_security_moduleColumn, $urlColumn, $command)
    ->dataSource($dataSource)
    ->addToolbarItem(new \Kendo\UI\GridToolbarItem('create'))
    ->height(400)
    ->sortable(true)
    ->pageable(true)
    ->editable('popup');

?>

<div id="page-wrapper">
    <div class="row">
        <div class="col-md-12">
            <h1 class="page-header">Administración de módulos del sistema</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php echo $grid->render(); ?>
        </div>
    </div>
</div>

<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/sfrs/include/footer.php';
?>
