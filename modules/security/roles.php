<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 10/9/2016
 * Time: 11:04 PM
 */

$view_name = "roles";
require_once $_SERVER['DOCUMENT_ROOT'] . '/sfrs/include/header.php';

$transport = new \Kendo\Data\DataSourceTransport();

$create = new \Kendo\Data\DataSourceTransportCreate();
$create->url('roles-service.php?type=create')
    ->contentType('application/json')
    ->type('POST');

$read = new \Kendo\Data\DataSourceTransportRead();
$read->url('roles-service.php?type=read')
    ->contentType('application/json')
    ->type('POST');

$update = new \Kendo\Data\DataSourceTransportUpdate();
$update->url('roles-service.php?type=update')
    ->contentType('application/json')
    ->type('POST');

$destroy = new \Kendo\Data\DataSourceTransportDestroy();
$destroy->url('roles-service.php?type=destroy')
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

$roleIdField = new \Kendo\Data\DataSourceSchemaModelField('idrole');
$roleIdField->type('number')
    ->editable(false)
    ->nullable(true);

$roleNameField = new \Kendo\Data\DataSourceSchemaModelField('rolename');
$roleNameField->type('string')
    ->validation(array('required' => true));

$model->id('idrole')
    ->addField($roleIdField, $roleNameField);

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
$nameColumn->field('rolename')
    ->width('40%')
    ->title('Nombre del rol');

$assignModulesCommandItem = new \Kendo\UI\GridColumnCommandItem();
$assignModulesCommandItem->click('assignModulesCommandItemClick')
    ->text('Módulos');

$command = new \Kendo\UI\GridColumn();
$command->addCommandItem('edit')
    ->addCommandItem('destroy')
    ->addCommandItem($assignModulesCommandItem)
    ->title('&nbsp;')
    ->width(250);

$grid->addColumn($nameColumn, $command)
    ->dataSource($dataSource)
    ->addToolbarItem(new \Kendo\UI\GridToolbarItem('create'))
    ->height(400)
    ->pageable(true)
    ->sortable(true)
    //->dataBound('dataBound')
    //->detailTemplateId('details')
    ->filterable(true)
    ->editable('popup');

?>

<script type="text/javascript" src="/sfrs/js/modules/security/roles.js"></script>

<div id="page-wrapper">
    <div class="row">
        <div class="col-md-12">
            <h1 class="page-header">Administración de roles del sistema</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php echo $grid->render(); ?>
        </div>
    </div>
</div>

<!--<script id="details" type="text/x-kendo-template">-->
    <?php
/*
    $transport = new \Kendo\Data\DataSourceTransport();

    $read = new \Kendo\Data\DataSourceTransportRead();

    $read->url('roles-service.php?details=details')
        ->contentType('application/json')
        ->type('POST');

    $transport->read($read)
        ->parameterMap('function(data) {
                //data.filter.filters[0].value = parseInt(data.filter.filters[0].value);
                return kendo.stringify(data);
              }');

    $model = new \Kendo\Data\DataSourceSchemaModel();

    $model->id('idrole')
        ->addField($roleIdField);

    $schema = new \Kendo\Data\DataSourceSchema();
    $schema->model($model)
        ->data('data')
        ->total('total');*/

    //$dataSource = new \Kendo\Data\DataSource();

/*    $filter = new \Kendo\Data\DataSourceFilterItem();
    $filter->field('idrole')
        ->operator('eq')
        ->value('#=idrole#');*/

/*    $dataSource->transport($transport)
        ->pageSize(10)
        ->schema($schema)
        //->addFilterItem($filter)
        ->serverSorting(true)
        ->serverFiltering(true)
        ->serverPaging(true);*/

/*    $detailGrid = new \Kendo\UI\Grid('detailGrid#=idrole#');

    $moduleId = new \Kendo\UI\GridColumn();
    $moduleId->field('idmodule')
        ->width(110)
        ->title('ID módulo');*/

    /*$moduleName = new \Kendo\UI\GridColumn();
    $moduleName->field('name')
        ->width(110)
        ->title('Nombre del módulo');*/

/*    $moduleQuery = new \Kendo\UI\GridColumn();
    $moduleQuery->field('query')
        ->title('Lectura');

    $moduleCreate = new \Kendo\UI\GridColumn();
    $moduleCreate->field('create')
        ->title('Crear');

    $moduleUpdate = new \Kendo\UI\GridColumn();
    $moduleUpdate->field('update')
        ->title('Editar');

    $moduleDelete = new \Kendo\UI\GridColumn();
    $moduleDelete->field('delete')
        ->title('Eliminar');*/

/*    $detailGrid->dataSource($dataSource)
        ->addColumn($moduleId, $moduleQuery, $moduleCreate, $moduleUpdate, $moduleDelete)
        ->pageable(true)
        ->sortable(true)
        ->scrollable(false);

    echo $detailGrid->renderInTemplate();*/
/*    */?><!--
</script>-->

<!--<script>
    function dataBound() {
        this.expandRow(this.tbody.find("tr.k-master-row").first());
    }
</script>-->



<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/sfrs/include/footer.php';
?>
