$(document).ready(() =>{
    $('#searchbtn').click(() =>{
        search();
    });
});

search = () => {
    console.log();
    $('#results').empty();
    let textvalue = $('#searchbar').val();
    let url;
    //let url = 'http://localhost/web/API/artist';
    (textvalue == '') ? url = '../API/artist' : 
    url = `../API/artist/name/${textvalue}`;
    console.log(url);
    $.get(url, (data) =>{
        console.log(data);
        data.forEach(artist => {
        let htmltext = '<div class="item" id="'+ artist.ArtistId +'"><p class="column"><b>' + artist.Name + '</b></p><p class="column">Id:' + artist.ArtistId + '</p></div>';
        $('#results').append(htmltext);
        });
    });
}