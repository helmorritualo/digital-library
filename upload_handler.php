<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, DELETE");

class FileHandler
{
     private $uploadDir = "uploads/";
     private $uploadedFiles = [];
     private static $allowedTypes = [
          'application/pdf',
          'application/epub+zip',
          'application/x-mobipocket-ebook',
          'audio/mpeg',
          'audio/mp4',
          'audio/x-m4a',
          'audio/ogg',
          'audio/x-wav'
     ];

     public function __construct()
     {
          if (!file_exists($this->uploadDir)) {
               mkdir($this->uploadDir, 0755, true);
          }
          ini_set('memory_limit', '512M');
          ini_set('max_execution_time', 300);
     }

     public function uploadFile($file)
     {
          try {
               if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
                    throw new Exception('Invalid upload attempt', 400);
               }

               if (!$this->isValidFile($file)) {
                    throw new Exception('Invalid file type or size', 415);
               }

               $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
               $fileName = uniqid(mt_rand(), true) . '.' . $extension;
               $targetPath = $this->uploadDir . $fileName;

               if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    chmod($targetPath, 0644);
                    $this->uploadedFiles[] = $targetPath;
                    return $targetPath;
               }

               throw new Exception("File upload failed", 500);
          } catch (Exception $e) {
               error_log("File upload error: " . $e->getMessage());
               throw $e;
          }
     }

     private function isValidFile($file)
     {
          static $maxFileSize = 500 * 1024 * 1024;

          if ($file['size'] > $maxFileSize) {
               return false;
          }

          return in_array($file['type'], self::$allowedTypes, true);
     }

     public function deleteFile($filePath)
     {
          try {
               if (!file_exists($filePath)) {
                    throw new Exception("File not found", 404);
               }

               if (unlink($filePath)) {
                    return true;
               } else {
                    throw new Exception("Failed to delete file", 500);
               }
          } catch (Exception $e) {
               error_log("File deletion error: " . $e->getMessage());
               throw $e;
          }
     }
}

// Handle request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     try {
          $fileHandler = new FileHandler();

          if (!isset($_FILES['file'])) {
               throw new Exception('No file uploaded', 400);
          }

          $filePath = $fileHandler->uploadFile($_FILES['file']);

          header('Content-Type: application/json');
          echo json_encode([
               'success' => true,
               'file_path' => $filePath
          ]);
     } catch (Exception $e) {
          http_response_code($e->getCode() ?: 500);
          echo json_encode([
               'success' => false,
               'error' => $e->getMessage()
          ]);
     }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
     try {
          $data = json_decode(file_get_contents('php://input'));

          if (!isset($data->file_path)) {
               throw new Exception('No file path provided', 400);
          }

          $fileHandler = new FileHandler();
          $result = $fileHandler->deleteFile($data->file_path);

          header('Content-Type: application/json');
          echo json_encode([
               'success' => true
          ]);
     } catch (Exception $e) {
          http_response_code($e->getCode() ?: 500);
          echo json_encode([
               'success' => false,
               'error' => $e->getMessage()
          ]);
     }
}