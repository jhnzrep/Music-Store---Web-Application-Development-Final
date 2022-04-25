$(document).ready(() =>{
    getCart();
    $('#purchase').click(() =>{
        purchase();
    });
});

getCart = () =>{
    let state = sessionStorage.getItem('state');
    let type = 'get';
    let data = JSON.stringify({state, type});
    //let url = 'http://localhost/web/controllers/cart';
    let url = '../API/cart';

    $.post(url, data)
    .done((result) => {
        console.log(result);
        $('#BillingAddress').val(result[1].Address);
        $('#BillingCity').val(result[1].City);
        $('#BillingState').val(result[1].State);
        $('#BillingCountry').val(result[1].Country);
        $('#BillingPostalCode').val(result[1].PostalCode);
        if(result[0] != null){
            $('#total').append(result[0].Total);
            result[0].item.forEach(item => {
                let htmltext = '<div class="item"><p class="column">Name:' + item.Name + '</p><p class="column">Price:' + item.UnitPrice + '</p><p class="column">Quantity:' + item.Quantity + '</p></div>';
                $('#results').append(htmltext);
            });
        }
        
    })
}

purchase = () =>{
    let Address = $('#BillingAddress').val();
    let City = $('#BillingCity').val();
    let State= $('#BillingState').val();
    let Country = $('#BillingCountry').val();
    let PostalCode = $('#BillingPostalCode').val();
    let type = 'purchase';
    let state = sessionStorage.getItem('state');
    
    let data = JSON.stringify({state, type, Address, City, State, Country, PostalCode});
    //let url = 'http://localhost/web/controllers/cart';
    let url = '../API/cart';
    $.post(url, data)
    .done((result)=>{
        console.log(result)
        sessionStorage.setItem('state', result);
        $('#total').empty();
        $('#results').empty();
        getCart();
    })
    .fail(()=>{
        console.log('failed to purchase');
    })
}