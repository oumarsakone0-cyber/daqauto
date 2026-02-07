<?php
/**
 * VIN Decoded Data API Endpoint / VIN解码数据API端点
 * 
 * Features / 功能：
 * - Save VIN decoded data to database (English format) / 保存VIN解码数据到数据库（英文格式）
 * - Retrieve cached VIN data from database / 从数据库检索缓存的VIN数据
 * - Support CORS cross-origin requests / 支持CORS跨域请求
 * 
 * Configuration / 配置：
 * - Database configuration should be set via config.php file (refer to config.example.php) / 数据库配置请通过config.php文件设置（参考config.example.php）
 * - If config.php does not exist, default configuration will be used (requires manual modification) / 如果config.php不存在，将使用默认配置（需要手动修改）
 * 
 * Important / 重要：Ensure file header has no BOM characters or spaces, file must be saved in UTF-8 without BOM format / 确保文件开头没有BOM字符或空格，文件必须以UTF-8无BOM格式保存
 */

// Start output buffering immediately (must be before any possible output) / 立即开始输出缓冲（必须在任何可能的输出之前）
// Use a safer way to handle output buffering / 使用更安全的方式处理输出缓冲
try {
    if (ob_get_level() == 0) {
        ob_start();
    }
    // Clear any possible output / 清除任何可能的输出
    if (ob_get_length() > 0) {
        ob_clean();
    }
} catch (Exception $e) {
    // If output buffering has issues, try to reset / 如果输出缓冲有问题，尝试重置
    while (@ob_end_clean()) {}
    ob_start();
}

// Record script start execution time / 记录脚本开始执行时间
$scriptStartTime = microtime(true);
error_log("Script started at " . date('Y-m-d H:i:s'));

// Set error reporting (but don't display to users) / 设置错误报告（但不显示给用户）
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
// Set error log to accessible location / 设置错误日志到可访问的位置
$errorLogPath = __DIR__ . '/php_errors.log';
ini_set('error_log', $errorLogPath);

// Set execution time limit (prevent timeout) / 设置执行时间限制（防止超时）
set_time_limit(30); // 30 seconds timeout / 30秒超时
ini_set('max_execution_time', 30);

// Set CORS headers immediately (must be before any output, including spaces, newlines, etc.) / 立即设置CORS头（必须在任何输出之前，包括空格、换行等）
// Use header_remove() to clear any existing old headers / 使用header_remove()清除可能存在的旧header
header_remove();

// 强制设置CORS头
header('Access-Control-Allow-Origin: *', true);
header('Access-Control-Allow-Methods: GET, POST, OPTIONS', true);
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With', true);
header('Access-Control-Max-Age: 86400', true);
header('Content-Type: application/json; charset=utf-8', true);

// Record header setting status (for debugging) / 记录header设置状态（用于调试）
if (headers_sent($file, $line)) {
    error_log("WARNING: Headers already sent in $file at line $line");
} else {
    error_log("CORS headers set successfully");
}

// Error handling function / 错误处理函数
function sendErrorResponse($code, $message, $errorType = 'Error') {
    // 清除之前的输出（限制循环次数，防止无限循环）
    $maxIterations = 10;
    $iterations = 0;
    while (ob_get_level() > 0 && $iterations < $maxIterations) {
        ob_clean();
        $iterations++;
    }
    
    // Force set CORS headers (even if already set) / 强制设置CORS头（即使之前已经设置）
    header_remove();
    header('Access-Control-Allow-Origin: *', true);
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS', true);
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With', true);
    header('Content-Type: application/json; charset=utf-8', true);
    
    http_response_code($code);
    
    $response = [
        'success' => false,
        'error' => $message,
        'error_type' => $errorType
    ];
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
    $iterations = 0;
    while (ob_get_level() > 0 && $iterations < $maxIterations) {
        ob_end_flush();
        $iterations++;
    }
    exit();
}

// Success response function / 成功响应函数
function sendSuccessResponse($data) {
    // Clear previous output (limit iterations to prevent infinite loop) / 清除之前的输出（限制循环次数，防止无限循环）
    $maxIterations = 10;
    $iterations = 0;
    while (ob_get_level() > 0 && $iterations < $maxIterations) {
        ob_clean();
        $iterations++;
    }
    
    // Force set CORS headers / 强制设置CORS头
    header_remove();
    header('Access-Control-Allow-Origin: *', true);
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS', true);
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With', true);
    header('Content-Type: application/json; charset=utf-8', true);
    
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    
    $iterations = 0;
    while (ob_get_level() > 0 && $iterations < $maxIterations) {
        ob_end_flush();
        $iterations++;
    }
    exit();
}

// Handle OPTIONS request (CORS preflight) / 处理OPTIONS请求（CORS预检）
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Clear previous output (limit iterations) / 清除之前的输出（限制循环次数）
    $maxIterations = 10;
    $iterations = 0;
    while (ob_get_level() > 0 && $iterations < $maxIterations) {
        ob_clean();
        $iterations++;
    }
    
    // 强制设置CORS头
    header_remove();
    header('Access-Control-Allow-Origin: *', true);
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS', true);
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With', true);
    header('Access-Control-Max-Age: 86400', true);
    header('Content-Type: application/json; charset=utf-8', true);
    
    http_response_code(200);
    
    $iterations = 0;
    while (ob_get_level() > 0 && $iterations < $maxIterations) {
        ob_end_flush();
        $iterations++;
    }
    exit();
}

// Handle GET request - Query VIN data or display API information / 处理GET请求 - 查询VIN数据或显示API信息
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // If action=get and vin parameters are provided, query VIN data / 如果提供了action=get和vin参数，则查询VIN数据
    if (isset($_GET['action']) && $_GET['action'] === 'get' && isset($_GET['vin'])) {
        // URL decode VIN parameter (handle special characters like +) / URL解码VIN参数（处理+号等特殊字符）
        $vin = trim(urldecode($_GET['vin']));
        // Remove possible spaces and other characters / 移除可能的空格和其他字符
        $vin = preg_replace('/\s+/', '', $vin);
        
        if (empty($vin)) {
            sendErrorResponse(400, 'VIN parameter is required.');
        }
        
        // VIN length check / VIN长度检查
        if (strlen($vin) !== 17) {
            sendErrorResponse(400, 'Invalid VIN length. VIN must be exactly 17 characters. Received: ' . strlen($vin) . ' characters.');
        }
        
        // Connect to database for query / 连接数据库查询
        try {
            // Try to load configuration file / 尝试加载配置文件
            $configFile = __DIR__ . '/config.php';
            if (file_exists($configFile)) {
                $config = require $configFile;
                $db_host = $config['database']['host'] ?? 'localhost';
                $db_name = $config['database']['dbname'] ?? 'c5apidata';
                $db_user = $config['database']['username'] ?? 'c5ajay00';
                $db_pass = $config['database']['password'] ?? '';
            } else {
                // Default configuration (use config.php in production) / 默认配置（生产环境请使用config.php）
                $db_host = 'localhost';
                $db_name = 'c5apidata';
                $db_user = 'c5ajay00';
                $db_pass = 'TestApi@';
            }
            
            $pdo = new PDO(
                "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
                $db_user,
                $db_pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_TIMEOUT => 5
                ]
            );
            
            $stmt = $pdo->prepare("SELECT * FROM vin_decoded_data WHERE vin = :vin LIMIT 1");
            $stmt->bindValue(':vin', $vin, PDO::PARAM_STR);
            $stmt->execute();
            
            $data = $stmt->fetch();
            
            if ($data) {
                // If model_list is a JSON string, parse it as an object / 如果model_list是JSON字符串，解析为对象
                if (!empty($data['model_list']) && is_string($data['model_list'])) {
                    $decodedModelList = json_decode($data['model_list'], true);
                    if ($decodedModelList !== null) {
                        $data['model_list'] = $decodedModelList;
                    } else {
                        $data['model_list'] = null;
                    }
                }
                
                sendSuccessResponse([
                    'success' => true,
                    'data' => $data,
                    'source' => 'database'
                ]);
            } else {
                sendSuccessResponse([
                    'success' => true,
                    'data' => null,
                    'message' => 'VIN not found in database'
                ]);
            }
        } catch (PDOException $e) {
            error_log("GET VIN Query Error: " . $e->getMessage());
            sendErrorResponse(500, 'Database error: ' . $e->getMessage(), 'PDOException');
        } catch (Exception $e) {
            error_log("GET VIN Error: " . $e->getMessage());
            sendErrorResponse(500, 'Error: ' . $e->getMessage(), 'Exception');
        }
    }
    
    // Otherwise display API information / 否则显示API信息
    sendSuccessResponse([
        'success' => true,
        'message' => 'VIN Data API is running',
        'endpoint' => 'save_vin_data.php',
        'methods' => ['GET', 'POST', 'OPTIONS'],
        'description' => 'This API endpoint saves and retrieves VIN decoded data to/from the database in English format.',
        'database' => 'c5apidata',
        'table' => 'vin_decoded_data',
        'usage' => [
            'GET' => '?action=get&vin=YOUR_VIN - Retrieve VIN data from database',
            'POST' => 'Send JSON data to save VIN data to database'
        ]
    ]);
}

// Only accept POST requests (GET handled above) / 只接受POST请求（GET已在上方处理）
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendErrorResponse(405, 'Method not allowed. Allowed methods: GET, POST, OPTIONS', 'MethodNotAllowed');
}

// Database configuration - Try to load configuration file / 数据库配置 - 尝试加载配置文件
$configFile = __DIR__ . '/config.php';
if (file_exists($configFile)) {
    $config = require $configFile;
    $db_host = $config['database']['host'] ?? 'localhost';
    $db_name = $config['database']['dbname'] ?? 'c5apidata';
    $db_user = $config['database']['username'] ?? 'c5ajay00';
    $db_pass = $config['database']['password'] ?? '';
} else {
    // 默认配置（生产环境请使用config.php）
    $db_host = 'localhost';
    $db_name = 'c5apidata';
    $db_user = 'c5ajay00';
    $db_pass = 'TestApi@';
}

try {
    // Connect to database / 连接数据库
    try {
        $pdo = new PDO(
            "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
            $db_user,
            $db_pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_TIMEOUT => 5
            ]
        );
        error_log("Database connection successful");
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        error_log("Connection details: host=$db_host, db=$db_name, user=$db_user");
        sendErrorResponse(500, 'Database connection failed. Please check server configuration.', 'DatabaseConnectionError');
    }

    // Get POST data / 获取POST数据
    $input = file_get_contents('php://input');
    
    // Record original input (for debugging, limit length) / 记录原始输入（用于调试，限制长度）
    if ($input) {
        error_log("VIN API POST Input (first 500 chars): " . substr($input, 0, 500));
    } else {
        error_log("VIN API POST Input: EMPTY");
    }
    
    $data = json_decode($input, true);

    if (!$data) {
        $jsonError = json_last_error_msg();
        error_log("JSON decode error: " . $jsonError);
        sendErrorResponse(400, 'Invalid JSON data: ' . $jsonError);
    }

    // 验证必需字段
    if (empty($data['vin'])) {
        sendErrorResponse(400, 'VIN is required');
    }
    
    // Record received data (for debugging) / 记录接收到的数据（用于调试）
    error_log("VIN API Received data for VIN: " . $data['vin']);
    $operationStartTime = microtime(true);

    // Table already exists (confirmed via test_db.php), skip table creation check for better performance / 表已经存在（通过test_db.php确认），跳过表创建检查以提升性能
    // If table doesn't exist, SQL execution will automatically error, we can handle it in error handling / 如果表不存在，SQL执行时会自动报错，我们可以在错误处理中处理

    // Prepare insert/update data / 准备插入/更新数据
    error_log("Starting SQL preparation at " . (microtime(true) - $operationStartTime) . " seconds");
    $sql = "
    INSERT INTO `vin_decoded_data` (
        `vin`, `brand_name`, `series_name`, `model_name`, `year`,
        `fuel_type`, `transmission`, `drivetrain`,
        `engine_size`, `engine_cylinders`,
        `body_type`, `doors`, `seats`,
        `length`, `width`, `height`, `wheelbase`,
        `curb_weight`, `gross_weight`,
        `max_power`, `max_torque`, `acceleration`,
        `mpg_city`, `mpg_highway`, `mpg_combined`, `co2_emissions`,
        `exterior_color`, `interior_color`, `interior_material`,
        `version`, `manufacturer`, `market_date`, `stop_date`,
        `front_tyre_size`, `rear_tyre_size`,
        `front_brake_type`, `rear_brake_type`, `parking_brake_type`,
        `data_source`, `api_provider`, `decoded_at`, `language`, `model_list`
    ) VALUES (
        :vin, :brand_name, :series_name, :model_name, :year,
        :fuel_type, :transmission, :drivetrain,
        :engine_size, :engine_cylinders,
        :body_type, :doors, :seats,
        :length, :width, :height, :wheelbase,
        :curb_weight, :gross_weight,
        :max_power, :max_torque, :acceleration,
        :mpg_city, :mpg_highway, :mpg_combined, :co2_emissions,
        :exterior_color, :interior_color, :interior_material,
        :version, :manufacturer, :market_date, :stop_date,
        :front_tyre_size, :rear_tyre_size,
        :front_brake_type, :rear_brake_type, :parking_brake_type,
        :data_source, :api_provider, :decoded_at, :language, :model_list
    )
    ON DUPLICATE KEY UPDATE
        `brand_name` = VALUES(`brand_name`),
        `series_name` = VALUES(`series_name`),
        `model_name` = VALUES(`model_name`),
        `year` = VALUES(`year`),
        `fuel_type` = VALUES(`fuel_type`),
        `transmission` = VALUES(`transmission`),
        `drivetrain` = VALUES(`drivetrain`),
        `engine_size` = VALUES(`engine_size`),
        `engine_cylinders` = VALUES(`engine_cylinders`),
        `body_type` = VALUES(`body_type`),
        `doors` = VALUES(`doors`),
        `seats` = VALUES(`seats`),
        `length` = VALUES(`length`),
        `width` = VALUES(`width`),
        `height` = VALUES(`height`),
        `wheelbase` = VALUES(`wheelbase`),
        `curb_weight` = VALUES(`curb_weight`),
        `gross_weight` = VALUES(`gross_weight`),
        `max_power` = VALUES(`max_power`),
        `max_torque` = VALUES(`max_torque`),
        `acceleration` = VALUES(`acceleration`),
        `mpg_city` = VALUES(`mpg_city`),
        `mpg_highway` = VALUES(`mpg_highway`),
        `mpg_combined` = VALUES(`mpg_combined`),
        `co2_emissions` = VALUES(`co2_emissions`),
        `exterior_color` = VALUES(`exterior_color`),
        `interior_color` = VALUES(`interior_color`),
        `interior_material` = VALUES(`interior_material`),
        `version` = VALUES(`version`),
        `manufacturer` = VALUES(`manufacturer`),
        `market_date` = VALUES(`market_date`),
        `stop_date` = VALUES(`stop_date`),
        `front_tyre_size` = VALUES(`front_tyre_size`),
        `rear_tyre_size` = VALUES(`rear_tyre_size`),
        `front_brake_type` = VALUES(`front_brake_type`),
        `rear_brake_type` = VALUES(`rear_brake_type`),
        `parking_brake_type` = VALUES(`parking_brake_type`),
        `data_source` = VALUES(`data_source`),
        `api_provider` = VALUES(`api_provider`),
        `decoded_at` = VALUES(`decoded_at`),
        `language` = VALUES(`language`),
        `model_list` = VALUES(`model_list`),
        `updated_at` = CURRENT_TIMESTAMP
    ";

    $prepareStartTime = microtime(true);
    $stmt = $pdo->prepare($sql);
    error_log("SQL prepared in " . round((microtime(true) - $prepareStartTime) * 1000, 2) . " ms");

    // Bind parameters (handle null values) / 绑定参数（处理null值）
    $bindStartTime = microtime(true);
    $stmt->bindValue(':vin', $data['vin'] ?? null, PDO::PARAM_STR);
    $stmt->bindValue(':brand_name', !empty($data['brand_name']) ? $data['brand_name'] : null, PDO::PARAM_STR);
    $stmt->bindValue(':series_name', !empty($data['series_name']) ? $data['series_name'] : null, PDO::PARAM_STR);
    $stmt->bindValue(':model_name', !empty($data['model_name']) ? $data['model_name'] : null, PDO::PARAM_STR);
    $stmt->bindValue(':year', !empty($data['year']) ? (int)$data['year'] : null, PDO::PARAM_INT);
    $stmt->bindValue(':fuel_type', !empty($data['fuel_type']) ? $data['fuel_type'] : null, PDO::PARAM_STR);
    $stmt->bindValue(':transmission', !empty($data['transmission']) ? $data['transmission'] : null, PDO::PARAM_STR);
    $stmt->bindValue(':drivetrain', !empty($data['drivetrain']) ? $data['drivetrain'] : null, PDO::PARAM_STR);
    $stmt->bindValue(':engine_size', !empty($data['engine_size']) ? $data['engine_size'] : null, PDO::PARAM_STR);
    $stmt->bindValue(':engine_cylinders', !empty($data['engine_cylinders']) ? (int)$data['engine_cylinders'] : null, PDO::PARAM_INT);
    $stmt->bindValue(':body_type', !empty($data['body_type']) ? $data['body_type'] : null, PDO::PARAM_STR);
    $stmt->bindValue(':doors', !empty($data['doors']) ? (int)$data['doors'] : null, PDO::PARAM_INT);
    $stmt->bindValue(':seats', !empty($data['seats']) ? (int)$data['seats'] : null, PDO::PARAM_INT);
    $stmt->bindValue(':length', !empty($data['length']) ? $data['length'] : null, PDO::PARAM_STR);
    $stmt->bindValue(':width', !empty($data['width']) ? $data['width'] : null, PDO::PARAM_STR);
    $stmt->bindValue(':height', !empty($data['height']) ? $data['height'] : null, PDO::PARAM_STR);
    $stmt->bindValue(':wheelbase', !empty($data['wheelbase']) ? $data['wheelbase'] : null, PDO::PARAM_STR);
    $stmt->bindValue(':curb_weight', !empty($data['curb_weight']) ? $data['curb_weight'] : null, PDO::PARAM_STR);
    $stmt->bindValue(':gross_weight', !empty($data['gross_weight']) ? $data['gross_weight'] : null, PDO::PARAM_STR);
    $stmt->bindValue(':max_power', !empty($data['max_power']) ? $data['max_power'] : null, PDO::PARAM_STR);
    $stmt->bindValue(':max_torque', !empty($data['max_torque']) ? $data['max_torque'] : null, PDO::PARAM_STR);
    $stmt->bindValue(':acceleration', !empty($data['acceleration']) ? $data['acceleration'] : null, PDO::PARAM_STR);
    $stmt->bindValue(':mpg_city', !empty($data['mpg_city']) ? $data['mpg_city'] : null, PDO::PARAM_STR);
    $stmt->bindValue(':mpg_highway', !empty($data['mpg_highway']) ? $data['mpg_highway'] : null, PDO::PARAM_STR);
    $stmt->bindValue(':mpg_combined', !empty($data['mpg_combined']) ? $data['mpg_combined'] : null, PDO::PARAM_STR);
    $stmt->bindValue(':co2_emissions', !empty($data['co2_emissions']) ? $data['co2_emissions'] : null, PDO::PARAM_STR);
    $stmt->bindValue(':exterior_color', !empty($data['exterior_color']) ? $data['exterior_color'] : null, PDO::PARAM_STR);
    $stmt->bindValue(':interior_color', !empty($data['interior_color']) ? $data['interior_color'] : null, PDO::PARAM_STR);
    $stmt->bindValue(':interior_material', !empty($data['interior_material']) ? $data['interior_material'] : null, PDO::PARAM_STR);
    $stmt->bindValue(':version', !empty($data['version']) ? $data['version'] : null, PDO::PARAM_STR);
    $stmt->bindValue(':manufacturer', !empty($data['manufacturer']) ? $data['manufacturer'] : null, PDO::PARAM_STR);
    // Date format conversion function: Convert various date formats to MySQL DATE format (YYYY-MM-DD) / 日期格式转换函数：将各种日期格式转换为MySQL DATE格式 (YYYY-MM-DD)
    $convertDate = function($dateValue) {
        if (empty($dateValue)) {
            return null;
        }
        
        // If already in correct format (YYYY-MM-DD), return directly / 如果已经是正确的格式 (YYYY-MM-DD)，直接返回
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateValue)) {
            return $dateValue;
        }
        
        // Handle YYYY.MM format (e.g., 2025.02) / 处理 YYYY.MM 格式 (如 2025.02)
        if (preg_match('/^(\d{4})\.(\d{2})$/', $dateValue, $matches)) {
            return $matches[1] . '-' . $matches[2] . '-01'; // 转换为 YYYY-MM-01
        }
        
        // Handle YYYY-MM format (e.g., 2025-02) / 处理 YYYY-MM 格式 (如 2025-02)
        if (preg_match('/^(\d{4})-(\d{2})$/', $dateValue, $matches)) {
            return $matches[1] . '-' . $matches[2] . '-01'; // 转换为 YYYY-MM-01
        }
        
        // 尝试使用PHP的DateTime解析
        try {
            $date = new DateTime($dateValue);
            return $date->format('Y-m-d');
        } catch (Exception $e) {
            error_log("Date conversion failed for: " . $dateValue . " - " . $e->getMessage());
            return null; // If parsing fails, return null / 如果无法解析，返回null
        }
    };
    
    // DateTime format conversion function: Convert ISO 8601 format to MySQL DATETIME format (YYYY-MM-DD HH:MM:SS) / 日期时间格式转换函数：将ISO 8601格式转换为MySQL DATETIME格式 (YYYY-MM-DD HH:MM:SS)
    $convertDateTime = function($dateTimeValue) {
        if (empty($dateTimeValue)) {
            return null;
        }
        
        // If already in correct format (YYYY-MM-DD HH:MM:SS), return directly / 如果已经是正确的格式 (YYYY-MM-DD HH:MM:SS)，直接返回
        if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $dateTimeValue)) {
            return $dateTimeValue;
        }
        
        // 处理ISO 8601格式 (如 2026-02-05T04:47:13.637Z 或 2026-02-05T04:47:13Z)
        // 移除毫秒和'Z'后缀
        $dateTimeValue = preg_replace('/\.\d{3}Z?$/', '', $dateTimeValue); // 移除毫秒
        $dateTimeValue = str_replace('Z', '', $dateTimeValue); // 移除Z后缀
        $dateTimeValue = str_replace('T', ' ', $dateTimeValue); // 将T替换为空格
        
        // If format is correct, return directly / 如果格式正确，直接返回
        if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $dateTimeValue)) {
            return $dateTimeValue;
        }
        
        // 尝试使用PHP的DateTime解析
        try {
            // 先尝试解析原始值
            $date = new DateTime($dateTimeValue);
            return $date->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            // If failed, try to parse ISO 8601 format / 如果失败，尝试解析ISO 8601格式
            try {
                // Handle ISO format with Z / 处理带Z的ISO格式
                $isoValue = str_replace('Z', '+00:00', $dateTimeValue);
                $date = new DateTime($isoValue);
                return $date->format('Y-m-d H:i:s');
            } catch (Exception $e2) {
                error_log("DateTime conversion failed for: " . $dateTimeValue . " - " . $e2->getMessage());
                return date('Y-m-d H:i:s'); // 如果无法解析，返回当前时间
            }
        }
    };
    
    $stmt->bindValue(':market_date', $convertDate($data['market_date'] ?? null), PDO::PARAM_STR);
    $stmt->bindValue(':stop_date', $convertDate($data['stop_date'] ?? null), PDO::PARAM_STR);
    $stmt->bindValue(':front_tyre_size', !empty($data['front_tyre_size']) ? $data['front_tyre_size'] : null, PDO::PARAM_STR);
    $stmt->bindValue(':rear_tyre_size', !empty($data['rear_tyre_size']) ? $data['rear_tyre_size'] : null, PDO::PARAM_STR);
    $stmt->bindValue(':front_brake_type', !empty($data['front_brake_type']) ? $data['front_brake_type'] : null, PDO::PARAM_STR);
    $stmt->bindValue(':rear_brake_type', !empty($data['rear_brake_type']) ? $data['rear_brake_type'] : null, PDO::PARAM_STR);
    $stmt->bindValue(':parking_brake_type', !empty($data['parking_brake_type']) ? $data['parking_brake_type'] : null, PDO::PARAM_STR);
    $stmt->bindValue(':data_source', $data['data_source'] ?? 'api', PDO::PARAM_STR);
    $stmt->bindValue(':api_provider', !empty($data['api_provider']) ? $data['api_provider'] : null, PDO::PARAM_STR);
    // Convert decoded_at to MySQL DATETIME format / 转换decoded_at为MySQL DATETIME格式
    $decodedAtValue = !empty($data['decoded_at']) ? $data['decoded_at'] : date('Y-m-d H:i:s');
    $stmt->bindValue(':decoded_at', $convertDateTime($decodedAtValue), PDO::PARAM_STR);
    $stmt->bindValue(':language', $data['language'] ?? 'en', PDO::PARAM_STR);
    
    // 处理model_list：如果存在，转换为JSON字符串保存
    $modelListValue = null;
    if (!empty($data['model_list'])) {
        if (is_array($data['model_list'])) {
            $modelListValue = json_encode($data['model_list'], JSON_UNESCAPED_UNICODE);
        } elseif (is_object($data['model_list'])) {
            $modelListValue = json_encode($data['model_list'], JSON_UNESCAPED_UNICODE);
        } elseif (is_string($data['model_list'])) {
            // If already a JSON string, validate and use directly / 如果已经是JSON字符串，验证并直接使用
            $decoded = json_decode($data['model_list'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $modelListValue = $data['model_list'];
            } else {
                error_log("Warning: model_list is not valid JSON: " . substr($data['model_list'], 0, 100));
            }
        }
        error_log("Saving model_list: " . substr($modelListValue ?? 'NULL', 0, 200));
    } else {
        error_log("No model_list provided in data");
    }
    $stmt->bindValue(':model_list', $modelListValue, PDO::PARAM_STR);
    error_log("Parameters bound in " . round((microtime(true) - $bindStartTime) * 1000, 2) . " ms");

    // 执行插入/更新
    try {
        $executeStartTime = microtime(true);
        $stmt->execute();
        $executeTime = round((microtime(true) - $executeStartTime) * 1000, 2);
        error_log("VIN data saved successfully for VIN: " . $data['vin'] . " in " . $executeTime . " ms");
        error_log("Total operation time: " . round((microtime(true) - $operationStartTime) * 1000, 2) . " ms");
    } catch (PDOException $e) {
        $executeTime = round((microtime(true) - ($executeStartTime ?? microtime(true))) * 1000, 2);
        error_log("SQL execution error after " . $executeTime . " ms: " . $e->getMessage());
        error_log("SQL State: " . $e->getCode());
        error_log("SQL: " . substr($sql, 0, 200));
        error_log("Data keys: " . implode(', ', array_keys($data)));
        error_log("Total operation time before error: " . round((microtime(true) - $operationStartTime) * 1000, 2) . " ms");
        throw $e;
    }

    // Get inserted/updated record ID / 获取插入/更新的记录ID
    $recordId = $pdo->lastInsertId();
    if (!$recordId) {
        // If it's an update operation, query existing record ID / 如果是更新操作，查询现有记录ID
        $stmtCheck = $pdo->prepare("SELECT id FROM vin_decoded_data WHERE vin = :vin LIMIT 1");
        $stmtCheck->bindValue(':vin', $data['vin'], PDO::PARAM_STR);
        $stmtCheck->execute();
        $recordId = $stmtCheck->fetchColumn();
    }

    sendSuccessResponse([
        'success' => true,
        'message' => 'VIN data saved successfully',
        'data' => [
            'id' => $recordId,
            'vin' => $data['vin']
        ]
    ]);

} catch (PDOException $e) {
    error_log("PDOException: " . $e->getMessage());
    error_log("SQL State: " . $e->getCode());
    error_log("File: " . $e->getFile() . " Line: " . $e->getLine());
    sendErrorResponse(500, 'Database error: ' . $e->getMessage() . ' (Code: ' . $e->getCode() . ')', 'PDOException');
} catch (Exception $e) {
    error_log("Exception: " . $e->getMessage());
    error_log("File: " . $e->getFile() . " Line: " . $e->getLine());
    sendErrorResponse(400, $e->getMessage(), 'Exception');
} catch (Error $e) {
    error_log("PHP Error: " . $e->getMessage());
    error_log("File: " . $e->getFile() . " Line: " . $e->getLine());
    sendErrorResponse(500, 'PHP Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(), 'Error');
} catch (Throwable $e) {
    error_log("Throwable: " . $e->getMessage());
    error_log("File: " . $e->getFile() . " Line: " . $e->getLine());
    sendErrorResponse(500, 'Unexpected error: ' . $e->getMessage(), 'Throwable');
}
