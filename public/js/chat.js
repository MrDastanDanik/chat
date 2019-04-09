document.addEventListener('DOMContentLoaded', function () {

    let conn = new WebSocket(`ws://192.168.0.177:8090?token=${USER_TOKEN}`);

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
        console.log(data);
        switch (data.action) {
            case "say":
                $('.msg').append(`<div class="incoming_msg" style="color: rgb(${data.color})">${data.payload}</div>`);
                break;
            case "users":
                if (data.user.admin) {
                    $(`td.user`).css({'color': `rgb(0, 0, 0)`, 'border': '0px solid green', 'background': ''});
                    for (let i = 0; i < Object.keys(data.payload).length / 2; i++) {
                        $(`td.${data.payload[i]}`).css({
                            'color': `rgb(${data.payload[data.payload[i]]})`,
                            'border': '1px solid ',
                            'background': '#16c1164f'
                        });
                    }
                } else {
                    $('.user').remove();
                    console.log(data.payload);
                    for (let i = 0; i < Object.keys(data.payload).length / 2; i++) {
                        $('tbody').append(`<tr class="user ${data.payload[i].name}">
                            <td class="user ${data.payload[i]}" style="color: rgb(${data.payload[data.payload[i]]})" border="1px solid ">${data.payload[i]}</td>
                        </tr>`);
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
            case "allUsers":
                $('.user').remove();
                if (data.user.admin) {
                    for (let i = 0; i <= data.payload.length - 1; i++) {
                        $('tbody').append(`<tr class="user ${data.payload[i].name}">
                            <td class="user ${data.payload[i].name}" style="color: rgb(0,0,0)" onclick="muted(this)" alt="${data.payload[i].name}">${data.payload[i].name} mute</td>
                            <td class="user ${data.payload[i].name}" style="color: rgb(0,0,0)" onclick="banned(this)" alt="${data.payload[i].name}">${data.payload[i].name} ban</td>
                        </tr>`);
                    }
                }
                break;
        }
    };

    window.muted = function (elem) {
        conn.send(JSON.stringify({action: "mute", payload: $(elem).attr('alt')}));
    };

    window.banned = function (elem) {
        conn.send(JSON.stringify({action: "ban", payload: $(elem).attr('alt')}));
    }
});