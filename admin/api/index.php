<?php
declare(strict_types=1);

header('Content-Type: application/json');

require '../../config/DatabaseUtility.php';
require '../../config/csrf.php';

verifyCsrf();

$action = $_POST['action'] ?? '';
$db = DatabaseUtility::getInstance();

try {
    switch ($action) {

        /* ===== Header ===== */
        case 'header.get':
            require 'SettingsController.php';
            echo json_encode([
                'success' => true,
                'data' => getHeader($db)
            ]);
            break;

        case 'header.save':
            require 'SettingsController.php';
            echo json_encode(saveHeader($db, $_POST));
            break;

        /* ===== Categories ===== */
        case 'category.list':
            echo json_encode([
                'success' => true,
                'data' => $db->getllCategories()
            ]);
            break;

        case 'category.save':
            require 'CategoryController.php';
            echo json_encode(saveCategory($db, $_POST));
            break;

        case 'category.delete':
            require 'CategoryController.php';
            echo json_encode(deleteCategory($db, (int)$_POST['id']));
            break;

        /* ===== Topics ===== */
        case 'topic.list':
            require 'TopicController.php';
            echo json_encode([
                'success' => true,
                'data' => getTopics($db)
            ]);
            break;

        case 'topic.save':
            require 'TopicController.php';
            echo json_encode(saveTopic($db, $_POST));
            break;

        case 'topic.delete':
            require 'TopicController.php';
            echo json_encode(deleteTopic($db, (int)$_POST['id']));
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error',
        'debug' => $e->getMessage()
    ]);
}

