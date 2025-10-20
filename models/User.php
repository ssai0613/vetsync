<?php
class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($user_un, $user_pass) {
        $query = "SELECT * FROM users WHERE user_un = :user_un";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_un", $user_un);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        // I-check nato if the user exists and if the password is like totally correct.
        if ($user && crypt($user_pass, $user['user_pass']) === $user['user_pass']) {
            return $user; // Success!
        }
        return false; // Fail! Try again.
    }
}