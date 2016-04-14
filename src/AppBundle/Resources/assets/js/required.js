function userRolesPreSelections(userRoles)
{
    var userRoles = JSON.parse(userRoles);
    var i, userRole, ddlOption;
    for (i = 0; i < userRoles.length; i++) {
        ddlOption = $("#user_roles option[value='" + userRoles[i].roleId + "']").prop('selected', true);
    }
}

function pageActionPreSelections(actionsToPageData)
{
    var actionsToPageData = JSON.parse(actionsToPageData);
    var i, userRole, ddlOption;
    for (i = 0; i < actionsToPageData.length; i++) {
        ddlOption = $("#page_actions option[value='" + actionsToPageData[i].actionId + "']").prop('selected', true);
    }
}

function pagePageParentPreSelections(pageParentId, pageId)
{
    ddlOption = $("#page_pageParentId option[value='" + pageId + "']").remove();
    ddlOption = $("#page_pageParentId option[value='" + pageParentId + "']").prop('selected', true);
}

function actionRolesPreSelections(actionsToPageData)
{
    var actionsToPageData = JSON.parse(actionsToPageData);
    var i, userRole, ddlOption;
    for (i = 0; i < actionsToPageData.length; i++) {
        ddlOption = $("#action_roles option[value='" + actionsToPageData[i].roleId + "']").prop('selected', true);
    }
}
