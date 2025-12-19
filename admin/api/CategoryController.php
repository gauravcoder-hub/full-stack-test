<?php

function saveCategory(DatabaseUtility $db, array $data): array
{
    if (empty($data['name'])) {
        return ['success' => false, 'message' => 'Category name required'];
    }

    $iconUrl = null;

    if (isset($_FILES['icon']) && $_FILES['icon']['error'] === UPLOAD_ERR_OK) {
        if ($_FILES['icon']['size'] > 2 * 1024 * 1024) {
            return ['success' => false, 'message' => 'Icon size must be under 2MB'];
        }

  $allowedMime = [
    'image/jpeg'    => 'jpg',
    'image/png'     => 'png',
    'image/webp'    => 'webp',
    'image/svg+xml' => 'svg'
];

        $mimeType = mime_content_type($_FILES['icon']['tmp_name']);
        if (!isset($allowedMime[$mimeType])) {
            return ['success' => false, 'message' => 'Invalid image format'];
        }

        $uploadDir = __DIR__ . '/../../Assets/images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = sprintf(
            'cat_%s_%s.%s',
            date('YmdHis'),
            random_int(1000, 9999),
            $allowedMime[$mimeType]
        );

        $target = $uploadDir . $fileName;

        if (!move_uploaded_file($_FILES['icon']['tmp_name'], $target)) {
            return ['success' => false, 'message' => 'Icon upload failed'];
        }

        $iconUrl = 'Assets/images/' . $fileName;
    }

    /* ========= BASE PAYLOAD ========= */
    $payload = [
        'name'          => trim($data['name']),
        'description'   => trim($data['description'] ?? ''),
        'display_order' => (int)($data['display_order'] ?? 1),
        'is_active'     => !empty($data['is_active']) ? 1 : 0
    ];

    if ($iconUrl !== null) {
        $payload['icon_url'] = $iconUrl;
    }
    if (!empty($data['id'])) {
        $id = (int)$data['id'];

        if ($iconUrl) {
            $old = $db->fetch(
                'SELECT icon_url FROM categories WHERE id = :id',
                ['id' => $id]
            );

            if (!empty($old['icon_url'])) {
                $oldFile = __DIR__ . '/../../' . $old['icon_url'];
                if (is_file($oldFile)) {
                    unlink($oldFile);
                }
            }
        }

        $db->update('categories', $payload, 'id = :id', ['id' => $id]);

        return ['success' => true, 'message' => 'Category updated'];
    }

    $db->insert('categories', $payload);

    return ['success' => true, 'message' => 'Category created'];
}

function deleteCategory(DatabaseUtility $db, int $id): array
{
    if ($id <= 0) {
        return ['success' => false, 'message' => 'Invalid ID'];
    }

    $db->delete('categories', 'id = :id', ['id' => $id]);
    return ['success' => true, 'message' => 'Category deleted'];
}
