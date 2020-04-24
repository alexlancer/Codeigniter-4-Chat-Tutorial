<?php 

namespace App\Libraries;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use App\Models\ConnectionsModel;
use App\Models\UserModel;

class Chat implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later

        // ws://localhost:8080/?access_token=12312313
        $uriQuery = $conn->httpRequest->getUri()->getQuery(); //access_token=12312313
        $uriQueryArr = explode('=',$uriQuery); //$uriQueryArr[1]
        $userModel = new UserModel();
        $conModel = new ConnectionsModel();

        $user = $userModel->find($uriQueryArr[1]);
        $conn->user = $user;
        $this->clients->attach($conn);

        $conModel->where('c_user_id', $user['id'])->delete();
        $conData = [
                'c_user_id' => $user['id'],
                'c_resource_id' => $conn->resourceId,
                'c_name' => $user['firstname']
        ];

        $conModel->save($conData);

        $users = $conModel->findAll();
        $users = ['users' => $users];

        foreach ($this->clients as $client) {
            $client->send(json_encode($users));
        }


        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

        foreach ($this->clients as $client) {
            if ($from !== $client) {

                $data = [
                    'message' => $msg,
                    'author' => $from->user['firstname'],
                    'time' => date('H:i')
                ];
                // The sender is not the receiver, send to each client connected
                $client->send(json_encode($data));

            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        $conModel = new ConnectionsModel();
        $conModel->where('c_resource_id', $conn->resourceId)->delete();
        $users = $conModel->findAll();
        $users = ['users' => $users];
        foreach ($this->clients as $client) {
            $client->send(json_encode($users));
        }

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}