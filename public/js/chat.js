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
        switch (data.action) {
            case "say":
                console.log(data);
                $('.chat .panel-body').append(`
                <div class="chat row msg_container base_receive">
                    <div class="chat col-md-10 col-xs-10">
                        <div class="chat messages msg_receive">
                            <p style="color: rgb(${data.color})">${data.payload}</p>
                        </div>
                    </div>
                </div>
                `);
                $(".msg_container_base").scrollTop(9999);
                break;
            case "users":
                if (!data.user.admin) {
                    console.log(data);
                    $('.user').remove();
                    for (let i = 0; i < Object.keys(data.payload).length / 2; i++) {
                        let bgMute = '';
                        if (data.payload[i].mute) {
                            bgMute = 'rgb(245,91,91)';
                        }
                        $('tbody').append(`<tr class="user ${data.payload[i].name}">
                            <td class="user ${data.payload[i].name}" style="color: rgb(${data.payload[data.payload[i]]}); background: ${bgMute}" border="1px solid ">${data.payload[i].name}</td>
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
                        let bgMute = '';
                        let bgBan = '';
                        if (data.payload[i].mute) {
                            bgMute = 'rgb(245,91,91)';
                        }
                        if (data.payload[i].ban) {
                            bgBan = 'rgb(245,91,91)';
                        }
                        $('tbody').append(`<tr class="user ${data.payload[i].name}">
                        <td class="user muted ${data.payload[i].name}" style="color: rgb(0,0,0); background: ${bgMute}" onclick="muted(this)" alt="${data.payload[i].name}">${data.payload[i].name} mute</td>
                        <td class="user baned ${data.payload[i].name}" style="color: rgb(0,0,0); background: ${bgBan}" onclick="banned(this)" alt="${data.payload[i].name}">${data.payload[i].name} ban</td></tr>`);
                    }
                    for (let i = 0; i < Object.keys(data.users).length / 2; i++) {
                        let bgMute = '#16c1164f';
                        let bgBan = '#16c1164f';
                        if (data.users[i].mute) {
                            bgMute = 'rgb(245,91,91)';
                        }
                        if (data.users[i].ban) {
                            bgBan = 'rgb(245,91,91)';
                        }
                        $(`td.${data.users[i].name}`).css({
                            'color': `rgb(${data.users[data.users[i].name]})`,
                            'border': '1px solid'
                        });
                        $(`td.muted.${data.users[i].name}`).css({
                            'background': bgMute
                        });
                        $(`td.baned.${data.users[i].name}`).css({
                            'background': bgBan
                        });
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