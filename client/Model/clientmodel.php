<?php
class clientmodel
{
    public $conn;

    public function __construct()
    {
        $this->conn = connectDB();
    }
}


?>