<?php
/**
 * Created by PhpStorm.
 * User: Víctor
 * Date: 25/10/2016
 * Time: 09:26 PM
 */

$view_name = "reservations";

require_once $_SERVER['DOCUMENT_ROOT'] . '/sfrs/include/header.php';

$transport = new \Kendo\Data\DataSourceTransport();

$create = new \Kendo\Data\DataSourceTransportCreate();
$create->url('reservations-service.php?type=create')
    ->contentType('application/json')
    ->type('POST');

$read = new \Kendo\Data\DataSourceTransportRead();
$read->url('reservations-service.php?type=read')
    ->contentType('application/json')
    ->type('POST');

$update = new \Kendo\Data\DataSourceTransportUpdate();
$update->url('reservations-service.php?type=update')
    ->contentType('application/json')
    ->type('POST');

$destroy = new \Kendo\Data\DataSourceTransportDestroy();
$destroy->url('reservations-service.php?type=destroy')
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

$reservationIdField = new \Kendo\Data\DataSourceSchemaModelField('idreservation');
$reservationIdField->type('number')
    ->editable(false)
    ->nullable(true);

$usernameField = new \Kendo\Data\DataSourceSchemaModelField('iduser');
$usernameField->type('number')
    ->nullable(true)
    ->validation(array('required' => true))
    ->editable(true);

$reservationDateField = new \Kendo\Data\DataSourceSchemaModelField('reservation_date');
$reservationDateField->type('date')
    ->validation(array('required' => true, 'min' => Date((new Datetime('tomorrow'))->format('Y-m-d'))))
    ->editable(true);

$soccerFieldField = new \Kendo\Data\DataSourceSchemaModelField('idsoccer_field');
$soccerFieldField->type('number')
    ->nullable(true)
    ->validation(array('required' => true))
    ->editable(true);

$dayofweekValidation = new \Kendo\Data\DataSourceSchemaModelFieldValidation();
$dayofweekValidation
    ->required(true)
    ->min(0)
    ->max(6);

$dayofweekField = new \Kendo\Data\DataSourceSchemaModelField('dayofweek');
$dayofweekField->type('number')
    ->nullable(true)
    ->validation($dayofweekValidation);

$priceField = new \Kendo\Data\DataSourceSchemaModelField('price');
$priceField->type('number')
    ->validation(array('required' => false))
    ->defaultValue('Se calculará automáticamente')
    ->nullable(false)
    ->editable(false);

$timeValidation = new \Kendo\Data\DataSourceSchemaModelFieldValidation();
$timeValidation
    ->required(true)
    ->min(0)
    ->max(23);

$timeField = new \Kendo\Data\DataSourceSchemaModelField('time');
$timeField->type('number')
    ->nullable(true)
    ->validation($timeValidation);

$createDateField = new \Kendo\Data\DataSourceSchemaModelField('create_date');
$createDateField->type('date')
    ->validation(array('required' => false))
    ->editable(false);

$reservationStateField = new \Kendo\Data\DataSourceSchemaModelField('reservation_state');
$reservationStateField->type('string')
    ->defaultValue('Creación')
    ->nullable(false)
    ->editable(false);

$model->id('idreservation')
    ->addField($reservationIdField, $usernameField, $reservationDateField,
        $soccerFieldField, $dayofweekField, $timeField, $priceField,
        $createDateField, $reservationStateField);

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
    ->serverFiltering(false)
    ->serverPaging(false)
    ->pageSize(5)
    ->serverSorting(false)
    ->error('onDataSourceError')
    ->schema($schema);

$grid = new \Kendo\UI\Grid('grid');

$usernameColumn = new \Kendo\UI\GridColumn();
$usernameColumn->field('username')
    ->title('Usuario')
    ->template('#= (data.username != undefined) ? data.username.username : "" #')
    ->editor('usernameDropDownEditor');

$reservationDateColumn = new \Kendo\UI\GridColumn();
$reservationDateColumn->field('reservation_date')
    ->title('Fecha reserva')
    ->format('{0:dd/MM/yyyy}');

$soccerFieldColumn = new \Kendo\UI\GridColumn();
$soccerFieldColumn->field('soccer_field')
    ->title('Cancha')
    ->template('#= (data.soccer_field != undefined) ? data.soccer_field.name : "" #')
    ->editor('soccer_fieldDropDownEditor');

/*$dayofweekColumn = new \Kendo\UI\GridColumn();
$dayofweekColumn->field('dayofweek')
    ->title('Día')
    ->format('{0:n0}')
    ->template('#: getDay(dayofweek) #');*/

$timeColumn = new \Kendo\UI\GridColumn();
$timeColumn->field('time')
    ->title('Hora')
    ->format('{0:n0}')
    ->template('#: formatAMPM(time) #');

$priceColumn = new \Kendo\UI\GridColumn();
$priceColumn->field('price')
    ->title('Precio')
    ->format('{0:c}');

$createDateColumn = new \Kendo\UI\GridColumn();
$createDateColumn->field('create_date')
    ->title('Fecha creación')
    ->format('{0:dd/MM/yyyy}');

$reservationStateColumn = new \Kendo\UI\GridColumn();
$reservationStateColumn->field('reservation_state')
    ->title('Estado');

$cancelReservationCommandItem = new \Kendo\UI\GridColumnCommandItem();
$cancelReservationCommandItem->click('cancelReservationCommandItemClick')
    ->text('Cancelar');

$payReservationCommandItem = new \Kendo\UI\GridColumnCommandItem();
$payReservationCommandItem->click('payReservationCommandItemClick')
    ->text('Pagar');

$command = new \Kendo\UI\GridColumn();
$command->addCommandItem('edit')
    ->addCommandItem($payReservationCommandItem)
    ->addCommandItem($cancelReservationCommandItem)
    ->title('&nbsp;')
    ->width(250);

$grid->addColumn($usernameColumn, $reservationDateColumn, $soccerFieldColumn,
     $priceColumn, $timeColumn, $createDateColumn, $reservationStateColumn, $command)
    ->dataSource($dataSource)
    ->addToolbarItem(new \Kendo\UI\GridToolbarItem('create'))
    ->height(400)
    ->pageable(true)
    ->filterable(true)
    ->sortable(true)
    ->editable('popup');

?>

<script type="text/javascript" src="/sfrs/js/modules/catalogs/reservations.js"></script>

<div id="page-wrapper">
    <div class="row">
        <div class="col-md-12">
            <h1 class="page-header">Administración de reservas</h1>
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
