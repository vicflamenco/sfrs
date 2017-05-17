<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 10/8/2016
 * Time: 10:07 PM
 */

$view_name = "index";
require_once 'include/header.php';

function renderDatePicker(){
    $datepicker = new \Kendo\UI\DatePicker('reservation_date');
    $datepicker
        ->change('loadSoccerFields')
        ->format('dd-MM-yyyy')
        ->min(Date((new Datetime('tomorrow'))->format('Y-m-d')))
        ->attr('style', 'width: 100%')
        ->attr('required','required');
    echo $datepicker->render();
}

function renderTimePicker(){
    $timePicker = new \Kendo\UI\TimePicker('time');
    $timePicker
        ->change('loadSoccerFields')
        ->attr('style', 'width: 100%')
        ->interval(60)
        ->attr('required','required');
    echo $timePicker->render();
}

?>

<script type="text/javascript" src="/sfrs/js/modules/index.js"></script>

<div id="page-wrapper">
    <div class="row">
        <div class="col-md-12">
            <h1 class="page-header">Reserva de canchas</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">

            <form class="form-horizontal" id="frmMakeReservation">
                <fieldset>

                    <input type="hidden" name="type" value="make_reservation" />
                    <!-- Form Name -->
                    <legend>Formulario de reserva</legend>

                    <!-- Text input-->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="reservation_date">Fecha:</label>
                        <div class="col-md-4">
                            <?php renderDatePicker(); ?>
                        </div>
                    </div>

                    <!-- Text input-->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="time">Hora:</label>
                        <div class="col-md-4">
                            <?php renderTimePicker(); ?>
                        </div>
                    </div>

                    <!-- Select Multiple -->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="soccer_field">Cancha:</label>
                        <div class="col-md-4">
                            <select id="soccer_field" name="soccer_field" class="form-control" size="7" required="required">
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label"></label>
                        <div class="col-md-4">
                            <input type="submit" value="Reservar" class="btn btn-primary" />
                        </div>
                    </div>

                </fieldset>
            </form>

        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div id="responseText">

            </div>
        </div>
    </div>
</div>


<?php
    require_once 'include/footer.php';
?>


