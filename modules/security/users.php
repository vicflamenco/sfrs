<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 10/9/2016
 * Time: 11:04 PM
 */

$view_name = "users";
require_once $_SERVER['DOCUMENT_ROOT'] . '/sfrs/include/header.php';

$transport = new \Kendo\Data\DataSourceTransport();

$create = new \Kendo\Data\DataSourceTransportCreate();
$create->url('users-service.php?type=create')
    ->contentType('application/json')
    ->type('POST');

$read = new \Kendo\Data\DataSourceTransportRead();
$read->url('users-service.php?type=read')
    ->contentType('application/json')
    ->type('POST');

$update = new \Kendo\Data\DataSourceTransportUpdate();
$update->url('users-service.php?type=update')
    ->contentType('application/json')
    ->type('POST');

$destroy = new \Kendo\Data\DataSourceTransportDestroy();
$destroy->url('users-service.php?type=destroy')
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

$userIdField = new \Kendo\Data\DataSourceSchemaModelField('iduser');
$userIdField->type('number')
    ->editable(false)
    ->nullable(true);

$userFirstnameField = new \Kendo\Data\DataSourceSchemaModelField('firstname');
$userFirstnameField->type('string')
    ->validation(array('required' => true));

$userLastnameField = new \Kendo\Data\DataSourceSchemaModelField('lastname');
$userLastnameField->type('string')
    ->validation(array('required' => true));

$emailField = new \Kendo\Data\DataSourceSchemaModelField('email');
$emailField->type('string')
    ->validation(array('required' => true));

$model->id('iduser')
    ->addField($userIdField, $userFirstnameField, $userLastnameField, $emailField);

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
    ->error('onDataSourceError')
    ->schema($schema);

$grid = new \Kendo\UI\Grid('grid');

$firstnameColumn = new \Kendo\UI\GridColumn();
$firstnameColumn->field('firstname')
    ->title('Nombres')
    ->width('20%');

$lastnameColumn = new \Kendo\UI\GridColumn();
$lastnameColumn->field('lastname')
    ->title('Apellidos')
    ->width('20%');

$emailColumn = new \Kendo\UI\GridColumn();
$emailColumn->field('email')
    ->title('Correo')
    ->width('20%');

$assignRolesCommandItem = new \Kendo\UI\GridColumnCommandItem();
$assignRolesCommandItem->click('assignRolesCommandItemClick')
                        ->text('Roles');

$passwordCommandItem = new \Kendo\UI\GridColumnCommandItem();
$passwordCommandItem->click('passwordCommandItemClick')
    ->text('Contraseña');

$commandColumn = new \Kendo\UI\GridColumn();
$commandColumn->addCommandItem('edit')
    ->addCommandItem('destroy')
    ->addCommandItem($assignRolesCommandItem)
    ->addCommandItem($passwordCommandItem)
    ->title('&nbsp;');


$grid->addColumn($firstnameColumn, $lastnameColumn, $emailColumn, $commandColumn)
    ->dataSource($dataSource)
    ->addToolbarItem(new \Kendo\UI\GridToolbarItem('create'))
    ->height(400)
    ->pageable(true)
    ->filterable(true)
    ->sortable(true)
    ->editable('popup');

?>
    <script type="text/javascript" src="/sfrs/js/modules/security/users.js"></script>

    <div id="page-wrapper">
        <div class="row">
            <div class="col-md-12">
                <h1 class="page-header">Catálogo de usuarios</h1>
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