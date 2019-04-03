document.addEventListener('DOMContentLoaded', function() {
    let conn = new WebSocket(`ws://192.168.10.25:8090?token=${USER_TOKEN}`);

    conn.onopen = function (e) {
        console.log("Connection established!");
    };
    conn.onmessage = function (e) {
        console.log(e.data);
    };
});