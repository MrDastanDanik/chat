let conn;

document.addEventListener('DOMContentLoaded', function () {
    conn = new WebSocket(`ws://localhost:8090?token=${USER_TOKEN}`);
    chat();
});

function chat() {
    $('.hid').css('display', 'none');

    $('.in').on('click', function () {
        $('.hid').css('display', '');
        $('.in').css('display', 'none');
        let date = `${new Date().getHours()}:${new Date().getMinutes()}`;

        conn = new WebSocket(`ws://localhost:8090?token=${USER_TOKEN}`);

        conn.onopen = function (e) {
            console.log("Connection established!");
            conn.send(JSON.stringify({action: "connected"}));
        };

        conn.onclose = function (e) {
            conn.send(JSON.stringify({action: "disconnected"}));
            if (event.wasClean) {
                console.log('Соединение закрыто');
            } else {
                console.log('Обрыв соединения');
            }
        };

        $('.btn-primary').on('click', function () {
            let message = $(".type_msg").val();
            conn.send(JSON.stringify({action: "say", payload: message}));
        });

        $('.out').on('click', function () {
            conn.close();
            $('.hid').css('display', 'none');
            $('.in').css('display', '');
        });

        conn.onmessage = function (e) {
            let data = JSON.parse(e.data);
            console.log(data);
            switch (data.action) {
                case "say":
                    $('.msg').append(`<div class="incoming_msg">${data.payload}</div>`);
                    $(".type_msg").val('');
                    break;
                case "connected":
                    $('.online').append(`<input type="button" class="btn user" onclick="muted(this)" value=${data.payload}>`);
                    break;
            }
        };
    });
}

function muted(elem) {
    conn.send(JSON.stringify({action: "mute", payload: elem.value}));
}