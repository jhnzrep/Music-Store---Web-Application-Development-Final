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
    //let url = 'http://localhost/web/API/album';
    (textvalue == '') ? url = '../API/album' : 
    url = `../API/album/name/${textvalue}`;
    console.log(url);
    $.get(url, (data) =>{
        console.log(data);
        data.forEach(album => {
        let htmltext = '<div class="item" id="'+ album.ArtistId +'"><p class="column"><b>' + album.Title + '</b></p><p class="column">Artist:' + album.Name + '<p class="column">Id:' + album.AlbumId + '</p></div>';
        $('#results').append(htmltext);
        });
    });
}