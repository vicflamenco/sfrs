/**
 * Created by Víctor on 30/10/2016.
 */

$(function () {

    $('#changePasswordDiv').hide();
    $("#btnChangePassword").click(btnChangePasswordClick);
    $("#btnCancel").click(btnCancelClick);
    $("#frmChangePassword").submit(savePassword);

});

function btnChangePasswordClick() {
    $('#changePasswordDiv').show();
    $('#readUserInformationDiv').hide();
};

function btnCancelClick(){
    $('#readUserInformationDiv').show();
    $('#frmChangePassword').trigger('reset');
    $('#changePasswordDiv').hide();
};

function savePassword(e){
    e.preventDefault();

    var password = $("#password").val();
    var newPassword = $("#newPassword").val();
    var newPassword2 = $("#newPassword2").val();

    if (password != '' && newPassword != '' && newPassword2 != ''){
        if (newPassword == newPassword2){
            if (confirm('¿Desea guardar los cambios?')) {
                $.ajax({
                    url: '/sfrs/modules/security/users-service.php?type=change_password',
                    type: 'POST',
                    data: {'password': password, 'newPassword': newPassword, 'newPassword2': newPassword2}
                }).done(function (data) {
                    btnCancelClick();
                    alert(data.message);
                }).fail(function (jqXHR, textStatus, errThrown) {
                    alert(jqXHR.statusText);
                });
            }
        } else {
            alert('Las contraseñas nuevas no coinciden.');
        }
    } else {
        alert('Complete todos los datos del formulario.');
    }

};

