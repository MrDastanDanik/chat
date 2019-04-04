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
    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not result in an error if it has already been closed.
     * @param ConnectionInterface $conn The socket/connection that is closing/closed
     * @throws \Exception
     */
    function onClose(ConnectionInterface $conn)
    {

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
        // TODO: Implement onError() method.
        echo "An error has occurred with user";
        $conn->close();
    }

    public function onMessage(ConnectionInterface $conn, MessageInterface $msg)
    {
        $data = $msg->getPayload();
        switch ($data->action){
            case 'say':
                foreach ($this->connections as $connections) {
                    $connections->send($msg->getPayload());

                }
                break;
            case 'ban':
                if (!$conn->user->isAdmin){
                    return false;
                }



                break;
        }



    }
}