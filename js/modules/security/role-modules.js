/**
 * Created by Víctor on 19/10/2016.
 */
$(function () {

    $('#btnAssignModules').click(function () {
        if (confirm("¿Desea guardar los cambios?")){
            var modulesMultiSelect = $("#modulesMultiSelect").data("kendoMultiSelect");
            $("#selectedModules").val(modulesMultiSelect.value().join(","));
            $("#frmAssignModules").submit();
        }
    });

    $('#btnCancel').click(function () {
        window.location = '/sfrs/modules/security/roles.php';
    });

});





