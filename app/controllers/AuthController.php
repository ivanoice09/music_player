<?php

class AuthController extends BaseController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function register()
    {
        // Check for POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Init data with proper sanitization
            $data = [
                'username' => $this->sanitizeInput($_POST['username'] ?? ''),
                'email' => $this->sanitizeEmail($_POST['email'] ?? ''),
                'password' => trim($_POST['password'] ?? ''),
                'confirm_password' => trim($_POST['confirm_password'] ?? ''),
                'username_err' => '',
                'email_err' => '',
                'password_err' => '',
                'confirm_password_err' => ''
            ];

            // Validate Username
            if (empty($data['username'])) {
                $data['username_err'] = 'Please enter username';
            } elseif (strlen($data['username']) < 3) {
                $data['username_err'] = 'Username must be at least 3 characters';
            } elseif ($this->userModel->findUserByUsername($data['username'])) {
                $data['username_err'] = 'Username is already taken';
            }

            // Validate Email
            if (empty($data['email'])) {
                $data['email_err'] = 'Please enter email';
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $data['email_err'] = 'Please enter a valid email';
            } elseif ($this->userModel->findUserByEmail($data['email'])) {
                $data['email_err'] = 'Email is already taken';
            }

            // Validate Password
            if (empty($data['password'])) {
                $data['password_err'] = 'Please enter password';
            } elseif (strlen($data['password']) < 6) {
                $data['password_err'] = 'Password must be at least 6 characters';
            }

            // Validate Confirm Password
            if (empty($data['confirm_password'])) {
                $data['confirm_password_err'] = 'Please confirm password';
            } else {
                if ($data['password'] != $data['confirm_password']) {
                    $data['confirm_password_err'] = 'Passwords do not match';
                }
            }

            // Make sure errors are empty
            if (
                empty($data['username_err']) && empty($data['email_err']) &&
                empty($data['password_err']) && empty($data['confirm_password_err'])
            ) {
                // Hash password
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

                // Register User
                if ($this->userModel->register($data)) {
                    flash('register_success', 'You are registered and can log in');
                    redirect('auth/login');
                } else {
                    die('Something went wrong');
                }
            } else {
                // Load view with errors
                $data['hide_navbar'] = true;
                $this->view('auth/register', $data);
            }
        } else {
            // Init data
            $data = [
                'username' => '',
                'email' => '',
                'password' => '',
                'confirm_password' => '',
                'username_err' => '',
                'email_err' => '',
                'password_err' => '',
                'confirm_password_err' => ''
            ];

            // Load view
            $data['hide_navbar'] = true;
            $this->view('auth/register', $data);
        }
    }

    public function login()
    {
        // Check for POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Init data with proper sanitization
            $data = [
                'username' => $this->sanitizeInput($_POST['username'] ?? ''),
                'password' => trim($_POST['password'] ?? ''),
                'username_err' => '',
                'password_err' => '',
            ];

            // Validate Username
            if (empty($data['username'])) {
                $data['username_err'] = 'Please enter username';
            }

            // Validate Password
            if (empty($data['password'])) {
                $data['password_err'] = 'Please enter password';
            }

            // Check for user/username
            if ($this->userModel->findUserByUsername($data['username'])) {
                // User found
            } else {
                $data['username_err'] = 'No user found';
            }

            // Make sure errors are empty
            if (empty($data['username_err']) && empty($data['password_err'])) {
                // Validated
                // Check and set logged in user
                $loggedInUser = $this->userModel->login($data['username'], $data['password']);

                if ($loggedInUser) {
                    // Create Session
                    $this->createUserSession($loggedInUser);
                } else {
                    $data['password_err'] = 'Password incorrect';
                    $this->view('auth/login', $data);
                }
            } else {
                // Load view with errors
                $data['hide_navbar'] = true;
                $this->view('auth/login', $data);
            }
        } else {
            // Init data
            $data = [
                'username' => '',
                'password' => '',
                'username_err' => '',
                'password_err' => '',
            ];

            // Load view
            $data['hide_navbar'] = true;
            $this->view('auth/login', $data);
        }
    }

    public function createUserSession($user)
    {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['username'] = $this->sanitizeOutput($user->username);
        $_SESSION['email'] = $this->sanitizeOutput($user->email);
        redirect('main');
    }

    public function logout()
    {
        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        unset($_SESSION['email']);
        session_destroy();
        redirect('auth/login');
    }

    /**
     * Sanitize general input
     */
    private function sanitizeInput($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitize email specifically
     */
    private function sanitizeEmail($email)
    {
        $email = trim($email);
        $email = stripslashes($email);
        $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }

    /**
     * Sanitize output for display
     */
    private function sanitizeOutput($data)
    {
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
}
