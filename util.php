<?php

class Util
{
    // Trim unnecessary spaces from input.
    public function testInput($data)
    {
        if (!is_string($data)) {
            return '';
        }

        return trim($data);
    }

    // Validate first and last names.
    public function isValidName($name)
    {
        return preg_match(
            "/\A[\p{L}\p{M} .'-]{1,255}\z/u",
            $name
        ) === 1;
    }

    // Validate email address.
    public function isValidEmail($email)
    {
        return strlen($email) <= 255
            && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    // Validate exactly 10 digits.
    public function isValidPhone($phone)
    {
        return preg_match('/\A[0-9]{10}\z/', $phone) === 1;
    }

    // Safely display application messages.
    public function showMessage($type, $message)
    {
        $allowedTypes = ['success', 'warning', 'danger', 'info'];

        if (!in_array($type, $allowedTypes, true)) {
            $type = 'info';
        }

        $message = htmlspecialchars(
            $message,
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );

        return '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">
                    <strong>' . $message . '</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
    }
}
