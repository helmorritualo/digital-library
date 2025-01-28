<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once 'config.php';

$database = new Database();
$db = $database->getConnection();

$action = $_GET['action'] ?? '';
$response = [];

switch ($action) {
     case 'create':
          if ($_SERVER['REQUEST_METHOD'] === 'POST') {
               $data = json_decode(file_get_contents("php://input"));

               $query = "INSERT INTO books (title, author, format, status, file_path) 
                     VALUES (:title, :author, :format, :status, :file_path)";

               $stmt = $db->prepare($query);
               $stmt->bindParam(":title", $data->title);
               $stmt->bindParam(":author", $data->author);
               $stmt->bindParam(":format", $data->format);
               $stmt->bindParam(":status", $data->status);
               $stmt->bindParam(":file_path", $data->file_path);

               if ($stmt->execute()) {
                    $response = ["message" => "Book created successfully"];
               }
          }
          break;

     case 'read':
          $query = "SELECT * FROM books ORDER BY created_at DESC";
          $stmt = $db->prepare($query);
          $stmt->execute();
          $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
          break;

     case 'update':
          if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
               $data = json_decode(file_get_contents("php://input"));

               $query = "UPDATE books 
                     SET title = :title, author = :author, format = :format, 
                         status = :status, file_path = :file_path 
                     WHERE id = :id";

               $stmt = $db->prepare($query);
               $stmt->bindParam(":title", $data->title);
               $stmt->bindParam(":author", $data->author);
               $stmt->bindParam(":format", $data->format);
               $stmt->bindParam(":status", $data->status);
               $stmt->bindParam(":file_path", $data->file_path);
               $stmt->bindParam(":id", $data->id);

               if ($stmt->execute()) {
                    $response = ["message" => "Book updated successfully"];
               }
          }
          break;

     case 'delete':
          if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
               $data = json_decode(file_get_contents("php://input"));

               $query = "DELETE FROM books WHERE id = :id";
               $stmt = $db->prepare($query);
               $stmt->bindParam(":id", $data->id);

               if ($stmt->execute()) {
                    $response = ["message" => "Book deleted successfully"];
               }
          }
          break;
}

echo json_encode($response);