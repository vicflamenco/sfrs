/**
 * Created by Víctor on 19/10/2016.
 */

//===============================================================================================================
//  Funciones que responden a eventos de botones
//===============================================================================================================
function payReservationCommandItemClick(e){
    e.preventDefault();
    if (confirm("¿Desea cambiar el estado de la reservación a pagada?")){
        var dataItem = this.dataItem($(e.currentTarget).closest("tr"));
        changeReservationStatus(dataItem.idreservation, 5);
    }
};

function cancelReservationCommandItemClick(e){
    e.preventDefault();
    if (confirm("¿Desea cancelar la reservación?")) {
        var dataItem = this.dataItem($(e.currentTarget).closest("tr"));
        changeReservationStatus(dataItem.idreservation, 6);
    }

};

function changeReservationStatus(idreservation, idreservation_state){
    $.ajax({
        'url': '/sfrs/modules/catalogs/reservations-service.php?type=change_status&idreservation=' +
                idreservation + '&idreservation_state=' + idreservation_state,
        'type': 'GET',
        'dataType': 'json'
    }).done(function () {
        window.location.reload();
    }).fail(function (jqXHR, textStatus, errorThrown) {
        alert(jqXHR.statusText);
    });
};

function onDataSourceError(e){
    alert(e.errorThrown);
};

function formatAMPM(time) {
    var hours = time;
    var ampm = time >= 12 ? 'pm' : 'am';
    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    var strTime = hours + ':00 ' + ampm;
    return strTime;
};

function usernameDropDownEditor(container, options) {
    $('<input data-text-field="username" data-value-field="iduser" data-bind="value:' + options.field + '"/>')
        .appendTo(container)
        .kendoDropDownList({
            autoBind: false,
            dataSource: {
                schema: {
                    data: "data"
                },
                transport: {
                    read: {
                        url: "reservations-service.php?type=users",
                        type: "POST",
                        dataType: "json"
                    }
                }
            }
        });
    $('<span class="k-invalid-msg" data-for="username"></span>').appendTo(container);
};

function soccer_fieldDropDownEditor(container, options) {
    $('<input data-text-field="name" data-value-field="idsoccer_field" data-bind="value:' + options.field + '"/>')
        .appendTo(container)
        .kendoDropDownList({
            autoBind: false,
            dataSource: {
                schema: {
                    data: "data"
                },
                transport: {
                    read: {
                        url: "reservations-service.php?type=soccer_fields",
                        type: "POST",
                        dataType: "json"
                    }
                }
            }
        });
};
//===============================================================================================================
//  Funciones para obtener fecha de reserva en formato UTC
//===============================================================================================================
var offsetMiliseconds = new Date().getTimezoneOffset() * 60000;

function onRequestEnd(e) {
    if (e.response.Data && e.response.Data.length) {
        var records = e.response.Data;
        if (this.group().length) {
            for (var i = 0; i < records.length; i++) {
                var gr = records[i];
                if (gr.Member == "reservation_date") {
                    gr.Key = gr.Key.replace(/\d+/,
                        function (n) { return parseInt(n) + offsetMiliseconds }
                    );
                }
                addOffset(gr.Items);
            }
        } else {
            addOffset(records);
        }

    }
};

function addOffset(records) {
    for (var i = 0; i < records.length; i++) {
        records[i].reservation_date = records[i].reservation_date.replace(/\d+/,
            function (n) { return parseInt(n) + offsetMiliseconds }
        );
    }
};