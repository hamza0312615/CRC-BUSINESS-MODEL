<?php
// tests/test_maintenance_upload.php

require_once __DIR__ . '/../includes/maintenance_logic.php';

function test_successful_upload() {
    $file = [
        'name' => 'receipt.jpg',
        'type' => 'image/jpeg',
        'tmp_name' => '/tmp/php1234.tmp',
        'error' => UPLOAD_ERR_OK,
        'size' => 1024
    ];

    // Mock move_uploaded_file to always succeed
    $mockMoveFile = function($from, $to) {
        return true;
    };

    // Use sys_get_temp_dir() to avoid creating actual directories in project root during tests
    $uploadDir = sys_get_temp_dir() . '/test_maintenance_uploads/';

    $result = handle_maintenance_upload($file, $uploadDir, $mockMoveFile);

    if ($result['success'] === true && strpos($result['path'], 'uploads/maintenance/') === 0 && $result['error'] === null) {
        echo "PASS: Successful upload\n";
        return true;
    } else {
        echo "FAIL: Successful upload\n";
        print_r($result);
        return false;
    }
}

function test_failed_upload() {
    $file = [
        'name' => 'receipt.jpg',
        'type' => 'image/jpeg',
        'tmp_name' => '/tmp/php1234.tmp',
        'error' => UPLOAD_ERR_OK,
        'size' => 1024
    ];

    // Mock move_uploaded_file to always fail
    $mockMoveFile = function($from, $to) {
        return false;
    };

    $uploadDir = sys_get_temp_dir() . '/test_maintenance_uploads/';

    $result = handle_maintenance_upload($file, $uploadDir, $mockMoveFile);

    if ($result['success'] === false && $result['path'] === null && $result['error'] === 'Failed to upload photo.') {
        echo "PASS: Failed upload\n";
        return true;
    } else {
        echo "FAIL: Failed upload\n";
        print_r($result);
        return false;
    }
}

function test_no_upload() {
    // Missing file or upload error
    $file = [
        'name' => '',
        'type' => '',
        'tmp_name' => '',
        'error' => UPLOAD_ERR_NO_FILE,
        'size' => 0
    ];

    $mockMoveFile = function($from, $to) {
        return false; // Should not be called
    };

    $uploadDir = sys_get_temp_dir() . '/test_maintenance_uploads/';

    $result = handle_maintenance_upload($file, $uploadDir, $mockMoveFile);

    if ($result['success'] === false && $result['path'] === null && $result['error'] === null) {
        echo "PASS: No file uploaded\n";
        return true;
    } else {
        echo "FAIL: No file uploaded\n";
        print_r($result);
        return false;
    }
}

function test_null_file() {
    // When $_FILES['photo'] is not even set
    $file = null;

    $mockMoveFile = function($from, $to) {
        return false; // Should not be called
    };

    $uploadDir = sys_get_temp_dir() . '/test_maintenance_uploads/';

    $result = handle_maintenance_upload($file, $uploadDir, $mockMoveFile);

    if ($result['success'] === false && $result['path'] === null && $result['error'] === null) {
        echo "PASS: Null file uploaded\n";
        return true;
    } else {
        echo "FAIL: Null file uploaded\n";
        print_r($result);
        return false;
    }
}

echo "Running maintenance upload tests...\n";
$all_passed = true;
$all_passed = test_successful_upload() && $all_passed;
$all_passed = test_failed_upload() && $all_passed;
$all_passed = test_no_upload() && $all_passed;
$all_passed = test_null_file() && $all_passed;

if ($all_passed) {
    echo "All tests passed successfully.\n";
    exit(0);
} else {
    echo "Some tests failed.\n";
    exit(1);
}
