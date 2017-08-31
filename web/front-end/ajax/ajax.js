function showCompForm() {
    _get('composant_new','','contentModalComp','minload');
    _showModal('frmCompModal');
}
function showUserForm() {
    _get('user_add','','contentModalUser','');
    _showModal('frmUserModal');
}
/** Affiche le formulaire d'ajout d'un profil **/
function showProfilFrom() {
    _get('profile_add','', 'contentModalProfil', '');
    _showModal('frmProfilModal');
}

function showNodeForm() {
    _get('node_new','','contentModalScope','minloadScope');
    _showModal('frmScopeModal');
}
function getPage() {
    _get('composant_list','', 'composant','minloadComp');
}
function showAxeForm() {
    _get('axis_new','','contentModalAxe','minloadAxe');
    _showModal('frmAxeModal');
}
function showGroupCompForm() {
    _get('groupe_new','','contentGroupModalComp','');
    _showModal('frmGroupCompModal');
}
function showGroupAxegroupForm() {
    _get('axegroupe_new','','contentGroupModalAxe','');
    _showModal('frmGroupAxeModal');
}
function updateLevel(id) {
    _get('level_edit',id,'contentModalLevel'+id);
    _showModal('frmUpdateLevel'+id);
}
function updateNode(id) {
    _get('node_edit',id,'contentModalNode'+id,'editloading');
    _showModal('frmUpdateNode'+id);
}
function updateAxe(id) {
    _get('axis_edit',id,'contentModalAxe'+id,'editloading');
    _showModal('frmUpdateAxe'+id);
}
function updates(route,id,contentModal,modalId,loding) {
 _get(route, id, contentModal+id);
    _showModal(modalId + id);
}
function _get(route, id, contentId,lodingId) {
    if(route){
        _empty(contentId);
        _display(lodingId+id);
        var url = getUrl(route,id);
        $.ajax({
            type: 'GET',
            url: url,
            success: function(response)
            {
                if(response.error == null){
                    _hide(lodingId+id);
                    _empty(contentId);
                    _setHtmlValue(contentId, response.view);
                    if (_getHtmlValue('frmAxeModal') !== '')
                    {
                        if (response.axis && response.axis.length == 0) {
                            $('#iscalculated').attr('disabled','disabled');
                        }
                    }
                }
                else showErrorResponse(response, 'flashError')
            },
            error: function(response){
                alert(response.error);
            }
        })
    }
};
function _getComposantAndAxe() {
    _get('composant_list','', 'idComposant');
}
function addComposant() {
    _post('composant_new', 'addComposant', 'frmCompModal', 'loadingComp', 'flashErrorFrom',function () {
        _get('composant_list','', 'composant');
    });

}
function addAxe() {
    _post('axis_new','formAxis', 'frmAxeModal', 'loadingAxe', 'flashErrorFrom', function () {
        _get('axis_lists','','axe');
    });

}
function addGroupComp() {
    _post('groupe_new', 'addgroupComp', 'frmGroupCompModal', 'loadingComp', 'flashErrorFrom',function () {
        _get('group_list','', 'groupeComp');
    });
}
function addAxeGroup() {
    _post('axegroupe_new', 'addAxegroup', 'frmGroupAxeModal', 'loadingComp', 'flashErrorFrom',function () {
        _get('axegroupe_list','', 'groupeAxe');
    });
}
function addUser() {
    _post('user_add', 'frmUserAdd', 'frmUserModal', 'loadingUser', 'flashErrorFrom',function () {
        _get('list_users','', 'users', 'loadingComp2');
    });
}
function addProfil(){
    _post('profile_add', 'frmProfilAdd', 'frmProfilModal', 'loadingProfil', 'flashErrorUsers', function () {
        _get('profile_list','', 'profiles');
    });
}
/**
 * Modification d'un niveau
 * @param id
 */
function editLevel(id) {
    _post('level_edit','editLevel','','loadingLevel','flashErrorFrom',function () {
        _get('list_level','','levels','loadinglevel');
    },id)
}

/**
 * Modification d'un noeud
 * @param id
 */
function editNode(id) {
    _post('node_edit','editNode','','loadingScope','flashErrorFrom', function () {
        _get('list_node','','nodes','loadingnoeud');
    }, id)
}

/**
 * Modification d'un axe
 * @param id
 */
function editAxe(id) {
    _post('axis_edit','editAxe','','loadingScope','flashErrorFrom', function () {
        _get('list_axe','','axes','loadingaxe');
    }, id)
}
function addScope() {
    _post('node_new', 'ScopeForm', 'frmScopeModal', 'loadingScope', 'flashErrorFrom', function () {
        _get('node_list','','scope');
    });
}
function _post(route, formId,  modal, loading, flashError, callback,id) {
    _show(loading);
    $('.modal-backdrop').attr('id', 'modalClose'+id);
    $('#vusalba_vuebundle_axis_formula').removeAttr('disabled');
    var url = getUrl(route,id);
    if (route) {
        $.ajax({
            type: 'POST',
            url : url,
            data : _serializeForm(formId),
            success: function(response)
            {
                if(response.error == null){
                    _hide(loading);
                    // On peut exécuter tout ce que l'on veut dans le fn callback
                    $('#' + 'modalClose'+id).removeClass('modal-backdrop fade in');
                    if (_getHtmlValue('frmCompModal') !== '') { $('#close').click();}
                    if (_getHtmlValue('frmAxeModal') !== '') { $('#closeAxe').click();}
                    if (_getHtmlValue('frmScopeModal') !== '') { $('#closeScope').click();}
                    if (_getHtmlValue('frmUpdateLevel'+id) !== '') { $('#' + 'closeLevel'+id).click();}
                    if (_getHtmlValue('frmUpdateAxe'+id) !== '') { $('#' + 'closeAxe'+id).click();}
                    if (_getHtmlValue('frmUserModal') !== '') { $('#' + 'closeuser').click();}

                    callback();
                }
                else showErrorResponse(response, flashError)
            },
            error: function(response){
                // alert('Error : '+ response.error);
            }
        })
    }
}

function createEntity(loading) {
    // $('.modal-backdrop').attr('id', 'modalClose');
    $('#loader').css('display', 'block');
    $.ajax({
        type: 'POST',
        url: Routing.generate('create_entity'),
        data : {},
        success : function (response) {
            $('#loader').css('display', 'none');
            if (response.error == '') {
                _show('finalId');
            }
            else {
                alert(response.error);
            }
            // _hide('generateId');
            // _show('updateId');
            // $('#finalId').css('display', 'block');
        },
        error : function (response) {
            alert("Erreur lors du traitement !!!");
        }
    });
}
function générateAll() {
    $('#loader').css('display', 'block');
    $.ajax({
        type: 'POST',
        url: Routing.generate('generate_all'),
        data : {},
        success : function (response) {
            $('#loader').css('display', 'none');
            // $('#finalId').css('display', 'none');
            window.location.href = Routing.generate('enter_index');
        },
        error : function (response) {
            alert("Erreur lors du traitement");
        }
    });
}