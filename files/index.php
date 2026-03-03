<?php
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: ../login.php");
    exit;
}

require '../vendor/autoload.php';
require '../model/database.php';
require '../model/users_db.php';
require '../model/files_db.php';

use Google\Cloud\Storage\StorageClient;

$user_id = (int) $_SESSION['userId'];
$users_db = new UsersDB;
$files_db = new FilesDB;
$user_info = $users_db->get_user_info($user_id);
$is_superuser = !empty($user_info['superuser']) && (int) $user_info['superuser'] === 1;

const GCS_BUCKET_NAME = 'crm-customer-storage';
const FILES_PROVIDER = 'gcp';

function resolve_gcs_key_file_path() {
    $candidates = [
        __DIR__ . '/../.credentials/gcs_upload_signer.json',
        __DIR__ . '/../credentials/gcs_upload_signer.json',
        '/etc/gcp/gcs_upload_signer.json'
    ];
    foreach ($candidates as $path) {
        if (is_readable($path)) {
            return $path;
        }
    }
    return '/etc/gcp/gcs_upload_signer.json';
}

function build_storage_client() {
    return new StorageClient([
        'keyFilePath' => resolve_gcs_key_file_path()
    ]);
}

function build_object_name($user_id, $original_name) {
    $safe_original_name = preg_replace('/[^A-Za-z0-9._-]/', '_', (string) $original_name);
    if ($safe_original_name === null || $safe_original_name === '') {
        $safe_original_name = 'file';
    }
    $safe_original_name = substr($safe_original_name, 0, 180);
    $random_suffix = bin2hex(random_bytes(6));
    return sprintf(
        'user_%d/%s_%s_%s',
        (int) $user_id,
        date('YmdHis'),
        $random_suffix,
        $safe_original_name
    );
}

function format_content_type($content_type) {
    if ($content_type === null || $content_type === '') {
        return 'application/octet-stream';
    }
    return substr((string) $content_type, 0, 32);
}

function build_file_name($original_name) {
    $file_name = trim((string) $original_name);
    if ($file_name === '') {
        return 'file';
    }
    // Strip control characters but keep user-provided name.
    $file_name = preg_replace('/[\x00-\x1F\x7F]/', '', $file_name);
    if ($file_name === null || $file_name === '') {
        return 'file';
    }
    return substr($file_name, 0, 1012);
}

$message_query = '';
$message = filter_input(INPUT_GET, 'message');
if ($message !== null && $message !== '') {
    $message_query = '&message=' . urlencode($message);
}

$action = filter_input(INPUT_POST, 'action');
if ($action === null) {
    $action = filter_input(INPUT_GET, 'action');
    if ($action === null) {
        $action = 'list_files';
    }
}

if ($action === 'upload_file') {
    $upload_user_id = $user_id;
    if ($is_superuser) {
        $requested_upload_user_id = filter_input(INPUT_POST, 'upload_user_id', FILTER_VALIDATE_INT);
        if ($requested_upload_user_id !== null && $requested_upload_user_id !== false && $requested_upload_user_id > 0) {
            $target_user_info = $users_db->get_user_info((int) $requested_upload_user_id);
            if ($target_user_info) {
                $upload_user_id = (int) $requested_upload_user_id;
            } else {
                $_SESSION['files_error'] = 'Selected upload user was not found.';
                header('Location: .?action=list_files');
                exit;
            }
        }
    }

    if (!isset($_FILES['upload_file']) || !is_array($_FILES['upload_file'])) {
        $_SESSION['files_error'] = 'No file was provided for upload.';
        header('Location: .?action=list_files');
        exit;
    }

    $upload = $_FILES['upload_file'];
    if (($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        $_SESSION['files_error'] = 'Upload failed. Please try again.';
        header('Location: .?action=list_files');
        exit;
    }

    $tmp_name = $upload['tmp_name'] ?? '';
    if ($tmp_name === '' || !is_uploaded_file($tmp_name)) {
        $_SESSION['files_error'] = 'Invalid upload payload.';
        header('Location: .?action=list_files');
        exit;
    }

    try {
        $storage = build_storage_client();
        $bucket = $storage->bucket(GCS_BUCKET_NAME);

        $object_name = build_object_name($upload_user_id, $upload['name'] ?? 'file');
        $file_name = build_file_name($upload['name'] ?? 'file');
        $content_type = format_content_type($upload['type'] ?? 'application/octet-stream');
        $size_bytes = isset($upload['size']) ? (int) $upload['size'] : 0;

        $stream = fopen($tmp_name, 'r');
        if ($stream === false) {
            throw new RuntimeException('Unable to open uploaded file stream.');
        }

        $bucket->upload($stream, [
            'name' => $object_name,
            'metadata' => [
                'contentType' => $content_type
            ]
        ]);
        if (is_resource($stream)) {
            fclose($stream);
        }

        $files_db->create_file(
            substr(FILES_PROVIDER, 0, 256),
            substr(GCS_BUCKET_NAME, 0, 256),
            substr($object_name, 0, 256),
            $file_name,
            $content_type,
            $size_bytes,
            $upload_user_id
        );

        $_SESSION['files_message'] = 'File uploaded successfully.';
    } catch (Throwable $e) {
        $_SESSION['files_error'] = 'Upload failed: ' . $e->getMessage();
    }

    header('Location: .?action=list_files');
    exit;
} else if ($action === 'download_file') {
    $file_id = filter_input(INPUT_GET, 'file_id', FILTER_VALIDATE_INT);
    if ($file_id === null || $file_id === false) {
        $_SESSION['files_error'] = 'Invalid file id.';
        header('Location: .?action=list_files');
        exit;
    }

    $file = $is_superuser ? $files_db->get_file($file_id) : $files_db->get_file_for_user($file_id, $user_id);
    if (!$file) {
        include '../view/record_access_error.php';
        exit;
    }

    try {
        $storage = build_storage_client();
        $bucket = $storage->bucket($file['Bucket']);
        $object = $bucket->object($file['ObjectName']);
        if (!$object->exists()) {
            $_SESSION['files_error'] = 'File is no longer available in storage.';
            header('Location: .?action=list_files');
            exit;
        }

        $download_name = build_file_name($file['FileName'] ?? basename($file['ObjectName']));
        $encoded_file_name = rawurlencode($download_name);
        $signed_url = $object->signedUrl(
            new DateTimeImmutable('+10 minutes'),
            [
                'version' => 'v4',
                'responseDisposition' => "attachment; filename*=UTF-8''{$encoded_file_name}"
            ]
        );
        header('Location: ' . $signed_url);
        exit;
    } catch (Throwable $e) {
        $_SESSION['files_error'] = 'Unable to generate download link: ' . $e->getMessage();
        header('Location: .?action=list_files');
        exit;
    }
} else if ($action === 'delete_file') {
    $file_id = filter_input(INPUT_POST, 'file_id', FILTER_VALIDATE_INT);
    if ($file_id === null || $file_id === false) {
        $_SESSION['files_error'] = 'Invalid file id.';
        header('Location: .?action=list_files' . $message_query);
        exit;
    }

    $deleted = $is_superuser
        ? $files_db->delete_file($file_id)
        : $files_db->delete_file_for_user($file_id, $user_id);

    if ($deleted) {
        $_SESSION['files_message'] = 'File deleted successfully.';
    } else {
        $_SESSION['files_error'] = 'Unable to delete that file.';
    }
    header('Location: .?action=list_files' . $message_query);
    exit;
} else if ($action === 'list_files') {
    $upload_target_users = [];
    $selected_upload_user_id = $user_id;
    $created_by_filter = null;
    if ($is_superuser) {
        $upload_target_users = $users_db->get_all_users();
        usort($upload_target_users, function ($a, $b) {
            return strcasecmp((string) ($a['email'] ?? ''), (string) ($b['email'] ?? ''));
        });

        $requested_created_by = filter_input(INPUT_GET, 'created_by', FILTER_VALIDATE_INT);
        if ($requested_created_by !== null && $requested_created_by !== false && $requested_created_by > 0) {
            $created_by_filter = (int) $requested_created_by;
        }
    }

    if ($is_superuser) {
        $files = $files_db->get_files_all($created_by_filter);
        $upload_users = $files_db->get_upload_users();
    } else {
        $files = $files_db->get_files_for_user($user_id);
        $upload_users = [];
    }

    include 'list_files.php';
    exit;
}

header('Location: .?action=list_files');
exit;
?>
