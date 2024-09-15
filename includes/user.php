<?php
class user
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }
    public function getUser($uname)
    {
        // Query to get the user by email
        $stmt = $this->db->prepare("SELECT * FROM users WHERE name = :uname");
        $stmt->execute(['uname' => $uname]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user;
    }
    public function getUserByID($id)
    {
        // Query to get the user by email
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :uid");
        $stmt->execute(['uid' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user;
    }
}
?>