<?php
include "../controllers/DBUtil.php";

class User
{
    private $db;

    public function __construct()
    {
        $this->db = new DBUntil();
    }

    public function listUsers()
    {
        $sql = "SELECT * FROM users";
        return $this->db->select($sql);
    }

    public function addUser($data)
    {
        //  email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        if (!preg_match('/^[0-9]{10}$/', $data['phone'])) {
            throw new Exception("Invalid phone number format. Must be 10 digits.");
        }
        return $this->db->insert('users', $data);
    }

    public function deleteUser($id)
    {
        return $this->db->delete('users', "id = $id");
    }

    public function updateUser($id, $data)
    {
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }
        if (!preg_match('/^[0-9]{10}$/', $data['phone'])) {
            throw new Exception("Invalid phone number format. Must be 10 digits.");
        }

        return $this->db->update('users', $data, "id = $id");
    }

    public function searchUsers($query)
    {
        $sql = "SELECT * FROM users WHERE username LIKE ? OR email LIKE ? OR role LIKE ? OR status LIKE ? OR address LIKE ? OR phone LIKE ?";
        $params = array_fill(0, 6, "%$query%");
        return $this->db->select($sql, $params);
    }
    
}
?>
