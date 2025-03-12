$(document).ready(function() {

    $('.btnAddToCart').on('click', function() {
        var itemId = $(this).closest('tr').attr('id');
        $(this).addClass('bounceAnimation')
        setTimeout(() => {
            $(this).removeClass('bounceAnimation');
        }, 100);
        addItemToCart(itemId)
    });

    $('.btnRemoveFromCart').on('click', function() {
        var itemId = $(this).closest('tr').attr('id');
        $(this).addClass('bounceAnimation')
        setTimeout(() => {
            $(this).removeClass('bounceAnimation');
        }, 100);
        reduceItemToCart(itemId)
    });
})

function addItemToCart(id){
    let count = getCartItemsCount()

    sendAsyncPost("addItemToCart", { id: id }) // Agregar que compare la cantidad de items del carrito con la cantidad que tengo en la variable de css, si es igual que no cambie la variable de css
        .then((response)=>{
            if (response.result == 2){
                count += 1
                console.log(count)
                console.log(parseInt(response.cantidad))
                if(count <= 0){
                    $('#cart').removeClass('withItems')
                } else {
                    if(!$('#cart').hasClass('withItems')){
                        $('#cart').addClass('withItems')
                    }
                    if(response.cantidad == count){
                        $('#cart').addClass('playAnimation')
                        setTimeout(() => {
                            $('#cart').removeClass('playAnimation');
                        }, 200);
                        document.documentElement.style.setProperty('--cart-items', `"${count}"`);
                        $('#' + id).attr('data-quantity', (parseInt($('#' + id).attr('data-quantity')) || 0) + 1 )
                        if(parseInt($('#' + id).attr('data-quantity')) > 0){
                            if(!$('#' + id).hasClass('inTheCart'))
                                $('#' + id).addClass('inTheCart')
                        }
                    }
                }
                
                console.log('Agregando al carrito el item con ID:', id);
                console.log(response)
            } else {
                console.log("error")
            }
        })
        .catch((error)=>{
            console.log("catch :"+error);
        })
}

function reduceItemToCart(id){ // VER CUANDO ES EL ULTIMO QUE QUEDA
    let count = getCartItemsCount()

    sendAsyncPost("reduceItemToCart", { id: id })
        .then((response)=>{
            if (response.result == 2){
                count -= 1
                console.log(response.cantidad)
                console.log(count)
                if(count <= 0){
                    $('#cart').removeClass('withItems')
                    document.documentElement.style.setProperty('--cart-items', `"0"`);
                    $('#' + id).attr('data-quantity', 0)
                    $('#' + id).removeClass('inTheCart')
                } else {
                    if(!$('#cart').hasClass('withItems')){
                        $('#cart').addClass('withItems')
                    }
                    if(response.cantidad == count){
                        $('#cart').addClass('playAnimation')
                        setTimeout(() => {
                            $('#cart').removeClass('playAnimation');
                        }, 200);
                        document.documentElement.style.setProperty('--cart-items', `"${count <= 0 ? 0 : count}"`);
                        $('#' + id).attr('data-quantity', (parseInt($('#' + id).attr('data-quantity')) || 0) - 1 ) // SOLO UN DETALLE AQUI
                        if($('#' + id).attr('data-quantity') <= 0){
                            $('#' + id).removeClass('inTheCart')
                        }
                    }
                }
                console.log('Quitando del carrito el item con ID:', id);
                console.log(response)
            } else {
                console.log("error")
            }
        })
        .catch((error)=>{
            console.log("catch :"+error);
        })
}

function getCartItemsCount(){
    // Get the value of the CSS variable
    const rootStyles = getComputedStyle(document.documentElement);
    const cartItemsValue = rootStyles.getPropertyValue('--cart-items');

    // The value will include quotes (like '"5"') if it was set as content
    // To get just the number:
    const count = cartItemsValue.replace(/"/g, '').trim();
    console.log(count - 0 )
    return count - 0
}
