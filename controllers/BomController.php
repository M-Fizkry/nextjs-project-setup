<?php
require_once '../config/config.php';

class BomController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function create($data) {
        try {
            $this->db->beginTransaction();

            // Insert BOM
            $query = "INSERT INTO bom (code, name) VALUES (?, ?)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$data['code'], $data['name']]);
            $bomId = $this->db->lastInsertId();

            // Insert BOM items
            $this->saveBomItems($bomId, $data['materials'], $data['quantities']);

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error creating BOM: " . $e->getMessage());
            return false;
        }
    }

    public function update($data) {
        try {
            $this->db->beginTransaction();

            // Update BOM
            $query = "UPDATE bom SET code = ?, name = ? WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$data['code'], $data['name'], $data['id']]);

            // Delete existing BOM items
            $query = "DELETE FROM bom_items WHERE bom_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$data['id']]);

            // Insert new BOM items
            $this->saveBomItems($data['id'], $data['materials'], $data['quantities']);

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error updating BOM: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id) {
        try {
            $this->db->beginTransaction();

            // Delete BOM items first (foreign key constraint)
            $query = "DELETE FROM bom_items WHERE bom_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$id]);

            // Delete BOM
            $query = "DELETE FROM bom WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$id]);

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error deleting BOM: " . $e->getMessage());
            return false;
        }
    }

    private function saveBomItems($bomId, $materials, $quantities) {
        $query = "INSERT INTO bom_items (bom_id, material_id, quantity) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($query);

        foreach ($materials as $index => $materialId) {
            $quantity = $quantities[$index];
            $stmt->execute([$bomId, $materialId, $quantity]);
        }
    }

    private function validateBomData($data) {
        // Check required fields
        if (empty($data['code']) || empty($data['name'])) {
            return false;
        }

        // Check if materials and quantities are provided and match
        if (!isset($data['materials']) || !isset($data['quantities']) || 
            count($data['materials']) !== count($data['quantities'])) {
            return false;
        }

        // Check if quantities are valid numbers
        foreach ($data['quantities'] as $quantity) {
            if (!is_numeric($quantity) || $quantity <= 0) {
                return false;
            }
        }

        return true;
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new BomController($db);
    
    if (isset($_POST['action'])) {
        $success = false;
        $message = '';

        switch ($_POST['action']) {
            case 'create':
                if ($controller->validateBomData($_POST)) {
                    $success = $controller->create($_POST);
                    $message = $success ? __('success_add') : __('error_occurred');
                } else {
                    $message = __('invalid_input');
                }
                break;
                
            case 'update':
                if ($controller->validateBomData($_POST)) {
                    $success = $controller->update($_POST);
                    $message = $success ? __('success_edit') : __('error_occurred');
                } else {
                    $message = __('invalid_input');
                }
                break;
                
            case 'delete':
                if (isset($_POST['id'])) {
                    $success = $controller->delete($_POST['id']);
                    $message = $success ? __('success_delete') : __('error_occurred');
                }
                break;
        }

        setFlashMessage($success ? 'success' : 'danger', $message);
    }
    
    header('Location: ../views/bom/index.php');
    exit();
}

// Handle direct script access
if (!isset($db)) {
    header('Location: ../index.php');
    exit();
}
?>
