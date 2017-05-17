<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 10/9/2016
 * Time: 11:04 PM
 */
$view_name = "soccer_fields";
require_once $_SERVER['DOCUMENT_ROOT'] . '/sfrs/include/header.php';

$transport = new \Kendo\Data\DataSourceTransport();

$create = new \Kendo\Data\DataSourceTransportCreate();
$create->url('soccer-fields-service.php?type=create')
    ->contentType('application/json')
    ->type('POST');

$read = new \Kendo\Data\DataSourceTransportRead();
$read->url('soccer-fields-service.php?type=read')
    ->contentType('application/json')
    ->type('POST');

$update = new \Kendo\Data\DataSourceTransportUpdate();
$update->url('soccer-fields-service.php?type=update')
    ->contentType('application/json')
    ->type('POST');

$destroy = new \Kendo\Data\DataSourceTransportDestroy();
$destroy->url('soccer-fields-service.php?type=destroy')
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

$soccerFieldIdField = new \Kendo\Data\DataSourceSchemaModelField('idsoccer_field');
$soccerFieldIdField->type('number')
    ->editable(false)
    ->nullable(true);

$soccerFieldNameField = new \Kendo\Data\DataSourceSchemaModelField('name');
$soccerFieldNameField->type('string')
    ->validation(array('required' => true));

$model->id('idsoccer_field')
    ->addField($soccerFieldIdField, $soccerFieldNameField);

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
    ->serverFiltering(true)
    ->serverSorting(false)
    ->schema($schema);

$grid = new \Kendo\UI\Grid('grid');

$nameColumn = new \Kendo\UI\GridColumn();
$nameColumn->field('name')
    ->title('Nombre de la cancha');

$command = new \Kendo\UI\GridColumn();
$command->addCommandItem('edit')
    ->addCommandItem('destroy')
    ->title('&nbsp;')
    ->width(250);

$grid->addColumn($nameColumn, $command)
    ->dataSource($dataSource)
    ->addToolbarItem(new \Kendo\UI\GridToolbarItem('create'))
    ->height(400)
    ->pageable(true)
    ->filterable(true)
    ->sortable(true)
    ->editable('popup');

?>

<div id="page-wrapper">
    <div class="row">
        <div class="col-md-12">
            <h1 class="page-header">Catálogo de canchas</h1>
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