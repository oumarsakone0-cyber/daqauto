# Bank Information System Implementation

## 1. Database Migration

Execute this SQL command to create the bank information table:

```sql
CREATE TABLE `adjame_bank_info` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `boutique_id` INT(11) NOT NULL,
  `beneficiary_name` VARCHAR(255) NOT NULL,
  `bank_name` VARCHAR(255) NOT NULL,
  `account_number` VARCHAR(100) NOT NULL,
  `swift_code` VARCHAR(50) DEFAULT NULL,
  `bank_address` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_boutique` (`boutique_id`),
  CONSTRAINT `fk_bank_boutique` FOREIGN KEY (`boutique_id`) REFERENCES `adjame_boutique` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## 2. Backend API Implementation (bank_info.php)

Create a new file `api_adjame/bank_info.php`:

```php
<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'config.php';

class BankInfoAPI {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];

        switch ($method) {
            case 'GET':
                $this->handleGet();
                break;
            case 'POST':
                $this->handlePost();
                break;
            case 'PUT':
                $this->handlePut();
                break;
            default:
                http_response_code(405);
                echo json_encode([
                    'success' => false,
                    'error' => 'Method not allowed'
                ]);
        }
    }

    private function handleGet() {
        $action = $_GET['action'] ?? '';

        switch ($action) {
            case 'get_by_boutique':
                $this->getBankInfoByBoutique();
                break;
            default:
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'Invalid action'
                ]);
        }
    }

    private function handlePost() {
        $action = $_GET['action'] ?? '';

        switch ($action) {
            case 'create':
                $this->createBankInfo();
                break;
            default:
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'Invalid action'
                ]);
        }
    }

    private function handlePut() {
        $action = $_GET['action'] ?? '';

        switch ($action) {
            case 'update':
                $this->updateBankInfo();
                break;
            default:
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'Invalid action'
                ]);
        }
    }

    /**
     * Get bank information by boutique ID
     * GET parameter: boutique_id
     */
    private function getBankInfoByBoutique() {
        $boutique_id = $_GET['boutique_id'] ?? null;

        if (!$boutique_id) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Boutique ID is required'
            ]);
            return;
        }

        try {
            $stmt = $this->conn->prepare("
                SELECT
                    id,
                    boutique_id,
                    beneficiary_name,
                    bank_name,
                    account_number,
                    swift_code,
                    bank_address,
                    created_at,
                    updated_at
                FROM adjame_bank_info
                WHERE boutique_id = ?
            ");

            $stmt->bind_param("i", $boutique_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $bankInfo = $result->fetch_assoc();
                echo json_encode([
                    'success' => true,
                    'data' => $bankInfo,
                    'exists' => true
                ]);
            } else {
                // No bank info exists for this boutique yet
                echo json_encode([
                    'success' => true,
                    'data' => null,
                    'exists' => false,
                    'message' => 'No bank information found for this boutique'
                ]);
            }

        } catch (Exception $e) {
            error_log("Error in getBankInfoByBoutique: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Server error'
            ]);
        }
    }

    /**
     * Create bank information for a boutique
     * POST data: {boutique_id, beneficiary_name, bank_name, account_number, swift_code, bank_address}
     */
    private function createBankInfo() {
        $input = json_decode(file_get_contents('php://input'), true);

        // Validate required fields
        $required = ['boutique_id', 'beneficiary_name', 'bank_name', 'account_number'];
        foreach ($required as $field) {
            if (empty($input[$field])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => "Field '$field' is required"
                ]);
                return;
            }
        }

        $boutique_id = intval($input['boutique_id']);
        $beneficiary_name = trim($input['beneficiary_name']);
        $bank_name = trim($input['bank_name']);
        $account_number = trim($input['account_number']);
        $swift_code = isset($input['swift_code']) ? trim($input['swift_code']) : null;
        $bank_address = isset($input['bank_address']) ? trim($input['bank_address']) : null;

        try {
            // Check if boutique exists
            $stmt = $this->conn->prepare("SELECT id FROM adjame_boutique WHERE id = ?");
            $stmt->bind_param("i", $boutique_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'error' => 'Boutique not found'
                ]);
                return;
            }

            // Check if bank info already exists for this boutique
            $stmt = $this->conn->prepare("SELECT id FROM adjame_bank_info WHERE boutique_id = ?");
            $stmt->bind_param("i", $boutique_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                http_response_code(409);
                echo json_encode([
                    'success' => false,
                    'error' => 'Bank information already exists for this boutique. Use update instead.'
                ]);
                return;
            }

            // Insert bank info
            $stmt = $this->conn->prepare("
                INSERT INTO adjame_bank_info (
                    boutique_id,
                    beneficiary_name,
                    bank_name,
                    account_number,
                    swift_code,
                    bank_address
                ) VALUES (?, ?, ?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "isssss",
                $boutique_id,
                $beneficiary_name,
                $bank_name,
                $account_number,
                $swift_code,
                $bank_address
            );

            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $bank_info_id = $this->conn->insert_id;

                echo json_encode([
                    'success' => true,
                    'message' => 'Bank information created successfully',
                    'data' => [
                        'id' => $bank_info_id,
                        'boutique_id' => $boutique_id
                    ]
                ]);
            } else {
                throw new Exception('Failed to create bank information');
            }

        } catch (Exception $e) {
            error_log("Error in createBankInfo: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Server error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update bank information
     * PUT data: {boutique_id, beneficiary_name, bank_name, account_number, swift_code, bank_address}
     */
    private function updateBankInfo() {
        $input = json_decode(file_get_contents('php://input'), true);

        // Validate required fields
        $required = ['boutique_id', 'beneficiary_name', 'bank_name', 'account_number'];
        foreach ($required as $field) {
            if (empty($input[$field])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => "Field '$field' is required"
                ]);
                return;
            }
        }

        $boutique_id = intval($input['boutique_id']);
        $beneficiary_name = trim($input['beneficiary_name']);
        $bank_name = trim($input['bank_name']);
        $account_number = trim($input['account_number']);
        $swift_code = isset($input['swift_code']) ? trim($input['swift_code']) : null;
        $bank_address = isset($input['bank_address']) ? trim($input['bank_address']) : null;

        try {
            // Check if bank info exists
            $stmt = $this->conn->prepare("SELECT id FROM adjame_bank_info WHERE boutique_id = ?");
            $stmt->bind_param("i", $boutique_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'error' => 'Bank information not found for this boutique'
                ]);
                return;
            }

            // Update bank info
            $stmt = $this->conn->prepare("
                UPDATE adjame_bank_info
                SET beneficiary_name = ?,
                    bank_name = ?,
                    account_number = ?,
                    swift_code = ?,
                    bank_address = ?
                WHERE boutique_id = ?
            ");

            $stmt->bind_param(
                "sssssi",
                $beneficiary_name,
                $bank_name,
                $account_number,
                $swift_code,
                $bank_address,
                $boutique_id
            );

            $stmt->execute();

            if ($stmt->affected_rows >= 0) {  // >= 0 because might be 0 if no changes
                echo json_encode([
                    'success' => true,
                    'message' => 'Bank information updated successfully',
                    'data' => [
                        'boutique_id' => $boutique_id
                    ]
                ]);
            } else {
                throw new Exception('Failed to update bank information');
            }

        } catch (Exception $e) {
            error_log("Error in updateBankInfo: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Server error: ' . $e->getMessage()
            ]);
        }
    }
}

// Initialize and run
try {
    $api = new BankInfoAPI($conn);
    $api->handleRequest();
} catch (Exception $e) {
    error_log("Fatal error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Server error'
    ]);
}
```

## 3. API Endpoints

### Get Bank Info
```
GET /api_adjame/bank_info.php?action=get_by_boutique&boutique_id=10
```

Response (if exists):
```json
{
  "success": true,
  "exists": true,
  "data": {
    "id": 1,
    "boutique_id": 10,
    "beneficiary_name": "John Doe",
    "bank_name": "Bank of Africa",
    "account_number": "CI00 0000 0000 0000",
    "swift_code": "BOABCIAB",
    "bank_address": "123 Avenue Street, Abidjan",
    "created_at": "2025-01-07 12:00:00",
    "updated_at": "2025-01-07 12:00:00"
  }
}
```

Response (if doesn't exist):
```json
{
  "success": true,
  "exists": false,
  "data": null,
  "message": "No bank information found for this boutique"
}
```

### Create Bank Info
```
POST /api_adjame/bank_info.php?action=create
Content-Type: application/json

{
  "boutique_id": 10,
  "beneficiary_name": "John Doe",
  "bank_name": "Bank of Africa",
  "account_number": "CI00 0000 0000 0000",
  "swift_code": "BOABCIAB",
  "bank_address": "123 Avenue Street, Abidjan"
}
```

### Update Bank Info
```
PUT /api_adjame/bank_info.php?action=update
Content-Type: application/json

{
  "boutique_id": 10,
  "beneficiary_name": "John Doe Updated",
  "bank_name": "Bank of Africa",
  "account_number": "CI00 0000 0000 0000",
  "swift_code": "BOABCIAB",
  "bank_address": "456 New Street, Abidjan"
}
```

## 4. Testing

Test the API endpoints:

1. **Get bank info (empty):**
```bash
curl "https://sastock.com/api_adjame/bank_info.php?action=get_by_boutique&boutique_id=10"
```

2. **Create bank info:**
```bash
curl -X POST "https://sastock.com/api_adjame/bank_info.php?action=create" \
  -H "Content-Type: application/json" \
  -d '{
    "boutique_id": 10,
    "beneficiary_name": "John Doe",
    "bank_name": "Bank of Africa",
    "account_number": "CI00 0000 0000 0000",
    "swift_code": "BOABCIAB",
    "bank_address": "123 Avenue Street, Abidjan"
  }'
```

3. **Update bank info:**
```bash
curl -X PUT "https://sastock.com/api_adjame/bank_info.php?action=update" \
  -H "Content-Type: application/json" \
  -d '{
    "boutique_id": 10,
    "beneficiary_name": "John Doe Updated",
    "bank_name": "Ecobank",
    "account_number": "CI11 1111 1111 1111",
    "swift_code": "ECOCCIAB",
    "bank_address": "456 New Street, Abidjan"
  }'
```

## 5. Security Considerations

1. Add authentication middleware to verify user owns the boutique
2. Encrypt sensitive banking data at rest
3. Add rate limiting to prevent abuse
4. Log all changes to bank information for audit trail
5. Use HTTPS only for all requests

## 6. Future Enhancements

- Add email verification when bank info is changed
- Add approval workflow for bank info changes
- Support multiple bank accounts per boutique
- Add bank account verification via micro-deposits
