/**
 * Created by Víctor on 19/10/2016.
 */
$(function () {

    $('#btnAssignRoles').click(function () {
        if (confirm("¿Desea guardar los cambios?")){
            var rolesMultiSelect = $("#rolesMultiSelect").data("kendoMultiSelect");
            $("#selectedRoles").val(rolesMultiSelect.value().join(","));
            $("#frmAssignRoles").submit();
        }
    });

    $('#btnCancel').click(function () {
        window.location = '/sfrs/modules/security/users.php';
    });

});





