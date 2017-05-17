/**
 * Created by Víctor on 19/10/2016.
 */

//===============================================================================================================
//  Funciones que responden a eventos de botones
//===============================================================================================================
function cancelReservationCommandItemClick(e){
    e.preventDefault();
    if (confirm("¿Desea cancelar la reservación?")) {
        var dataItem = this.dataItem($(e.currentTarget).closest("tr"));
        changeReservationStatus(dataItem.idreservation, 6);
    }

};

function changeReservationStatus(idreservation, idreservation_state){
    $.ajax({
        'url': '/sfrs/modules/common/myreservations-service.php?type=change_status&idreservation=' +
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
