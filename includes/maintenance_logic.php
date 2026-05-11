<?php
// includes/maintenance_logic.php

/**
 * Handle maintenance photo upload
 *
 * @param array|null $file The $_FILES['photo'] array
 * @param string $uploadDir The base upload directory
 * @param callable $moveFileFunc Function to use for moving the file (allows mocking in tests)
 * @return array ['success' => bool, 'path' => string|null, 'error' => string|null]
 */
function handle_maintenance_upload($file, $uploadDir = '../../uploads/maintenance/', $moveFileFunc = 'move_uploaded_file') {
    if (isset($file) && isset($file['error']) && $file['error'] === UPLOAD_ERR_OK) {
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $fileName = time() . '_' . basename($file['name']);
        $targetFile = rtrim($uploadDir, '/') . '/' . $fileName;

        if (call_user_func($moveFileFunc, $file['tmp_name'], $targetFile)) {
            return ['success' => true, 'path' => 'uploads/maintenance/' . $fileName, 'error' => null];
        } else {
            return ['success' => false, 'path' => null, 'error' => 'Failed to upload photo.'];
        }
    }
    return ['success' => false, 'path' => null, 'error' => null]; // Not an error if no file was uploaded
}
