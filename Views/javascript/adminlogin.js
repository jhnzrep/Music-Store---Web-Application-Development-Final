$(document).ready(() =>{
    $('#login').click(() =>{
        login();
    });
});

login = () =>{
    let Password = $('#Password').val();
    let url = '../API/adminlogin';

    data = JSON.stringify({Password});
    console.log(data);

    $.post(url, data)
    .done((result) => {
        sessionStorage.setItem('state', result);
        console.log(sessionStorage.getItem('state'));
        location.href = 'adminpanel.html';
    })
    .fail(() =>{
        $('#response').append('<p>Failed to login</p>');
        console.log('You suck.');
    });
}