/**
 * Created by Víctor on 19/10/2016.
 */

function assignRolesCommandItemClick(e){
    e.preventDefault();

    var dataItem = this.dataItem($(e.currentTarget).closest("tr"));
    window.location = "/sfrs/modules/security/user-roles.php?iduser=" + dataItem.iduser;

};

function passwordCommandItemClick(e){
    e.preventDefault();
    if (confirm('¿Desea generar una contraseña nueva para el usuario?')){
        var dataItem = this.dataItem($(e.currentTarget).closest("tr"));

        $.ajax({
            url: '/sfrs/modules/security/users-service.php?type=generate_password&iduser=' + dataItem.iduser,
            method: 'GET',
            dataType: 'json'
        }).done(function (data) {
            alert(data.message);
            location.reload();
        }).fail(function (jqXHR, textStatus, errThrown) {
            alert('Ocurrió un error al intentar generar una nueva contraseña para el usuario');
        });
    }
};

function onDataSourceError(e){
    alert(e.errorThrown);
};