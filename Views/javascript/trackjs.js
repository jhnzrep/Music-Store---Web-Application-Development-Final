$(document).ready(() =>{
    $('#searchbtn').click(() =>{
        search();
    });
    $('#cart').click(() =>{
        location.href = './cartpage.html';
    });
    console.log(sessionStorage.getItem('state'));
});

$(document).on('click', '.track', event => {
    let id = event.currentTarget.id;
    console.log('clicking'); 
    console.log(id);
    addtrack(id);
});

search = () => {
    console.log();
    $('#results').empty();
    let textvalue = $('#searchbar').val();
    let url;
    //let url = 'http://localhost/web/API/track';
    (textvalue == '') ? url = '../API/track' : 
    url = `../API/track/name/${textvalue}`;
    console.log(url);
    $.get(url, (data) =>{
        console.log(data);
        data.forEach(track => {
        let htmltext = '<div class="track" id="'+ track.TrackId +'"><p class="column"><b>' + track.Name + '</b></p><p class="column">Composer: ' + track.Composer + '</p><p class="column">Length(S): ' + track.Milliseconds/1000 + '</p><p class="column">Price: ' + track.UnitPrice + '</p><button class="btn column" width="10%">Purchase</button></div>'
        $('#results').append(htmltext);
        });
    });
}

addtrack = trackId =>{
    console.log(trackId)
    //let url = `http://localhost/web/API/track/${trackId}`;
    let url = `../API/track/${trackId}`;
    let quantity = 1;
    let unitPrice;
    let postData;
    let state = sessionStorage.getItem('state');

    $.get(url, (data) =>{
        console.log('id' + data[0].UnitPrice);
        unitPrice = data[0].UnitPrice;
        postData = JSON.stringify({quantity, unitPrice, trackId, state});
        console.log(postData);
        //let url = 'http://localhost/web/API/cart';
        $.post('../API/cart', postData)
        .done((result) =>{
            console.log(result)
        });
    });


}
