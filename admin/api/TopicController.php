<?php

declare(strict_types=1);

function getTopics(DatabaseUtility $db): array
{
    return $db->fetchAll(
        "SELECT * 
         FROM topics 
         ORDER BY display_order ASC, id ASC"
    );
}

function saveTopic(DatabaseUtility $db, array $data): array
{
    if (empty(trim($data['title'] ?? ''))) {
        return ['success' => false, 'message' => 'Topic title is required'];
    }

    if (empty($data['category_id'])) {
        return ['success' => false, 'message' => 'Category is required'];
    }

    $slug = strtolower(trim($data['slug'] ?? ''));
    if ($slug === '') {
        $slug = preg_replace('/[^a-z0-9]+/i', '-', $data['title']);
        $slug = trim($slug, '-');
    }

    $payload = [
        'category_id'   => (int) $data['category_id'],
        'title'         => trim($data['title']),
        'slug'          => $slug,
        'description'   => trim($data['description'] ?? ''),
        'content'       => trim($data['content'] ?? ''),
        'image_url'     => trim($data['image_url'] ?? ''),
        'display_order' => (int) ($data['display_order'] ?? 1),
        'is_active'     => !empty($data['is_active']) ? 1 : 0
    ];

    try {
        $db->begin();

        if (!empty($data['id'])) {
            $db->update('topics', $payload, 'id = :id', [
                'id' => (int) $data['id']
            ]);
        } else {
            $db->insert('topics', $payload);
        }

        $db->commit();

        return ['success' => true, 'message' => 'Topic saved successfully'];

    } catch (Throwable $e) {
        $db->rollback();

        error_log($e->getMessage()); 

        return [
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ];
    }
}

function deleteTopic(DatabaseUtility $db, int $id): array
{
    if ($id <= 0) {
        return [
            'success' => false,
            'message' => 'Invalid topic ID'
        ];
    }

    try {
        $db->begin();



        $db->delete(
            'topics',
            'id = :id',
            ['id' => $id]
        );

        $db->commit();

        return [
            'success' => true,
            'message' => 'Topic and related data deleted successfully'
        ];

    } catch (Throwable $e) {
        $db->rollback();

        return [
            'success' => false,
            'message' => 'Failed to delete topic'
        ];
    }
}
