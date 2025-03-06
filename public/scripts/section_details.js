function confirmRemoveSubsectionFromSection(idSubSection, nameSubSection, section){
    console.log(`ELIMINAR RELACION(SUBSECCION ${idSubSection}, SECCION ${section})`)
    $('#relationLabel2').text(`¿Eliminar sección "${nameSubSection}" del inventario ?`)
    $('#buttonModalDeleteRelation2').off('click').on('click', function () {
        mostrarLoader(true)
        sendAsyncPost("removeRelationSectionSubSection", { idSubSection: idSubSection, idSection: section })
            .then((response)=>{
                mostrarLoader(false)
                if (response.result == 2){
                    $('#modalDeleteRelSectionSubSection').modal('hide')
                    // showReplyMessage(response.result, response.message, "Notificación", "modalEditUser");
                    window.location.href = getSiteURL() + `secciones/${section}`;

                } else {
                    console.log("error")
                }
            })
            .catch((error)=>{
                mostrarLoader(false)
                console.log("catch :"+error);
            })
    })
    $('#modalDeleteRelSectionSubSection').modal()
}

$(document).ready(function() {

    $('#tableSubSections tbody tr td:first-child').on('click', function() {
        var id = $(this).parent('tr').attr('id');
        console.log('ID de la subseccion seleccionada:', id);
        window.location.href = getSiteURL() + `subsecciones/${id}`;
    });

    $('#btnNewSubSection').on('click', function(e){
        seccionId = $('#tableSubSections').attr('seccion')
        console.log(`AGREGAR NUEVA SUBSECCION A LA SECCION ${seccionId}`)
        mostrarLoader(true)
        sendAsyncPost("getAllSubSections", {})
            .then((response)=>{
                console.log(response)
                mostrarLoader(false)
                if (response.result == 2){
                    $('#selectSectionSubSection').empty()
                    response.subsecciones.forEach(element => {
                        // Check if a row with this ID already exists in the table
                        if ($('#tableSubSections tr#' + element.id).length === 0) {
                            option = `<option value="${element.id}"> ${element.subseccion}</option>`
                            $('#selectSectionSubSection').append(option)
                        }
                    });

                    $('#buttonModalAddSubSection').off().on('click', function(){
                        nuevaRelacionSeccionSubseccion(seccionId, $('#selectSectionSubSection option:selected').val());
                    })

                    $('#modalAddSubSection').modal()
                }
            })
            .catch((error)=>{
                mostrarLoader(false)
                console.log("catch :"+error);
            })
    })
    // $('#tableSections tbody tr td:first-child').on('click', function() {
    //     var id = $(this).parent('tr').attr('id');
    //     console.log('ID de la Seccion seleccionada:', id);
    //     window.location.href = getSiteURL() + `secciones/${id}`;
    // });
})

function nuevaRelacionSeccionSubseccion(seccion, subseccion){
    console.log(`seccion: ${seccion}. subseccion: ${subseccion}`)
    sendAsyncPost("addRelationSectionSubSection", {sectionId: seccion, subSectionId: subseccion})
        .then((response)=>{
            console.log(response)
            if (response.result == 2){
                window.location.href = getSiteURL() + `secciones/${seccion}`;
            } else {
                // console.log("error")
            }
        })
        .catch((error)=>{
            // mostrarLoader(false)
            console.log("catch :"+error);
        })
}
