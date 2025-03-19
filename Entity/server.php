<?php
require '../vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface
{
    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);

        if (isset($data['user_id']) && isset($data['other_user_id'])) {
            require '../övrigt/PDO.php';

            $messageContent = isset($data['message']) ? $data['message'] : '';
            $base64Image = isset($data['message_image']) ? $data['message_image'] : null;

            // Save both message and base64 image in database
            $stmt = $pdo->prepare("
                INSERT INTO dms (message_content, message_image, CreatedDate, user1_id, user2_id, unread_status)
                VALUES (:message, :message_image, NOW(), :user_id, :other_user_id, 1)
            ");
            $stmt->bindParam(':message', $messageContent, PDO::PARAM_STR);
            $stmt->bindParam(':message_image', $base64Image, PDO::PARAM_STR);
            $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
            $stmt->bindParam(':other_user_id', $data['other_user_id'], PDO::PARAM_INT);
            $stmt->execute();

            // Broadcast to all clients
            foreach ($this->clients as $client) {
                $client->send(json_encode([
                    'message' => $messageContent,
                    'message_image' => $base64Image,
                    'user_id' => $data['user_id'],
                    'other_user_id' => $data['other_user_id'],
                    'timestamp' => date('Y-m-d H:i:s')
                ]));
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "Error: " . $e->getMessage() . "\n";
        $conn->close();
    }
}

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat()
        )
    ),
    8080 // WebSocket server will run on port 8080
);

echo "WebSocket server started on ws://localhost:8080\n";
$server->run();