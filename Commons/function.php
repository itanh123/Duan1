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

// Render view với layout admin
function renderAdminView($viewPath, $data = [], $pageTitle = 'Admin Panel')
{
    // Extract data to variables
    extract($data);
    
    // Set page title
    $pageTitle = $pageTitle;
    
    // Start output buffering for content
    ob_start();
    include $viewPath;
    $content = ob_get_clean();
    
    // Include layout
    include './admin/View/layout.php';
}

/**
 * Tính thứ trong tuần từ ngày học hoặc dùng thứ trong tuần từ database
 * Ưu tiên: Nếu có ngày học, tính thứ từ ngày đó. Nếu không, dùng thứ trong tuần từ database.
 * 
 * @param string|null $ngay_hoc Ngày học (format: Y-m-d hoặc Y-m-d H:i:s)
 * @param string|null $thu_trong_tuan Thứ trong tuần từ database (Thứ 2, Thứ 3, ..., Chủ nhật)
 * @return string Thứ trong tuần (Thứ 2, Thứ 3, ..., Chủ nhật)
 */
function tinhThuTuNgayHoc($ngay_hoc = null, $thu_trong_tuan = null)
{
    // Nếu có ngày học, tính thứ từ ngày đó
    if (!empty($ngay_hoc)) {
        try {
            $date = new DateTime($ngay_hoc);
            $thu = (int)$date->format('N'); // 1 = Monday, 7 = Sunday
            $thuMap = [
                1 => 'Thứ 2',
                2 => 'Thứ 3',
                3 => 'Thứ 4',
                4 => 'Thứ 5',
                5 => 'Thứ 6',
                6 => 'Thứ 7',
                7 => 'Chủ nhật'
            ];
            return $thuMap[$thu] ?? $thu_trong_tuan ?? 'Chưa xác định';
        } catch (Exception $e) {
            // Nếu lỗi parse ngày, dùng thứ trong tuần từ database
            return $thu_trong_tuan ?? 'Chưa xác định';
        }
    }
    
    // Nếu không có ngày học, dùng thứ trong tuần từ database
    return $thu_trong_tuan ?? 'Chưa xác định';
}



?>