$(document).ready(() =>{
    $('#tcreate').click(() =>{
        createTrack();
    });
    $('#tdelete').click(() =>{
        deleteTrack();
    });

    $('#albcreate').click(() =>{
        createAlbum();
    });
    $('#albdelete').click(() =>{
        deleteAlbum();
    });

    $('#artcreate').click(() =>{
        createArtist();
    });
    $('#artdelete').click(() =>{
        deleteArtist();
    });
});

createTrack = () =>{
    let Name = $('#tname').val();
    let AlbumId = $('#talbum').val();
    let MediaTypeId = $('#tmedia').val();
    let GenreId = $('#tgenre').val();
    let Composer = $('#tcomposer').val();
    let Milliseconds = $('#tmilliseconds').val();
    let Bytes = $('#tbytes').val();
    let UnitPrice= $('#tunitprice').val();
    let state = sessionStorage.getItem('state');

    let data = JSON.stringify({Name, AlbumId, MediaTypeId, GenreId, Composer, Milliseconds, Bytes, UnitPrice, state});
    $('#tcreateresponse').empty();

    let url = '../API/track';
    $.post(url, data)
    .done((result)=>{
        console.log(result);
        $('#tcreateresponse').append("Song has been added.");
    })
    .fail(()=>{
        $('#tcreateresponse').append("Failed to add song.");
    })
}
deleteTrack = () =>{
    let id  = $('#tid').val();
    let url = `../API/track/${id}`
    let state = sessionStorage.getItem('state');
    let data = JSON.stringify({state});
    console.log(data);
    console.log('trying to detete');
    $.ajax({url: url, type:'DELETE', data: data})
    .done((result)=>{
        console.log(result);
        $('#tdeleteresponse').append("Song has been deleted");
    })
    .fail(() => {
        $('#tdeleteresponse').append("Song failed to delete.");
    })
}
createAlbum = () =>{
    let Title = $('#albtitle').val();
    let ArtistId = $('#albumartistId').val();
    let state = sessionStorage.getItem('state');

    let data = JSON.stringify({Title, ArtistId, state});

    $('#albcreateresponse').empty();

    let url = '../API/album';
    $.post(url, data)
    .done((result)=>{
        console.log(result);
        $('#albcreateresponse').append("Album has been added.");
    })
    .fail(()=>{
        $('#albcreateresponse').append("Failed to add album.");
    })
}
deleteAlbum = () =>{
    let id  = $('#albumid').val();
    let url = `../API/album/${id}`
    let state = sessionStorage.getItem('state');

    let data = JSON.stringify({state});

    console.log(data);
    console.log('trying to detete');

    $('#albdeleteresponse').empty();

    $.ajax({url: url, type:'DELETE', data: data})
    .done((result)=>{
        console.log(result);
        $('#albdeleteresponse').append("Album has been deleted");
    })
    .fail(() => {
        $('#albdeleteresponse').append("Album failed to delete.");
    })
}
createArtist = () =>{
    let Name = $('#artname').val();
    let state = sessionStorage.getItem('state');

    let data = JSON.stringify({Name, state});

    $('#artcreateresponse').empty();

    let url = '../API/artist';
    $.post(url, data)
    .done((result)=>{
        console.log(result);
        $('#artcreateresponse').append("Artist has been added.");
    })
    .fail(()=>{
        $('#artcreateresponse').append("Failed to add artist.");
    })
}

deleteArtist = () =>{
    let id = $('#artistid').val();
    let url = `../API/artist/${id}`
    let state = sessionStorage.getItem('state');
    let data = JSON.stringify({state});

    $('#artdeleteresponse').empty();

    console.log(data);
    console.log('trying to detete');

    $.ajax({url: url, type:'DELETE', data: data})
    .done((result)=>{
        console.log(result);
        $('#artdeleteresponse').append("Artist has been deleted");
    })
    .fail(() => {
        $('#artdeleteresponse').append("Artist failed to delete.");
    })
}