<?php

declare(strict_types=1);

function saveHeader(DatabaseUtility $db, array $data): array
{
    $title = trim($data['site_title'] ?? '');
    $description = trim($data['site_description'] ?? '');

    if ($title === '') {
        return ['success' => false, 'message' => 'Page title is required'];
    }

    $db->begin();

    try {
        $db->update(
            'settings',
            ['setting_value' => $title],
            'setting_key = :key',
            ['key' => 'site_title']
        );

        $db->update(
            'settings',
            ['setting_value' => $description],
            'setting_key = :key',
            ['key' => 'site_description']
        );

        $db->commit();

        return ['success' => true, 'message' => 'Header updated successfully'];

    } catch (Throwable $e) {
        $db->rollback(); 
        throw $e;
    }
}

function getHeader(DatabaseUtility $db): array
{
    return [
        'site_title' => $db->getSetting('site_title'),
        'site_description' => $db->getSetting('site_description')
    ];
}

function getSettings(DatabaseUtility $db): array
{
    return $db->fetch(
        "SELECT *
         FROM settings
         WHERE id = 1"
    ) ?? [
        'insurance_enabled'       => 0,
        'max_topics_per_category' => 10,
        'slider_dots_count'       => 3,
        'site_title'              => '',
        'site_description'        => ''
    ];
}
function saveSettings(DatabaseUtility $db, array $data): array
{
    $payload = [
        'insurance_enabled'       => !empty($data['insurance_enabled']) ? 1 : 0,
        'max_topics_per_category' => (int) ($data['max_topics_per_category'] ?? 10),
        'slider_dots_count'       => (int) ($data['slider_dots_count'] ?? 3)
    ];

    try {
        $db->begin();

        $exists = $db->fetch(
            "SELECT id FROM settings WHERE id = 1"
        );

        if ($exists) {
            $db->update(
                'settings',
                $payload,
                'id = :id',
                ['id' => 1]
            );
        } else {
            $db->insert(
                'settings',
                array_merge(['id' => 1], $payload)
            );
        }

        $db->commit();

        return [
            'success' => true,
            'message' => 'Settings saved successfully'
        ];

    } catch (Throwable $e) {
        $db->rollback();

        return [
            'success' => false,
            'message' => 'Failed to save settings'
        ];
    }
}
