<?php

namespace App;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use PDO;
use PDOException;
use Dotenv\Dotenv;

class App
{
    protected static $db;

    // Load environment variables
    public static function initDB()
    {
        if (self::$db)
            return self::$db;

        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        $host = getenv('DB_HOST');
        $dbname = getenv('DB_NAME');
        $user = getenv('DB_USER');
        $password = getenv('DB_PASS');

        try {
            self::$db = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }

        return self::$db;
    }

    // Endpoint for subscribing to the newsletter
    public static function subscribe(Request $request, Response $response, $args)
    {
        // Get email from request (support both JSON and form data)
        $contentType = $request->getHeaderLine('Content-Type');
        if (strpos($contentType, 'application/json') !== false) {
            // JSON Body
            $data = json_decode($request->getBody()->getContents(), true);
            $email = isset($data['email']) ? $data['email'] : null;
        } else {
            // Form data (application/x-www-form-urlencoded)
            $parsedBody = $request->getParsedBody();
            $email = isset($parsedBody['email']) ? $parsedBody['email'] : null;
        }

        // Validate email
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response->getBody()->write(json_encode(['error' => 'Invalid email']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Check if email already exists
        $db = self::initDB();
        $stmt = $db->prepare("SELECT id FROM subscribers WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $response->getBody()->write(json_encode(['error' => 'Email already subscribed']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(409);
        }

        // Insert email into the database
        $stmt = $db->prepare("INSERT INTO subscribers (email) VALUES (?)");
        $stmt->execute([$email]);

        $response->getBody()->write(json_encode(['message' => 'Subscribed successfully!']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    // Endpoint for unsubscribing from the newsletter
    public static function unsubscribe(Request $request, Response $response, $args)
    {
        // Get email from request (support both JSON and form data)
        $contentType = $request->getHeaderLine('Content-Type');
        if (strpos($contentType, 'application/json') !== false) {
            // JSON Body
            $data = json_decode($request->getBody()->getContents(), true);
            $email = isset($data['email']) ? $data['email'] : null;
        } else {
            // Form data (application/x-www-form-urlencoded)
            $parsedBody = $request->getParsedBody();
            $email = isset($parsedBody['email']) ? $parsedBody['email'] : null;
        }

        // Validate email
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response->getBody()->write(json_encode(['error' => 'Invalid email']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $db = self::initDB();

        // Check if email exists
        $stmt = $db->prepare("SELECT id FROM subscribers WHERE email = ?");
        $stmt->execute([$email]);

        if (!$stmt->fetch()) {
            $response->getBody()->write(json_encode(['error' => 'Email not found']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        // Remove email from the database
        $stmt = $db->prepare("DELETE FROM subscribers WHERE email = ?");
        $stmt->execute([$email]);

        $response->getBody()->write(json_encode(['message' => 'Unsubscribed successfully!']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    // Endpoint to check DB connection status
    public static function dbStatus(Request $request, Response $response, $args)
    {
        try {
            // Attempt a simple query to check DB status
            $db = self::initDB();
            $stmt = $db->query("SELECT 1");

            // If query succeeds, return success message
            $response->getBody()->write(json_encode(['status' => 'Database connection is healthy']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (PDOException $e) {
            // If there is an error connecting to the DB
            $response->getBody()->write(json_encode(['error' => 'Database connection failed', 'details' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
