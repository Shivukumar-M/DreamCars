<?php

class Login extends BasicPage {

    private $values = array();

    private function verify() {
        $errors = array();

        $required = ['username', 'password'];

        foreach ($required as $field) {
            if (!isset($_POST[$field]) || strlen($_POST[$field]) == 0)
                $errors[] = $field . ' is required!';
            else
                $this->values[$field] = $_POST[$field];
        }

        return $errors;
    }

    public function render() {
        $this->setTitle('Sign In - RentDream');

        $errors = array();
        $isAdminLogin = isset($_GET['admin']) && $_GET['admin'] == 'true';

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $errors = $this->verify();

            if(count($errors) == 0) {
                $username = $_POST['username'];
                $password = $_POST['password'];
                $isAdminLogin = isset($_POST['is_admin']) && $_POST['is_admin'] == 'true';

                try {
                    $db = Database::getInstance()->getDb();
                    
                    if($isAdminLogin) {
                        // Admin login logic
                        $stmt = $db->prepare("
                            SELECT u._id, u.password, u.first_name, u.last_name, u.username, u.email
                            FROM user u 
                            JOIN admins a ON u._id = a.user_id 
                            WHERE u.username = ? OR u.email = ?
                        ");
                        $stmt->execute([$username, $username]);
                        $user = $stmt->fetch(PDO::FETCH_ASSOC);

                        if($user) {
                            if(password_verify($password, $user['password'])) {
                                $_SESSION['admin_logged_in'] = true;
                                $_SESSION['admin_id'] = $user['_id'];
                                $_SESSION['admin_name'] = $user['first_name'] . ' ' . $user['last_name'];
                                $_SESSION['admin_username'] = $user['username'];
                                $_SESSION['admin_email'] = $user['email'];
                                
                                $_SESSION['flash_message'] = 'Welcome back, ' . $user['first_name'] . '!';
                                $_SESSION['flash_type'] = 'success';
                                
                                header("Location: /admin/dashboard");
                                exit;
                            } else {
                                $errors[] = "Invalid password for admin account";
                            }
                        } else {
                            $errors[] = "No admin account found with this username/email";
                        }
                    } else {
                        // Regular user login logic
                        $id = User::doesUserExist($username);
                        if ($id == 0) {
                            $errors[] = 'User does not exist! Please register first!';
                        } else if(!User::verifyUser($id, $password)) {
                            $errors[] = 'Wrong Password!';
                        } else {
                            $this->values = [];
                            Utils::login($id);
                            $this->refreshStatus();
                            
                            $_SESSION['flash_message'] = 'Successfully logged in!';
                            $_SESSION['flash_type'] = 'success';
                            
                            header("Location: /");
                            exit;
                        }
                    }
                    
                } catch (PDOException $e) {
                    $errors[] = "Database error: " . $e->getMessage();
                    error_log("Login error: " . $e->getMessage());
                }
            }
        }

        Renderer::render("login.php", [
            'errors' => $errors,
            'values' => $this->values,
            'isAdminLogin' => $isAdminLogin
        ]);
    }

}