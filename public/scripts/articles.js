$('#btnNewArticle').on('click', function(){
    $('#inputArticleDetalle').val('')
    $('#inputArticleCodigo').val('')
    $('#inputArticleMarca').val('')
    $('#inputArticleProvider').val('')
    $('#inputArticleCodigoProvider').val('')
    openModalNewArticle();
})

function openModalNewArticle(){
    mostrarLoader(true)
    sendAsyncPost("getAllProviders", {})
        .then((response)=>{
            mostrarLoader(false)
            if (response.result == 2){
                console.log(response)
                // $('#inputArticleProvider').empty()
                // response.proveedores.forEach(proveedor => {
                //     option = `<option value="${proveedor.id}"> ${proveedor.proveedor}  </option>`
                //     $('#inputArticleProvider').append(option)
                // });
                console.log(response)
                $('#newArticleProvidersTable tbody').empty()
                response.proveedores.forEach(proveedor => {
                    row = `<tr id="proveedor-${proveedor.id}"> 
                                    <td style="align-content: center;">
                                        <input class="m-0 form-check-input" type="checkbox" value="" style="" id="newProvider-${proveedor.id}" > 
                                        <label class="ml-4 form-check-label" style="" for="newProvider-${proveedor.id}"> ${proveedor.proveedor} </label> 
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" id="providerCode-${proveedor.id}" placeholder="Ingresar codigo" disabled>
                                    </td>
                                </tr>`
                    $('#newArticleProvidersTable tbody').append(row)
                });

                $('#modalNewArticle').modal('show')
            } else {
                console.log("error")
            }
        })
        .catch((error)=>{
            mostrarLoader(false)
            console.log("catch :"+error);
        })
}


// Make sure this event handler is bound after the DOM is fully loaded
$(document).ready(function() {

    $('.tableContainer').on('scroll', function() {
        console.log("SCROLLEANDO")

        // Obtener la última fila visible dentro del contenedor
        let lastVisibleRow = $(this).find('tbody tr:visible').last();

        if (lastVisibleRow.length) {
            console.log("Última fila visible ID:", lastVisibleRow.attr('id'));
        }

        if($(this).scrollTop() + $(this).innerHeight() >= ($(this)[0].scrollHeight) - 10) {
            console.log("CARGAR MAS ARTICULOS")
            getArticles(lastVisibleRow.attr('id'));
        }
    });

    window.addEventListener('scroll', (event) => {
        console.log('Scroll detectado en window');
    });

    $('#buttonModalNewArticle').on('click', function(e){
        console.log("CREAR NUEVO ARTICULO")
        let detalle = $('#inputArticleDetalle').val().trim() || null
        let codigo = $('#inputArticleCodigo').val().trim() || null
        let marca = $('#inputArticleMarca').val().trim() || null
        let proveedores = [];
        $('#newArticleProvidersTable tbody tr').each(function() {
            const providerId = $(this).attr('id').replace('proveedor-', '').trim();
            let isChecked = $('#newProvider-' + providerId).prop('checked');
            // console.log('Provider ' + providerId + ' checked: ' + isChecked);
            if(isChecked){
                // Get the codigo value from the input field
                const codigo = $('#providerCode-' + providerId).val().trim();
                // console.log('Adding provider ' + providerId + ' with code: ' + codigo);
                // Add object with id and codigo to the array
                proveedores.push({
                    id: providerId,
                    codigo: codigo
                });
            }
        });
        // console.log('Final array:', proveedores);
        // if(proveedores.length == 0){
        //     return;
        // }
        if(detalle && proveedores.length > 0) {
            newArticle(detalle, codigo, marca, proveedores)
        }
    })

    $('#tableArticles tbody tr td:first-child').on('click', function() {
        var id = $(this).parent('tr').attr('id');
        console.log('ID del Articulo seleccionado:', id);
        showModalEditArticle(id)

    });

    $(document).on('click', 'input[type="checkbox"]', function(event) {
        let checkedBoxes = $('#providersTable tbody input[type="checkbox"]:checked');
        // If this is the only one checked, prevent unchecking
        if (checkedBoxes.length == 0 && $(this).prop('checked') == false) {
            $(this).prop('checked', true)
            event.preventDefault();
            return false;
        }

        // Get the closest tr and find the input field
        let row = $(this).closest('tr');
        let inputField = row.find('input[type="text"]');

        // Enable/Disable the input field based on checkbox state
        inputField.prop('disabled', !$(this).prop('checked'));
    });

})

function showModalEditArticle(id){ // SEGUIR ACA ACA ACA TERMINAR
    $('#proveedorTitle').text(`Proveedores(0)`)

    let response = sendPost("getAllProviders", {});
	if(response.result == 2){
		console.log(response)
        $('#providersTable tbody').empty()
        response.proveedores.forEach(proveedor => {
            row = `<tr id="proveedor-${proveedor.id}"> 
                            <td style="align-content: center;">
                                <input class="m-0 form-check-input" type="checkbox" value="" style="" id="provider-${proveedor.id}" > 
                                <label class="ml-4 form-check-label" style="" for="provider-${proveedor.id}"> ${proveedor.proveedor} </label> 
                            </td>
                            <td>
                                <input type="text" class="form-control" id="providerCode-${proveedor.id}" placeholder="Ingresar codigo" disabled>
                            </td>
                        </tr>`
            $('#providersTable tbody').append(row)
        });
        mostrarLoader(true)
        sendAsyncPost("getArticle", { articleId: id })
            .then((response)=>{
                mostrarLoader(false)
                if (response.result == 2){
                    console.log(response)
                    $('#inputEditArticleDetalle').val(response.articulo.detalle)
                    $('#inputEditArticleCodigo').val(response.articulo.codigo)
                    $('#inputEditArticleMarca').val(response.articulo.marca)

                    // Process the providers from the response
                    if (response.proveedores && response.proveedores.length > 0) {
                        response.proveedores.forEach(proveedor => {
                            // Find the row with this provider's name
                            $('#providersTable tbody tr').each(function() {
                                const providerId = $(this).attr('id').replace('proveedor-', '').trim();
                                console.log(providerId)
                                if (providerId == proveedor.id) {
                                    $('#provider-' + providerId).prop('checked', true);
                                    $(this).find('input').attr('disabled', false)
                                    if (proveedor.codigo) {
                                        $(this).find('input').val(proveedor.codigo);
                                    }
                                }
                            });
                        });
                    }
                    $('#proveedorTitle').text(`Proveedores(${response.proveedores.length})`)

                    $('#buttonModalEditArticle').off().on('click', function(){
                        proveedores = getCheckedProviders()
                        editArticle(id, $('#inputEditArticleDetalle').val(),$('#inputEditArticleCodigo').val(), $('#inputEditArticleMarca').val(), proveedores)
                    })
                    $('#modalEditArticle').modal()
                    // window.location.href = getSiteURL() + "proveedores";
                } else {
                    console.log("error")
                }
            })
            .catch((error)=>{
                mostrarLoader(false)
                console.log("catch :"+error);
            })
	}
}

function getCheckedProviders() {
    let selectedProviders = [];

    $('#providersTable tbody input[type="checkbox"]:checked').each(function() {
        let row = $(this).closest('tr');
        let id = parseInt(row.attr('id').replace('proveedor-', '').trim()); // Get the tr ID
        let codigo = row.find('input[type="text"]').val() || null; // Get the input value

        selectedProviders.push({
            id: id,
            codigo: codigo
        });
    });

    return selectedProviders;
}

function editArticle(id, detalle, codigo, marca, proveedores){
    mostrarLoader(true)
    sendAsyncPost("editArticle", { articleId: id, detalle: detalle, codigo: codigo, marca: marca, proveedores: proveedores })
        .then((response)=>{
            mostrarLoader(false)
            if (response.result == 2){
                console.log(response)
                window.location.href = getSiteURL() + "articulos";
            } else {
                console.log("error")
            }
        })
        .catch((error)=>{
            mostrarLoader(false)
            console.log("catch :"+error);
        })

    console.log(id)
    console.log(proveedores)
}


function getArticles(lastId){
    // mostrarLoader(true)
    sendAsyncPost("getAllArticles", { lastId: lastId })
        .then((response)=>{
            // mostrarLoader(false)
            if (response.result == 2){
                console.log(response)
                response.articulos.forEach(element => {
                    $('#tableArticles tbody').append(`
                        <tr id="${element.id}">
                            <td>${element.detalle}</td>
                            <td>${element.codigo || ""}</td>
                            <td>${element.marca || ""}</td>
                            <td>
                                ${element.proveedores.map(proveedor => `<p class="mb-0">${proveedor.nombre}</p>`).join('')}
                            </td>
                        </tr>
                    `);
                });
            } else {
                console.log("error")
            }
        })
        .catch((error)=>{
            // mostrarLoader(false)
            console.log("catch :"+error);
        })
}

function newArticle(detalle, codigo, marca, proveedores){
    mostrarLoader(true)
    sendAsyncPost("newArticle", { detalle: detalle, codigo: codigo, marca: marca, proveedores: proveedores})
        .then((response)=>{
            mostrarLoader(false)
            if (response.result == 2){
                window.location.href = getSiteURL() + "articulos";
            } else {
                console.log("error")
            }
        })
        .catch((error)=>{
            mostrarLoader(false)
            console.log("catch :"+error);
        })
}