<?php


namespace App\Console\Commands;


use App\Models\User;
use Exception;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Collection;
use phpDocumentor\Reflection\Types\Array_;
use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;
use Ratchet\WebSocket\MessageComponentInterface;
use Ratchet\WebSocket\WsConnection;
use Auth;

class WebSocketMessageComponent implements MessageComponentInterface
{
    private $connections;
    private $lastMsg;

    public function __construct()
    {
        $this->connections = new Collection();
        $this->lastMsg = [];
    }

    /**
     * When a new connection is opened it will be passed to this method
     * @param ConnectionInterface $conn The socket/connectionmuted ban that just connected to your application
     * @throws Exception
     */
    function onOpen(ConnectionInterface $conn)
    {
        parse_str($conn->httpRequest->getUri()->getQuery(), $params);

        if (empty($params['token'])
            || !$user = User::where(['remember_token' => $params['token']])->first()) {
            $conn->close();
        }
        // запоминаем пользователя в обьекте подключения
        $conn->user = $user;

        if ($conn->user->ban) {
            $conn->send(json_encode(['action' => 'alert', 'payload' => 'Вы забанены']));
            $conn->send(json_encode(['action' => 'ban', '']));
            $conn->close();
            Auth::logout();
        }

        $this->connections->add($conn);

        $conn->send($conn->user);

        $users = array();
        foreach ($this->connections as $connection) {
            if (!in_array($connection->user->name, $users))
                array_push($users, $connection->user->name);
        }

        foreach ($this->connections as $connection) {
            /** @var $connection ConnectionInterface */
            $connection->send(json_encode(['action' => 'say', 'payload' => $conn->user->name . ' connected']));
            $connection->send(json_encode(['action' => 'users', 'payload' => $users, 'user' => $connection->user]));
        }
    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not result in an error if it has already been closed.
     * @param ConnectionInterface $conn The socket/connection that is closing/closed
     * @throws \Exception
     */
    function onClose(ConnectionInterface $conn)
    {
        $this->connections->forget($this->connections->search($conn));


        $users = array();
        foreach ($this->connections as $connection) {
            if (!in_array($connection->user->name, $users))
                array_push($users, $connection->user->name);
        };

        foreach ($this->connections as $connection) {
            $connection->send(json_encode(['action' => 'say', 'payload' => $conn->user->name . ' disconnected']));
            $connection->send(json_encode(['action' => 'users', 'payload' => $users, 'user' => $connection->user]));
        }
    }

    /**
     * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown,
     * the Exception is sent back down the stack, handled by the Server and bubbled back up the application through this method
     * @param ConnectionInterface $conn
     * @param \Exception $e
     * @throws \Exception
     */
    function onError(ConnectionInterface $conn, Exception $e)
    {
        foreach ($this->connections as $connection) {
            $connection->send(json_encode(['action' => 'say', 'payload' => $e]));
        }
    }

    /**
     * @param ConnectionInterface $conn
     * @param MessageInterface $msg
     */
    public function onMessage(ConnectionInterface $conn, MessageInterface $msg)
    {
        $data = json_decode($msg->getPayload());
        if ($conn->user->ban || $conn->user->mute) {
            return;
        }
        switch ($data->action) {
            case 'say':
                if (strlen($data->payload) > 200) {
                    $conn->send(json_encode(['action' => 'alert', 'payload' => 'не более 200 символов в сообщении']));
                    return;
                }

                //TODO проверка 15 секунд

                var_dump($this->lastMsg[$conn->user]);
                //var_dump($this->lastMsg[$conn->user->id] - microtime());

                foreach ($this->connections as $connection) {
                    $connection->send(json_encode(['action' => 'say', 'payload' => $conn->user->name . ' : ']));
                    $connection->send($msg->getPayload());
                    $this->lastMsg[$connection->user->id] = microtime();
                }
                break;
            case 'mute':
                $user = User::where(['name' => $data->payload])->first();
                if ($user->admin) {
                    return;
                }

                $user->mute = !$user->mute;
                $user->save();

                foreach ($this->connections as $connection) {
                    if ($user->id === $connection->user->id) {
                        $connection->user = $user;
                        if ($user->mute) {
                            $connection->send(json_encode(['action' => 'alert', 'payload' => 'Вам запрещенно отправлять сообщения']));
                        } else {
                            $connection->send(json_encode(['action' => 'alert', 'payload' => 'Вам разрешенно отправлять сообщения']));
                        }
                    }
                }

                foreach ($this->connections as $connection) {
                    if ($user->mute) {
                        $connection->send(json_encode(['action' => 'say', 'payload' => 'muted ' . $user->name]));
                    } else {
                        $connection->send(json_encode(['action' => 'say', 'payload' => 'unmuted ' . $user->name]));
                    }
                }
                break;
            case 'ban':
                $user = User::where(['name' => $data->payload])->first();
                if ($user->admin) {
                    return;
                }

                $user->ban = !$user->ban;
                $user->save();

                foreach ($this->connections as $connection) {
                    if ($user->id === $connection->user->id) {
                        $connection->send(json_encode(['action' => 'alert', 'payload' => 'Вы забанены']));
                        $connection->send(json_encode(['action' => 'ban', '']));
                        $connection->close();
                    }
                }

                foreach ($this->connections as $connection) {
                    if ($user->ban) {
                        $connection->send(json_encode(['action' => 'say', 'payload' => 'baned ' . $user->name]));
                    } else {
                        $connection->send(json_encode(['action' => 'say', 'payload' => 'unbaned ' . $user->name]));
                    }
                }
                break;
        }

    }
}