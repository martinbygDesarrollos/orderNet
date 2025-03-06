$('#tableUsers tbody tr').on('click', function() {
    var id = $(this).attr('id');
    console.log('ID del usuario seleccionado:', id);
    openModalEditUser(id);
});

function openModalEditUser(id){
    mostrarLoader(true)
    sendAsyncPost("getUserData", { id: id })
        .then((response)=>{
            mostrarLoader(false)
            if (response.result == 2){
                console.log(response)
                $('#tableUserInventarios tbody').empty()
                $('#modalEditUser').attr('data-userId', id);
                $('#inputUsuario').val(response.usuario.usuario)
                $('#btnCleanPasswordUser').on('click', function(){
                    cleanPassword(id)
                });
                if(response.secciones.length > 0)
                    cargarSecciones(response.secciones, response.usuario.id, response.usuario.usuario);
                $('#modalEditUser').modal('show')
            } else {
                console.log("error")
            }
        })
        .catch((error)=>{
            mostrarLoader(false)
            console.log("catch :"+error);
        })
}

function cargarSecciones(secciones, userId, user){
    console.log('cargarSecciones')
    $('#tableUserInventarios tbody').empty()
    secciones.forEach(element => {  
        let row = `<tr id='${element.id}'> <td class='text-align-start d-flex align-content-center justify-content-center'><p class="mb-0">${element.seccion}</p></td> <td class='text-center'> <a class="btn template-btn" href="${getSiteURL()}secciones/${element.id}" title="Editar Inventario"><i class="fas fa-edit"></i></a> <button class="btn template-btn" onclick="confirmDeleteInventario(${element.id}, '${element.seccion}', ${userId}, '${user}')" title="Eliminar Inventario"><i class="fas fa-trash"></i></button> </td> </tr>`
        $('#tableUserInventarios tbody').append(row)
    });
}

function cleanPassword(id){
    $('#modalEditUser').modal('hide')
    mostrarLoader(true)
    sendAsyncPost("cleanPassword", { id: id })
        .then((response)=>{
            mostrarLoader(false)
            if (response.result == 2){
                showReplyMessage(response.result, response.message, "Notificación", "modalEditUser");
            } else {
                console.log("error")
            }
        })
        .catch((error)=>{
            mostrarLoader(false)
            console.log("catch :"+error);
        })
}

function confirmDeleteInventario(sectionId, sectionName, userId, userName){
    $('#modalEditUser').modal('hide')
    console.log(`ELIMINAR RELACION SECCION(${sectionId}, ${sectionName}, ${userId}, ${userName})`)
    $('#modalDeleteRelUserSection').on('hidden.bs.modal', function () {
        openModalEditUser(userId)
    })
    $('#relationLabel').text(`¿Eliminar relación ${sectionName} - ${userName} ?`)

    $('#buttonModalDeleteRelation').off('click').on('click', function () {
        mostrarLoader(true)
        sendAsyncPost("removeRelationUserSection", { sectionId: sectionId, userId: userId })
            .then((response)=>{
                mostrarLoader(false)
                if (response.result == 2){
                    $('#modalDeleteRelUserSection').modal('hide')
                    // showReplyMessage(response.result, response.message, "Notificación", "modalEditUser");
                } else {
                    console.log("error")
                }
            })
            .catch((error)=>{
                mostrarLoader(false)
                console.log("catch :"+error);
            })
    })

    $('#modalDeleteRelUserSection').modal()
}

