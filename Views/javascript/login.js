$(document).ready(() =>{
    $('#login').click(() =>{
        login();
    });
});

login = () =>{
    let Email = $('#Email').val();
    let Password = $('#Password').val();
    //let url = 'http://localhost/web/API/login';
    let url = './API/login';
    data = JSON.stringify({Email, Password});
    console.log(data);

    $.post(url, data)
    .done((result) => {
        sessionStorage.setItem('state', result);
        console.log(sessionStorage.getItem('state'));
        location.href = './views/trackpage.html';
    })
    .fail(() =>{
        $('#response').append('<p>Failed to login</p>');
        console.log('You suck.');
    })
}