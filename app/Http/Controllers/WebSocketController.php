<?php

namespace App\Http\Controllers;

use App\Models\User;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class WebSocketController extends Controller implements MessageComponentInterface
{
    private $connections = [];

    function onOpen(ConnectionInterface $conn)
    {
        $params = $conn->WebSocket->request->getQuery()->toArray();
        dd($params);
        // если нет токена в запросе при подключении - отключаем пользователя
        if (empty($params['token'])) {
            $conn->close();
        }

        //  ищем пользователя в бд по токену. если нет - отключем
        if (!$user = User::query()->where(['remember_token' => $params['token']])->first()) {
            $conn->close();
        }

        // запоминаем пользователя в обьекте подключения
        $conn->user = $user;

        $this->connections->attach($conn);
    }

    function onClose(ConnectionInterface $conn)
    {
        echo "close";
    }

    function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred with user";
        $conn->close();
    }

    function onMessage(ConnectionInterface $conn, $msg)
    {
        echo $msg;
    }
}