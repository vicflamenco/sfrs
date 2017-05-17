/**
 * Created by Víctor on 30/10/2016.
 */

$(function () {

    $('#frmMakeReservation').submit(function (e) {

        e.preventDefault();
        var data = $(this).serialize();

        $.ajax({
            'url': '/sfrs/modules/catalogs/reservations-service.php',
            'method': 'GET',
            'dataType': 'json',
            'data': data
        }).done(function () {
            alert('La reservación se creó satisfactoriamente.');
            window.location = '/sfrs/modules/common/myreservations.php';
        }).fail(function (jqXHR, textStatus, errThrown) {
            alert(jqXHR.statusText);
        });
    });

});
function loadSoccerFields(){

    var control = $('#soccer_field');

    control.find('option').remove();

    var timeControl = $("#time").data("kendoTimePicker");
    var reservation_dateControl = $("#reservation_date").data("kendoDatePicker");

    if (timeControl && reservation_dateControl){

        var time = timeControl.value();
        var reservation_date = new Date(reservation_dateControl.value());

        if (time != null && reservation_date != null){

            var rd = reservation_date.getFullYear() + '-' + (reservation_date.getMonth() + 1) + '-' + reservation_date.getDate();

            $.ajax({
                'url': '/sfrs/modules/catalogs/reservations-service.php?type=getAvailableSoccerFields&time='+
                            time.getHours()+'&reservation_date='+ rd,
                'method': 'GET',
                'dataType': 'json'
            }).done(function (data) {

                var control = $('#soccer_field');
                var soccer_fields = data.data;

                $.each(soccer_fields, function (i, item) {
                    control.append($('<option>', {
                        value: item.idsoccer_field,
                        text : item.name
                    }));
                });

            }).fail(function (jqXHR, textStatus, errThrown) {
                alert(jqXHR.statusText);
            });
        }
    }
};

