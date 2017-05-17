<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 10/9/2016
 * Time: 11:04 PM
 */

$view_name = "schedules";
require_once $_SERVER['DOCUMENT_ROOT'] . '/sfrs/include/header.php';
?>
<script>

    function onDataSourceError(e){
        alert(e.errorThrown);
    };

    function getDay(number){
        var dayName = "";
        switch (number)
        {
            case 0:
                dayName = "Lunes";
                break;
            case 1:
                dayName = "Martes";
                break;
            case 2:
                dayName = "Miércoles";
                break;
            case 3:
                dayName = "Jueves";
                break;
            case 4:
                dayName = "Viernes";
                break;
            case 5:
                dayName = "Sábado";
                break;
            case 6:
                dayName = "Domingo";
                break;
            default:
                dayName = "";
        }
        return dayName;
    }

    function formatAMPM(time) {
        var hours = time;
        var ampm = time >= 12 ? 'pm' : 'am';
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        var strTime = hours + ':00 ' + ampm;
        return strTime;
    }

    function dayOfWeekDropDownEditor(container, options) {

        var value = null;

        if (options.model.dayofweek){
            switch(options.model.dayofweek){
                case 0: value = { 'dayofweek' : 0 , 'name' : 'Lunes' }; break;
                case 1: value = { 'dayofweek' : 1 , 'name' : 'Martes' }; break;
                case 2: value = { 'dayofweek' : 2 , 'name' : 'Miercoles' }; break;
                case 3: value = { 'dayofweek' : 3 , 'name' : 'Jueves' }; break;
                case 4: value = { 'dayofweek' : 4 , 'name' : 'Viernes' }; break;
                case 5: value = { 'dayofweek' : 5 , 'name' : 'Sabado' }; break;
                case 6: value = { 'dayofweek' : 6 , 'name' : 'Domingo' }; break;
            }
        }
        $('<input data-text-field="name" data-value-field="dayofweek" data-bind="value:' + options.field + '"/>')
            .appendTo(container)
            .kendoDropDownList({
                autoBind: false,
                valuePrimitive: true,
                dataSource: {
                    data: [
                        { 'dayofweek' : 0 , 'name' : 'Lunes' },
                        { 'dayofweek' : 1 , 'name' : 'Martes' },
                        { 'dayofweek' : 2 , 'name' : 'Miercoles' },
                        { 'dayofweek' : 3 , 'name' : 'Jueves' },
                        { 'dayofweek' : 4 , 'name' : 'Viernes' },
                        { 'dayofweek' : 5 , 'name' : 'Sabado' },
                        { 'dayofweek' : 6 , 'name' : 'Domingo' }
                    ]
                },
                value: value
            });
        $('<span class="k-invalid-msg" data-for="dayofweek"></span>').appendTo(container);
    };
</script>
<?php
$transport = new \Kendo\Data\DataSourceTransport();

$create = new \Kendo\Data\DataSourceTransportCreate();
$create->url('schedules-service.php?type=create')
    ->contentType('application/json')
    ->type('POST');

$read = new \Kendo\Data\DataSourceTransportRead();
$read->url('schedules-service.php?type=read')
    ->contentType('application/json')
    ->type('POST');

$update = new \Kendo\Data\DataSourceTransportUpdate();
$update->url('schedules-service.php?type=update')
    ->contentType('application/json')
    ->type('POST');

$destroy = new \Kendo\Data\DataSourceTransportDestroy();
$destroy->url('schedules-service.php?type=destroy')
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

$scheduleIdField = new \Kendo\Data\DataSourceSchemaModelField('idschedule');
$scheduleIdField->type('number')
    ->editable(false)
    ->nullable(true);

$scheduleDayValidation = new \Kendo\Data\DataSourceSchemaModelFieldValidation();
$scheduleDayValidation
    ->required(true)
    ->min(0)
    ->max(6);

$scheduleDayField = new \Kendo\Data\DataSourceSchemaModelField('dayofweek');
$scheduleDayField->type('number')
    ->nullable(true)
    ->validation($scheduleDayValidation);

$scheduleTimeValidation = new \Kendo\Data\DataSourceSchemaModelFieldValidation();
$scheduleTimeValidation
    ->required(true)
    ->min(0)
    ->max(23);

$scheduleTimeField = new \Kendo\Data\DataSourceSchemaModelField('time');
$scheduleTimeField->type('number')
    ->nullable(true)
    ->validation($scheduleTimeValidation);

$schedulePriceField = new \Kendo\Data\DataSourceSchemaModelField('price');
$schedulePriceField->type('number')
    ->nullable(true)
    ->validation(array('required' => true));

$model->id('idschedule')
    ->addField($scheduleIdField, $scheduleDayField, $scheduleTimeField, $schedulePriceField);

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
    ->serverSorting(false)
    ->pageSize(5)
    ->error('onDataSourceError')
    ->serverFiltering(true)
    ->schema($schema);

$grid = new \Kendo\UI\Grid('grid');

$dayColumn = new \Kendo\UI\GridColumn();
$dayColumn->field('dayofweek')
    ->title('Día de la semana')
    ->format('{0:n0}')
    ->editor('dayOfWeekDropDownEditor')
    ->template('#: getDay(dayofweek) #');
    //->editor('dayOfWeekEditor');

$timeColumn = new \Kendo\UI\GridColumn();
$timeColumn->field('time')
    ->title('Hora')
    ->format('{0:n0}')
    ->template('#: formatAMPM(time) #');

$priceColumn = new \Kendo\UI\GridColumn();
$priceColumn->field('price')
    ->title('Precio')
    ->format('{0:c}');

$command = new \Kendo\UI\GridColumn();
$command->addCommandItem('edit')
    ->addCommandItem('destroy')
    ->title('&nbsp;')
    ->width(250);

$grid->addColumn($dayColumn, $timeColumn, $priceColumn, $command)
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
            <h1 class="page-header">Catálogo de horarios para reservación</h1>
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
