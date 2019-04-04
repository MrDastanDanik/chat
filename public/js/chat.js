document.addEventListener('DOMContentLoaded', function () {
    $('.hid').css('display', 'none');
$('.in').on('click', function () {
    $('.hid').css('display','');
    $('.in').css('display', 'none');
        let name = $('.dropdown-toggle').text().replace(/\s+/g, '');
        let date = `${new Date().getHours()}:${new Date().getMinutes()}`;

        let conn = new WebSocket(`ws://localhost:8090?token=${USER_TOKEN}`);

        conn.onopen = function (e) {
            console.log("Connection established!");
            conn.send(JSON.stringify({action: "ping", payload: "pong", user: name, time: date}));
        };

        conn.onclose = function (e) {
            if (event.wasClean) {
                console.log('Соединение закрыто');
            } else {
                console.log('Обрыв соединения');
            }
        };

        $('.btn-primary').on('click', function () {
            let message = $(".type_msg").val();

            conn.send(JSON.stringify({action: "say", payload: message, user: name, time: date}));
        });


        $('.out').on('click', function () {
            conn.send(JSON.stringify({action: "say", payload: "disconnected", user: name, time: date}));
            conn.close();
            $('.hid').css('display', 'none');
            $('.in').css('display', '');
        });


        conn.onmessage = function (e) {
            console.log(e.data);
            let obj = JSON.parse(e.data);

            switch (obj.action) {
                case 'say':
                    $('.msg').append(`<div class="incoming_msg">
              <div class="received_msg">
                <div class="received_withd_msg">
                  <p>${obj.user} : ${obj.payload}</br>${obj.time}</p>
                  </div>
              </div>
            </div>`);
                    $(".type_msg").val('');
                    break;
                case 'ping':
                    $('.online').append(`<p>${obj.user}</p>`);
                    break;
            }
        };
    });
});