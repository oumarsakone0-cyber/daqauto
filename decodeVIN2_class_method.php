<?php
/**
 * decodeVIN2() Class Method for products.php
 * 
 * This function handles VIN decoding with database check and external API fallback
 * 此函数处理VIN解码，包括数据库检查和外部API回退
 * 
 * Usage in products.php:
 *        case 'decode-vin2':
 *            $this->decodeVIN2();
 *            break;
 * 
 * Function: decodeVIN2()
 * Purpose: Check database first, if not found, call external API
 * 功能：先检查数据库，如果未找到，则调用外部API
 */
private function decodeVIN2() {
    // Set CORS headers / 设置CORS头
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Content-Type: application/json; charset=utf-8');
    
    // Handle preflight OPTIONS request / 处理预检OPTIONS请求
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
    
    // Only accept POST requests / 只接受POST请求
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->sendResponse(405, ['error' => 'Method not allowed. Use POST.']);
        return;
    }
    
    // Get VIN from POST data / 从POST数据获取VIN
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    $vin = $data['vin'] ?? '';
    
    // Validate VIN / 验证VIN
    if (empty($vin) || strlen($vin) !== 17) {
        $this->sendResponse(400, [
            'success' => false,
            'error' => 'VIN invalide. Le VIN doit contenir 17 caractères.'
        ]);
        return;
    }
    
    try {
        // Database configuration - Try to load configuration file / 数据库配置 - 尝试加载配置文件
        $configFile = __DIR__ . '/config.php';
        if (file_exists($configFile)) {
            $config = require $configFile;
            $db_host = $config['database']['host'] ?? 'localhost';
            $db_name = $config['database']['dbname'] ?? 'c5apidata';
            $db_user = $config['database']['username'] ?? 'c5ajay00';
            $db_pass = $config['database']['password'] ?? '';
        } else {
            // Default configuration (production should use config.php) / 默认配置（生产环境应使用config.php）
            $db_host = 'localhost';
            $db_name = 'c5apidata';
            $db_user = 'c5ajay00';
            $db_pass = 'TestApi@';
        }
        
        // Connect to database / 连接数据库
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
        
        // Check if VIN exists in database / 检查数据库中是否存在VIN
        $stmt = $pdo->prepare("SELECT * FROM vin_decoded_data WHERE vin = :vin LIMIT 1");
        $stmt->bindValue(':vin', $vin, PDO::PARAM_STR);
        $stmt->execute();
        $dbData = $stmt->fetch();
        
        if ($dbData) {
            // VIN found in database, return database data / 在数据库中找到VIN，返回数据库数据
            // Parse model_list if it's a JSON string / 如果model_list是JSON字符串，解析它
            if (!empty($dbData['model_list']) && is_string($dbData['model_list'])) {
                $decodedModelList = json_decode($dbData['model_list'], true);
                if ($decodedModelList !== null) {
                    $dbData['model_list'] = $decodedModelList;
                } else {
                    $dbData['model_list'] = null;
                }
            }
            
            $this->sendResponse(200, [
                'success' => true,
                'message' => 'VIN data retrieved from database',
                'data' => $dbData,
                'source' => 'database'
            ]);
            return;
        }
        
        // VIN not found in database, call external API / 数据库中未找到VIN，调用外部API
        // Use Tanshu API / 使用Tanshu API
        $apiKey = 'c5d10bc2b3e40f8a17998f8a5b7ce156';
        $apiUrl = "https://api.tanshuapi.com/api/vin/v2/index?key={$apiKey}&vin=" . urlencode($vin);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($httpCode !== 200 || !$response) {
            throw new Exception('External API error: ' . ($curlError ?: "HTTP $httpCode"));
        }
        
        $apiData = json_decode($response, true);
        
        if (empty($apiData) || !isset($apiData['data'])) {
            throw new Exception('Invalid response from external API');
        }
        
        // Extract car data from API response / 从API响应中提取车辆数据
        $carData = $apiData['data'];
        
        // Map API response to our format (similar to fillCarDataFromAPI logic) / 将API响应映射到我们的格式（类似于fillCarDataFromAPI逻辑）
        $mappedData = [
            'vin' => $vin,
            'brand_name' => $carData['brand_name'] ?? $carData['brand'] ?? '',
            'series_name' => $carData['series_name'] ?? $carData['series'] ?? '',
            'model_name' => $carData['model_name'] ?? $carData['model'] ?? '',
            'version' => $carData['version'] ?? $carData['car_trim_level'] ?? '',
            'year' => !empty($carData['year']) ? intval($carData['year']) : null,
            'fuel_type' => $carData['fuel_type'] ?? '',
            'transmission' => $carData['transmission'] ?? '',
            'drivetrain' => $carData['drivetrain'] ?? '',
            'engine_size' => !empty($carData['engine_size']) ? floatval($carData['engine_size']) : null,
            'engine_cylinders' => !empty($carData['engine_cylinders']) ? intval($carData['engine_cylinders']) : null,
            'body_type' => $carData['body_type'] ?? '',
            'doors' => !empty($carData['doors']) ? intval($carData['doors']) : null,
            'seats' => !empty($carData['seats']) ? intval($carData['seats']) : null,
            'length' => !empty($carData['length']) ? floatval($carData['length']) : null,
            'width' => !empty($carData['width']) ? floatval($carData['width']) : null,
            'height' => !empty($carData['height']) ? floatval($carData['height']) : null,
            'wheelbase' => !empty($carData['wheelbase']) ? floatval($carData['wheelbase']) : null,
            'curb_weight' => !empty($carData['curb_weight']) ? floatval($carData['curb_weight']) : null,
            'gross_weight' => !empty($carData['gross_weight']) ? floatval($carData['gross_weight']) : null,
            'max_power' => !empty($carData['max_power']) ? floatval($carData['max_power']) : null,
            'max_torque' => !empty($carData['max_torque']) ? floatval($carData['max_torque']) : null,
            'acceleration' => !empty($carData['acceleration']) ? floatval($carData['acceleration']) : null,
            'mpg_city' => !empty($carData['mpg_city']) ? floatval($carData['mpg_city']) : null,
            'mpg_highway' => !empty($carData['mpg_highway']) ? floatval($carData['mpg_highway']) : null,
            'mpg_combined' => !empty($carData['mpg_combined']) ? floatval($carData['mpg_combined']) : null,
            'co2_emissions' => !empty($carData['co2_emissions']) ? floatval($carData['co2_emissions']) : null,
            'exterior_color' => $carData['exterior_color'] ?? '',
            'interior_color' => $carData['interior_color'] ?? '',
            'interior_material' => $carData['interior_material'] ?? '',
            'manufacturer' => $carData['manufacturer'] ?? '',
            'market_date' => $carData['market_date'] ?? null,
            'stop_date' => $carData['stop_date'] ?? null,
            'front_tyre_size' => $carData['front_tyre_size'] ?? '',
            'rear_tyre_size' => $carData['rear_tyre_size'] ?? '',
            'front_brake_type' => $carData['front_brake_type'] ?? '',
            'rear_brake_type' => $carData['rear_brake_type'] ?? '',
            'parking_brake_type' => $carData['parking_brake_type'] ?? '',
            'data_source' => 'api',
            'api_provider' => 'TanshuAPI',
            'decoded_at' => date('Y-m-d H:i:s'),
            'language' => 'zh' // API returns Chinese data / API返回中文数据
        ];
        
        // Handle model_list if present / 如果存在model_list，处理它
        if (isset($carData['model_list']) && is_array($carData['model_list'])) {
            $mappedData['model_list'] = $carData['model_list'];
        } elseif (isset($carData['model_list']) && is_object($carData['model_list'])) {
            $mappedData['model_list'] = (array)$carData['model_list'];
        }
        
        // Return API data (without saving to database) / 返回API数据（不保存到数据库）
        $this->sendResponse(200, [
            'success' => true,
            'message' => 'VIN data retrieved from external API',
            'data' => $mappedData,
            'source' => 'api',
            'raw_data' => $carData // Include raw data for reference / 包含原始数据以供参考
        ]);
        
    } catch (PDOException $e) {
        error_log("Database error in decodeVIN2: " . $e->getMessage());
        $this->sendResponse(500, [
            'success' => false,
            'error' => 'Database error: ' . $e->getMessage()
        ]);
    } catch (Exception $e) {
        error_log("Error in decodeVIN2: " . $e->getMessage());
        $this->sendResponse(500, [
            'success' => false,
            'error' => 'Error decoding VIN: ' . $e->getMessage()
        ]);
    }
}
