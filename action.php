<?php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/util.php';

$db = new Database();
$util = new Util();

ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Strict'
]);

session_start();

function getValidUserData($util)
{
    $fields = ['fname', 'lname', 'email', 'phone'];
    $data = [];

    foreach ($fields as $field) {
        if (!isset($_POST[$field]) || !is_string($_POST[$field])) {
            throw new InvalidArgumentException('All fields are required.');
        }

        $data[$field] = $util->testInput($_POST[$field]);
    }

    if (!$util->isValidName($data['fname'])) {
        throw new InvalidArgumentException('Please enter a valid first name.');
    }

    if (!$util->isValidName($data['lname'])) {
        throw new InvalidArgumentException('Please enter a valid last name.');
    }

    if (!$util->isValidEmail($data['email'])) {
        throw new InvalidArgumentException('Please enter a valid email address.');
    }

    if (!$util->isValidPhone($data['phone'])) {
        throw new InvalidArgumentException('Phone number must contain exactly 10 digits.');
    }

    return $data;
}

function getValidId($value)
{
    $id = filter_var($value, FILTER_VALIDATE_INT);

    if ($id === false || $id < 1) {
        throw new InvalidArgumentException('Invalid user ID.');
    }

    return $id;
}

function escapeOutput($value)
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

try {

    // Verify CSRF token for every POST request.
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $submittedToken = $_POST['csrf_token'] ?? '';

        if (
            !is_string($submittedToken) ||
            empty($_SESSION['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'], $submittedToken)
        ) {
            http_response_code(403);
            exit('Invalid or missing security token.');
        }
    }

    // Read all users.
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['read'])) {

        $users = $db->read();
        $output = '';

        if ($users) {
            foreach ($users as $row) {
                $id = escapeOutput($row['id']);
                $fname = escapeOutput($row['first_name']);
                $lname = escapeOutput($row['last_name']);
                $email = escapeOutput($row['email']);
                $phone = escapeOutput($row['phone']);
                $time = escapeOutput($row['timestamp'] ?? '');

                $output .= '<tr>
                    <td>' . $id . '</td>
                    <td>' . $fname . '</td>
                    <td>' . $lname . '</td>
                    <td>' . $email . '</td>
                    <td>' . $phone . '</td>
                    <td>' . $time . '</td>
                    <td>
                        <a href="#" id="' . $id . '" class="btn btn-success editlink">Edit</a>
                        <a href="#" id="' . $id . '" class="btn btn-warning deletelink">Delete</a>
                    </td>
                </tr>';
            }
        } else {
            $output = '<tr><td colspan="7">No users found in the Database</td></tr>';
        }

        echo $output;
        exit;
    }

    // Read one user for editing.
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['edit'])) {

        header('Content-Type: application/json; charset=UTF-8');

        $id = getValidId($_GET['id'] ?? null);
        $user = $db->readOne($id);

        if (!$user) {
            http_response_code(404);
            echo json_encode(['error' => 'User not found.']);
            exit;
        }

        echo json_encode($user);
        exit;
    }

    // All remaining operations must use POST.
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        header('Allow: GET, POST');
        exit('Method not allowed.');
    }

    // Add user.
    if (isset($_POST['add'])) {

        $data = getValidUserData($util);

        $db->insert(
            $data['fname'],
            $data['lname'],
            $data['email'],
            $data['phone']
        );

        echo $util->showMessage('success', 'User inserted successfully!');
        exit;
    }

    // Update user.
    if (isset($_POST['update'])) {

        $id = getValidId($_POST['id'] ?? null);
        $data = getValidUserData($util);

        if (!$db->readOne($id)) {
            http_response_code(404);
            echo $util->showMessage('warning', 'User not found.');
            exit;
        }

        $db->update(
            $id,
            $data['fname'],
            $data['lname'],
            $data['email'],
            $data['phone']
        );

        echo $util->showMessage('success', 'User updated successfully!');
        exit;
    }

    // Delete user using POST only.
    if (isset($_POST['delete'])) {

        $id = getValidId($_POST['id'] ?? null);

        if (!$db->readOne($id)) {
            http_response_code(404);
            echo $util->showMessage('warning', 'User not found.');
            exit;
        }

        $db->delete($id);

        echo $util->showMessage('danger', 'User deleted successfully!');
        exit;
    }

    http_response_code(400);
    echo $util->showMessage('warning', 'Invalid request.');

} catch (InvalidArgumentException $e) {

    http_response_code(400);
    echo $util->showMessage('warning', $e->getMessage());

} catch (Throwable $e) {

    error_log('[PHP CRUD] Request failed: ' . $e->getMessage());

    http_response_code(500);
    echo $util->showMessage('warning', 'Something went wrong. Please try again later.');
}
