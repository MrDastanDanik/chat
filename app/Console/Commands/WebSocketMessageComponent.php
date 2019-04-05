<?php


namespace App\Console\Commands;


use App\Models\User;
use Exception;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Collection;
use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;
use Ratchet\WebSocket\MessageComponentInterface;
use Ratchet\WebSocket\WsConnection;

class WebSocketMessageComponent implements MessageComponentInterface
{
    private $connections;

    public function __construct()
    {
        $this->connections = new Collection();
    }

    /**
     * When a new connection is opened it will be passed to this method
     * @param ConnectionInterface $conn The socket/connection that just connected to your application
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

        $this->connections->add($conn);

        $conn->send($conn->user);

        foreach ($this->connections as $connections) {
            $connections->send(json_encode(array('action' => 'connected', 'payload' => $conn->user->name)));
            $connections->send(json_encode(array('action' => 'say', 'payload' => $conn->user->name . ' connected')));
        }
    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not result in an error if it has already been closed.
     * @param ConnectionInterface $conn The socket/connection that is closing/closed
     * @throws \Exception
     */
    function onClose(ConnectionInterface $conn)
    {
        foreach ($this->connections as $connections) {
            $connections->send(json_encode(array('action' => 'disconnected', 'payload' => $conn->user->name)));
            $connections->send(json_encode(array('action' => 'say', 'payload' => $conn->user->name . ' disconnected')));
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

    }

    public function onMessage(ConnectionInterface $conn, MessageInterface $msg)
    {
        $data = json_decode($msg->getPayload());
        var_dump($conn->user->mute);
        if (!$conn->user->mute) {
            switch ($data->action) {
                case 'say':
                    foreach ($this->connections as $connections) {
                        $connections->send(json_encode(array('action' => 'say', 'payload' => $conn->user->name . ' : ')));
                        $connections->send($msg->getPayload());
                    }
                    break;
                case 'mute':
                    $user = User::where(['name' => $data->payload])->first();
                    if (!$user->admin) {

                        if (!$user->mute) {
                            User::where(['name' => $data->payload])->update(['mute' => 1]);
                            /*$user->mute = 1;
                            $user->save();*/
                        } else if ($user->mute) {
                            /*$user->mute = 0;
                            $user->save();*/
                        }

                        var_dump($user->mute);

                        //TODO перезапись на лету

                        /*$key = $user->id;
                        var_dump('key' . $key);
                        User::all()->forget($key);
                        $conn->user = $user;
                        $this->connections->push($conn);*/

                        $conn->send(json_encode(array('action' => 'say', 'payload' => 'muted ' . $user->name . ' ' . $user->mute)));
                    }
                    break;
            }
        }
    }
}