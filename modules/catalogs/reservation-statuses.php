<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 10/9/2016
 * Time: 11:05 PM
 */
?>
<script type="text/javascript">

    function isFinalStateEditor(container, options) {
        var input =
            '<select name="is_final_state" data-bind="value:' + options.field + '" required="required" value="' + (options.model.is_final_state == "1" ? "Si" : "No") + '"> ' +
            '<option value="1">Si</option> ' +
            '<option value="0">No</option> ' +
            '</select>';

        console.log(input);
        $(input).appendTo(container);

        $('<span class="k-invalid-msg" data-for="is_final_state"></span>').appendTo(container);
    };
</script>


<!--<script type="text/javascript">
    function isFinalStateEditor (container, options) {
        console.log(options);
        $('<input name="is_final_state" data-text-field="name" data-value-field="id" data-bind="value:' + options.field + '" required="required" />')
            .appendTo(container)
            .kendoComboBox({
                autoBind: false,
                dataSource: {
                    data: [
                        {name: "No", id: "0"},
                        {name: "Si", id: "1"}
                    ]
                }
            });

        $('<span class="k-invalid-msg" data-for="is_final_state"></span>').appendTo(container);
    };
</script>-->


<?php
$view_name = "reservation_statuses";
require_once $_SERVER['DOCUMENT_ROOT'] . '/sfrs/include/header.php';

$transport = new \Kendo\Data\DataSourceTransport();

$create = new \Kendo\Data\DataSourceTransportCreate();
$create->url('reservation-statuses-service.php?type=create')
    ->contentType('application/json')
    ->type('POST');

$read = new \Kendo\Data\DataSourceTransportRead();
$read->url('reservation-statuses-service.php?type=read')
    ->contentType('application/json')
    ->type('POST');

$update = new \Kendo\Data\DataSourceTransportUpdate();
$update->url('reservation-statuses-service.php?type=update')
    ->contentType('application/json')
    ->type('POST');

$destroy = new \Kendo\Data\DataSourceTransportDestroy();
$destroy->url('reservation-statuses-service.php?type=destroy')
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

$reservationIdField = new \Kendo\Data\DataSourceSchemaModelField('idreservation_state');
$reservationIdField->type('number')
    ->editable(false)
    ->nullable(true);

$reservationDescriptionField = new \Kendo\Data\DataSourceSchemaModelField('description');
$reservationDescriptionField->type('string')
    ->validation(array('required' => true));

$isFinalStateField = new \Kendo\Data\DataSourceSchemaModelField('is_final_state');
$isFinalStateField->type('bit');


$model->id('idreservation_state')
    ->addField($reservationIdField, $reservationDescriptionField, $isFinalStateField);

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
    ->pageSize(20)
    ->serverFiltering(true)
    ->serverPaging(true)
    ->schema($schema);

$grid = new \Kendo\UI\Grid('grid');

$nameColumn = new \Kendo\UI\GridColumn();
$nameColumn->field('description')
    ->title('Nombre del estado');

$isFinalStateColumn = new \Kendo\UI\GridColumn();
$isFinalStateColumn->field('is_final_state')
    //->template('#: formatBoolean(is_final_state) #')
    ->template('#: is_final_state == 1 ? "Si" : "No" #')
    //->editor(new \Kendo\JavaScriptFunction('isFinalStateEditor'))
    ->title('Estado final');

$command = new \Kendo\UI\GridColumn();
$command->addCommandItem('edit')
    ->addCommandItem('destroy')
    ->title('&nbsp;')
    ->width(250);

$grid->addColumn($nameColumn, $isFinalStateColumn, $command)
    ->dataSource($dataSource)
    ->addToolbarItem(new \Kendo\UI\GridToolbarItem('create'))
    ->height(400)
    ->pageable(true)
    ->filterable(true)
    ->editable('popup');

?>

<div id="page-wrapper">
    <div class="row">
        <div class="col-md-12">
            <h1 class="page-header">Catálogo de estados de una reservación</h1>
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

