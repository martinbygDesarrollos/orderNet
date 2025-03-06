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
                if(count <= 0){
                    $('#cart').removeClass('withItems')
                } else {
                    if(!$('#cart').hasClass('withItems')){
                        $('#cart').addClass('withItems')
                    }
                    $('#cart').addClass('playAnimation')
                    setTimeout(() => {
                        $('#cart').removeClass('playAnimation');
                    }, 200);
                }
                
                console.log('Agregando al carrito el item con ID:', id);
                document.documentElement.style.setProperty('--cart-items', `"${count}"`);
                console.log(response)
            } else {
                console.log("error")
            }
        })
        .catch((error)=>{
            console.log("catch :"+error);
        })
}

function reduceItemToCart(id){
    let count = getCartItemsCount()

    sendAsyncPost("reduceItemToCart", { id: id })
        .then((response)=>{
            if (response.result == 2){
                count -= 1
                if(count <= 0){
                    $('#cart').removeClass('withItems')
                } else {
                    if(!$('#cart').hasClass('withItems')){
                        $('#cart').addClass('withItems')
                    }
                    $('#cart').addClass('playAnimation')
                    setTimeout(() => {
                        $('#cart').removeClass('playAnimation');
                    }, 200);
                }
                console.log('Quitando del carrito el item con ID:', id);
                document.documentElement.style.setProperty('--cart-items', `"${count <= 0 ? 0 : count}"`);
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
