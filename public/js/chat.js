document.addEventListener('DOMContentLoaded', function () {
    let conn = new WebSocket(`ws://localhost:8090?token=${USER_TOKEN}`);

    conn.onopen = function (e) {
        console.log("Connection established!");
    };

    conn.onclose = function (e) {
        if (event.wasClean) {
            console.log('Соединение закрыто');
        } else {
            console.log('Обрыв соединения');
        }
        console.log(e);
    };

    $('.btn-primary').on('click', function () {
        let message = $(".type_msg").val();
        conn.send(JSON.stringify({action: "say", payload: message}));
        $(".type_msg").val('');
    });

    conn.onmessage = function (e) {
        let data = JSON.parse(e.data);
        switch (data.action) {
            case "say":
                $('.msg').append(`<div class="incoming_msg">${data.payload}</div>`);
                break;
            case "users":
                $('.user').remove();

                if (data.user.admin) {
                    for (let i = 0; i <= data.payload.length - 1; i++) {
                        $('.online').append(`<input type="button" class="btn user ${data.payload[i]}" onclick="muted(this)" value=${data.payload[i]}>
                        <img class="user ${data.payload[i]}" src="https://img.icons8.com/color/48/000000/delete-sign.png" onclick="banned(this)" alt=${data.payload[i]}>`);
                    }
                } else {
                    for (let i = 0; i <= data.payload.length - 1; i++) {
                        $('.online').append(`<span class="user">${data.payload[i]}; </span>`);
                    }
                }
                break;
            case "alert":
                alert(data.payload);
                break;
            case "ban":
                event.preventDefault();
                document.getElementById('logout-form').submit();
                break;
        }
    };

    window.muted = function(elem) {
        conn.send(JSON.stringify({action: "mute", payload: elem.value}));
    };

    window.banned = function(elem) {
        conn.send(JSON.stringify({action: "ban", payload: elem.alt}));
    }
});