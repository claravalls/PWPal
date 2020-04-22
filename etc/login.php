<?php

include 'src/Model/User.php';

interface UserRepository {
    public function validateFields(string $email, string $password);
    public function save(User $user);
    public function find(User $user);
}

final class MySQLMySQLRepository implements MySQLRepository {

    public function validateFields(string $email, string $password){
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "Invalid email format\n";
            return 0;
        }
        if (strlen($password) < 6){
            echo "Password too short.\n";
            return 0;
        }
        return 1;
    }

    public function save(User $user){
    }
        
    public function find(User $user){
        try {
            $db = new PDO('mysql:host=localhost;dbname=php_exercices_db', 'homestead', 'secret');
            // set the PDO error mode to exception
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $db->prepare("SELECT id, password FROM user WHERE email ='" . $user->getEmail() . "'");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if($result[0]['password'] == $user->getPassword())   {
                echo "Starts session.\n";
                session_start();

                if (!isset($_SESSION['id'])) {
                    $_SESSION['id'] = $result[0]['id'];

                } 
                echo $_SESSION['id'];

            }else{
                echo "Password NOT OK\n";
            }
            
        }
        catch(PDOException $e){
            echo "Connection failed: " . $e->getMessage();
        }
    }
    
}

if (!(empty($_POST['email']) || empty($_POST['password']))) {

    $u = new User($_POST["email"], $_POST["password"]);
    $repo = new MySQLUserRepository();

    if($repo->validateFields($u->getEmail(), $u->getPassword())){
        $repo->find($u);
    }
} 

?>
<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
</head>

<body>
    <form action="login.php" method="POST" accept-charset="utf-8">
        <input type="email" name="email" value="" placeholder="Enter your email" required>
        <input type="password" name="password" value="" placeholder="Enter your password">
        <button type="submit">Send</button>
    </form>
    <br>
    <a href="register.php">Register</a>
</body>

</html>
