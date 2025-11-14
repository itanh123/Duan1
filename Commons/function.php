<?php
// hỗ trợ show bất cứ data nào
function debug($data)
{
    echo '<pre>';
    print_r($data);
    die();
}

function notFound()
{
    http_response_code(404);
    echo '404 - Page Not Found';
    exit;

}

//kết nối CSDL qua PDO

function connectDB()
{
    $host = DB_HOST;
    $port = DB_PORT;
    $dbname = DB_NAME;
    try {
        $conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname", DB_USERNAME, DB_PASSWORD);

        // cài đặt chế độ báo lỗi là xử lý ngoại lệ
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // cài đặt chế độ trả dữ liệu
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return $conn;
    } catch (PDOException $e) {
        debug("Connection false:" . $e->getMessage());
    }
}



?>