/**
 * Created by Víctor on 19/10/2016.
 */

function assignModulesCommandItemClick(e){
    e.preventDefault();

    var dataItem = this.dataItem($(e.currentTarget).closest("tr"));
    window.location = "/sfrs/modules/security/role-modules.php?idrole=" + dataItem.idrole;

};