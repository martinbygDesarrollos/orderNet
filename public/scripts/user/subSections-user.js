
$(document).ready(function() {
    // $('#buttonModalNewSubSection').on('click', function(e){
    //     console.log("CREAR NUEVA SUBSECCION")
    //     let nombre = $('#inputSubSectionName').val().trim() || null
    //     if(nombre) {
    //         newSubSection(nombre)
    //     }
    // })

    $('#tableUserSubSections tbody tr td:first-child').on('click', function() {
        var id = $(this).parent('tr').attr('id');
        console.log('ID de la Subseccion seleccionada:', id);
        window.location.href = getSiteURL() + $('#tableUserSubSections').attr('data-seccion') + `/${id}/items`;
    });

    $('input[type=checkbox]').on('click', function(){

        let subseccion = this.id.replace('subCheck-', ''); // Removes 'subCheck-' from the ID
        console.log("Check pressed: " + subseccion);
        console.log("Is checked? " + $(this).prop('checked'));
        seccion = $('#tableUserSubSections').attr('data-seccion')
        estado = $(this).prop('checked') ? true : false;
        
        console.log(`seccion: ${seccion}, subseccion: ${subseccion}, estado: ${estado} `);
        changeStatus(seccion, subseccion, estado)

    })
})

function changeStatus(seccion, subseccion, status){
    mostrarLoader(true)
    sendAsyncPost("changeStatusSubsection", { seccion: seccion, subseccion: subseccion, newStatus: status })
        .then((response)=>{
            mostrarLoader(false)
            if (response.result == 2){
                window.location.href = getSiteURL() + $('#tableUserSubSections').attr('data-seccion') + "/subsecciones";
                // console.log(response)
                // $('#tableUserInventarios tbody').empty()
                // $('#modalEditUser').attr('data-userId', id);
                // $('#inputUsuario').val(response.usuario.usuario)
                // $('#btnCleanPasswordUser').on('click', function(){
                //     cleanPassword(id)
                // });
                // if(response.secciones.length > 0)
                //     cargarSecciones(response.secciones, response.usuario.id, response.usuario.usuario);
                // $('#modalEditUser').modal('show')
            } else {
                console.log("error")
            }
        })
        .catch((error)=>{
            mostrarLoader(false)
            console.log("catch :"+error);
        })
}