# Agent System Implementation Guide

## 1. Database Migration

Execute this SQL command on your MySQL database:

```sql
ALTER TABLE `adjame_users`
ADD COLUMN `agent` VARCHAR(50) DEFAULT NULL COMMENT 'NULL or empty = admin/owner, "agent" = agent'
AFTER `phone`;
```

## 2. Backend API Implementation (users.php)

### Add the `addAgent()` function

Add this private function to your `users.php` API file:

```php
/**
 * Add a new agent to a boutique
 * Expected POST data:
 * {
 *   "full_name": "Agent Name",
 *   "email": "agent@example.com",
 *   "phone": "+225...",
 *   "password": "password123",
 *   "agent": "agent" or "" (empty for admin),
 *   "boutique_id": 123
 * }
 */
private function addAgent() {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    // Validate required fields
    $required = ['full_name', 'email', 'password', 'boutique_id'];
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

    $full_name = trim($input['full_name']);
    $email = trim($input['email']);
    $phone = isset($input['phone']) ? trim($input['phone']) : '';
    $password = $input['password'];
    $agent = isset($input['agent']) && $input['agent'] === 'agent' ? 'agent' : NULL;
    $boutique_id = intval($input['boutique_id']);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Invalid email format'
        ]);
        return;
    }

    try {
        // Check if email already exists
        $stmt = $this->conn->prepare("SELECT id FROM adjame_users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            http_response_code(409);
            echo json_encode([
                'success' => false,
                'error' => 'An account with this email already exists'
            ]);
            return;
        }

        // Check if boutique exists
        $stmt = $this->conn->prepare("SELECT id, name FROM adjame_boutique WHERE id = ?");
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

        $boutique = $result->fetch_assoc();

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Generate email verification token
        $verification_token = bin2hex(random_bytes(32));

        // Start transaction
        $this->conn->begin_transaction();

        try {
            // Insert user
            $stmt = $this->conn->prepare("
                INSERT INTO adjame_users (
                    email,
                    password,
                    full_name,
                    phone,
                    agent,
                    email_verification_token,
                    is_active
                ) VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $is_active = 1; // Active by default
            $stmt->bind_param(
                "ssssssi",
                $email,
                $hashed_password,
                $full_name,
                $phone,
                $agent,
                $verification_token,
                $is_active
            );
            $stmt->execute();

            if ($stmt->affected_rows === 0) {
                throw new Exception('Failed to create user');
            }

            $user_id = $this->conn->insert_id;

            // Associate user with boutique
            $role = 'staff'; // Agents are staff members
            $permissions = json_encode([
                'view_products' => true,
                'manage_orders' => true,
                'manage_inventory' => true,
                'view_reports' => false,
                'manage_users' => false,
                'manage_settings' => false
            ]);

            $stmt = $this->conn->prepare("
                INSERT INTO adjame_user_boutique (
                    user_id,
                    boutique_id,
                    role,
                    permissions,
                    is_active
                ) VALUES (?, ?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "iissi",
                $user_id,
                $boutique_id,
                $role,
                $permissions,
                $is_active
            );
            $stmt->execute();

            if ($stmt->affected_rows === 0) {
                throw new Exception('Failed to associate user with boutique');
            }

            // Commit transaction
            $this->conn->commit();

            // Send verification email (optional)
            $this->sendVerificationEmail($email, $full_name, $verification_token, $boutique['name']);

            // Send welcome email with credentials
            $this->sendWelcomeEmail($email, $full_name, $password, $boutique['name']);

            // Return success response
            echo json_encode([
                'success' => true,
                'message' => $agent === 'agent'
                    ? 'Agent successfully added to boutique'
                    : 'Admin successfully added to boutique',
                'data' => [
                    'user_id' => $user_id,
                    'email' => $email,
                    'full_name' => $full_name,
                    'agent' => $agent,
                    'boutique_id' => $boutique_id,
                    'boutique_name' => $boutique['name']
                ]
            ]);

        } catch (Exception $e) {
            // Rollback transaction
            $this->conn->rollback();
            throw $e;
        }

    } catch (Exception $e) {
        error_log("Error in addAgent: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Server error: ' . $e->getMessage()
        ]);
    }
}

/**
 * Send verification email to new user
 */
private function sendVerificationEmail($email, $full_name, $token, $boutique_name) {
    // You can use PHPMailer or your existing email service
    // This is a placeholder implementation
    $verification_link = "https://sastock.com/verify-email?token=" . $token;

    // TODO: Implement email sending
    error_log("Send verification email to $email: $verification_link");
}

/**
 * Send welcome email with credentials
 */
private function sendWelcomeEmail($email, $full_name, $password, $boutique_name) {
    // Send email with login credentials
    // TODO: Implement email sending
    error_log("Send welcome email to $email with password: $password");
}
```

### Modify the `handlePost()` function

Add this case to your switch statement in `handlePost()`:

```php
public function handlePost() {
    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'add_agent':  // NEW CASE
            $this->addAgent();
            break;

        // ... other existing cases

        default:
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Invalid action'
            ]);
    }
}
```

### Modify the `getUsersByBoutique()` function

Update the SELECT query to include the `agent` field:

```php
private function getUsersByBoutique() {
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
        // Get users associated with this boutique
        $stmt = $this->conn->prepare("
            SELECT
                u.id AS user_id,
                u.full_name,
                u.email,
                u.phone,
                u.agent,  -- ADDED THIS FIELD
                u.is_active,
                ub.role,
                ub.permissions,
                ub.created_at,
                b.id AS boutique_id,
                b.name AS boutique_name
            FROM adjame_users u
            INNER JOIN adjame_user_boutique ub ON u.id = ub.user_id
            INNER JOIN adjame_boutique b ON ub.boutique_id = b.id
            WHERE ub.boutique_id = ?
            ORDER BY ub.created_at DESC
        ");

        $stmt->bind_param("i", $boutique_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = [
                'user_id' => $row['user_id'],
                'full_name' => $row['full_name'],
                'email' => $row['email'],
                'phone' => $row['phone'],
                'agent' => $row['agent'],  // ADDED THIS FIELD
                'active' => (bool)$row['is_active'],
                'role' => $row['role'],
                'permissions' => $row['permissions'],
                'created_at' => $row['created_at']
            ];
        }

        // Get boutique info
        $stmt = $this->conn->prepare("SELECT id, name FROM adjame_boutique WHERE id = ?");
        $stmt->bind_param("i", $boutique_id);
        $stmt->execute();
        $boutique_result = $stmt->get_result();
        $boutique = $boutique_result->fetch_assoc();

        echo json_encode([
            'success' => true,
            'data' => [
                'boutique' => $boutique,
                'users' => $users
            ]
        ]);

    } catch (Exception $e) {
        error_log("Error in getUsersByBoutique: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Server error'
        ]);
    }
}
```

### Note about the `login()` function

The `login()` function already returns ALL fields from the `adjame_users` table, so it will automatically include the `agent` field once you add it to the database. No changes are needed to the login function.

When a user logs in, the response will look like:

```json
{
  "success": true,
  "data": {
    "id": 123,
    "email": "user@example.com",
    "full_name": "John Doe",
    "phone": "+225...",
    "agent": "agent",  // or null for admins
    "boutiques": [...]
  }
}
```

## 3. Frontend Implementation

The frontend has been updated in `Parametre-management.vue`:

### Features Added:
1. ✅ New column "Type" in users table showing "Admin" or "Agent"
2. ✅ Updated "Add User" modal with fields:
   - Full name (required)
   - Email (required)
   - Phone (optional)
   - Password (required)
   - User Type dropdown (Admin/Agent)
3. ✅ Modified `addUser()` function to call API endpoint
4. ✅ Badge styling: Purple for Admin, Blue for Agent

## 4. Testing

### Test Case 1: Add an Admin
1. Go to Parameters page
2. Click "Add User"
3. Fill in:
   - Name: "Test Admin"
   - Email: "admin@test.com"
   - Password: "Admin123"
   - User Type: "Admin (Owner)"
4. Click "Add"
5. Verify user appears with "Admin" badge (purple)

### Test Case 2: Add an Agent
1. Click "Add User"
2. Fill in:
   - Name: "Test Agent"
   - Email: "agent@test.com"
   - Password: "Agent123"
   - User Type: "Agent (Staff Member)"
3. Click "Add"
4. Verify user appears with "Agent" badge (blue)

### Test Case 3: Login as Agent
1. Logout current user
2. Login with agent credentials
3. Verify the user data includes `"agent": "agent"`
4. Verify agent can access the same boutique

## 5. Deployment Checklist

- [ ] Execute SQL migration to add `agent` column
- [ ] Upload updated `users.php` to server
- [ ] Deploy updated `Parametre-management.vue` frontend
- [ ] Test adding admin users
- [ ] Test adding agent users
- [ ] Test login for both admin and agent
- [ ] Verify permissions work correctly
- [ ] Test email notifications (if implemented)

## 6. Security Notes

⚠️ **Important Security Considerations:**

1. The password is sent in plain text to the API - ensure you're using HTTPS
2. Consider implementing password strength validation on backend
3. Consider sending a password reset link instead of the actual password in the welcome email
4. Implement rate limiting on the add_agent endpoint to prevent abuse
5. Add CSRF token validation for POST requests
6. Consider implementing 2FA for admin accounts

## 7. Future Enhancements

- Add ability to edit user permissions individually
- Add ability to deactivate users instead of deleting
- Add audit log for user actions
- Add role-based UI restrictions (hide features agents can't access)
- Add bulk user import functionality
- Add user activity tracking
