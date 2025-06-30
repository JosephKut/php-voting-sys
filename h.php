<?php
// Database configuration
$host = 'localhost';
$dbname = 'excel_import_db';
$username = 'root';
$password = 'JK';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Create logs table if it doesn't exist
$createLogTable = "
CREATE TABLE IF NOT EXISTS change_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    table_name VARCHAR(100) NOT NULL,
    record_id VARCHAR(50) NOT NULL,
    action_type ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL,
    old_values JSON NULL,
    new_values JSON NULL,
    user_id VARCHAR(50) NULL,
    user_ip VARCHAR(45) NULL,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    description TEXT NULL,
    INDEX idx_table_name (table_name),
    INDEX idx_action_type (action_type),
    INDEX idx_timestamp (timestamp),
    INDEX idx_user_id (user_id)
)";

$pdo->exec($createLogTable);

// Function to log database changes
function logDatabaseChange($pdo, $tableName, $recordId, $actionType, $oldValues = null, $newValues = null, $userId = null, $description = null) {
    $userIp = $_SERVER['REMOTE_ADDR'] ?? null;
    
    $sql = "INSERT INTO change_logs (table_name, record_id, action_type, old_values, new_values, user_id, user_ip, description) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $tableName,
        $recordId,
        $actionType,
        $oldValues ? json_encode($oldValues) : null,
        $newValues ? json_encode($newValues) : null,
        $userId,
        $userIp,
        $description
    ]);
    
    return $pdo->lastInsertId();
}

// Handle form submissions for creating sample logs
if ($_POST['action'] ?? '' === 'create_sample_log') {
    $sampleData = [
        'old_values' => ['name' => 'John Doe', 'email' => 'john@example.com', 'status' => 'active'],
        'new_values' => ['name' => 'John Smith', 'email' => 'john.smith@example.com', 'status' => 'active']
    ];
    
    logDatabaseChange(
        $pdo,
        $_POST['table_name'] ?? 'users',
        $_POST['record_id'] ?? '123',
        $_POST['action_type'] ?? 'UPDATE',
        $sampleData['old_values'],
        $sampleData['new_values'],
        $_POST['user_id'] ?? 'admin',
        $_POST['description'] ?? 'Sample log entry'
    );
    
    $message = "Sample log entry created successfully!";
}

// Get filter parameters
$filterTable = $_GET['filter_table'] ?? '';
$filterAction = $_GET['filter_action'] ?? '';
$filterUser = $_GET['filter_user'] ?? '';
$filterDateFrom = $_GET['filter_date_from'] ?? '';
$filterDateTo = $_GET['filter_date_to'] ?? '';
$search = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Build query with filters
$whereConditions = [];
$params = [];

if ($filterTable) {
    $whereConditions[] = "table_name = ?";
    $params[] = $filterTable;
}

if ($filterAction) {
    $whereConditions[] = "action_type = ?";
    $params[] = $filterAction;
}

if ($filterUser) {
    $whereConditions[] = "user_id LIKE ?";
    $params[] = "%$filterUser%";
}

if ($filterDateFrom) {
    $whereConditions[] = "DATE(timestamp) >= ?";
    $params[] = $filterDateFrom;
}

if ($filterDateTo) {
    $whereConditions[] = "DATE(timestamp) <= ?";
    $params[] = $filterDateTo;
}

if ($search) {
    $whereConditions[] = "(description LIKE ? OR record_id LIKE ? OR old_values LIKE ? OR new_values LIKE ?)";
    $searchTerm = "%$search%";
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
}

$whereClause = $whereConditions ? "WHERE " . implode(" AND ", $whereConditions) : "";

// Get total count for pagination
$countSql = "SELECT COUNT(*) FROM change_logs $whereClause";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalRecords = $countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $perPage);

// Get logs with pagination
$sql = "SELECT * FROM change_logs $whereClause ORDER BY timestamp DESC LIMIT ? OFFSET ?";
$params[] = $perPage;
$params[] = $offset;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get unique table names for filter dropdown
$tablesStmt = $pdo->query("SELECT DISTINCT table_name FROM change_logs ORDER BY table_name");
$tables = $tablesStmt->fetchAll(PDO::FETCH_COLUMN);

// Get unique users for filter dropdown
$usersStmt = $pdo->query("SELECT DISTINCT user_id FROM change_logs WHERE user_id IS NOT NULL ORDER BY user_id");
$users = $usersStmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Change Logs</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .header h1 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .stats {
            display: flex;
            gap: 20px;
            margin-top: 15px;
        }
        
        .stat-item {
            background: #3498db;
            color: white;
            padding: 10px 15px;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .controls {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .filters {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group label {
            font-weight: 600;
            margin-bottom: 5px;
            color: #555;
        }
        
        .form-group input,
        .form-group select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .search-box {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .search-box input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .btn {
            padding: 10px 20px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background: #2980b9;
        }
        
        .btn-secondary {
            background: #95a5a6;
        }
        
        .btn-secondary:hover {
            background: #7f8c8d;
        }
        
        .btn-success {
            background: #27ae60;
        }
        
        .btn-success:hover {
            background: #229f56;
        }
        
        .logs-table {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #555;
            position: sticky;
            top: 0;
        }
        
        tr:hover {
            background-color: #f5f5f5;
        }
        
        .action-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .action-insert {
            background: #d4edda;
            color: #155724;
        }
        
        .action-update {
            background: #fff3cd;
            color: #856404;
        }
        
        .action-delete {
            background: #f8d7da;
            color: #721c24;
        }
        
        .json-data {
            max-width: 300px;
            max-height: 100px;
            overflow: auto;
            background: #f8f9fa;
            padding: 8px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 12px;
            white-space: pre-wrap;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-top: 20px;
        }
        
        .pagination a,
        .pagination span {
            padding: 8px 12px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #333;
        }
        
        .pagination .current {
            background: #3498db;
            color: white;
            border-color: #3498db;
        }
        
        .pagination a:hover {
            background: #f5f5f5;
        }
        
        .create-log-form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .create-log-form h3 {
            margin-bottom: 15px;
            color: #2c3e50;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .message {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Database Change Logs</h1>
            <p>Monitor and track all database changes and transactions</p>
            <div class="stats">
                <div class="stat-item">Total Records: <?php echo number_format($totalRecords); ?></div>
                <div class="stat-item">Current Page: <?php echo $page; ?> of <?php echo $totalPages; ?></div>
            </div>
        </div>

        <?php if (isset($message)): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <div class="create-log-form">
            <h3>Create Sample Log Entry</h3>
            <form method="post">
                <input type="hidden" name="action" value="create_sample_log">
                <div class="form-row">
                    <div class="form-group">
                        <label>Table Name</label>
                        <input type="text" name="table_name" value="users" required>
                    </div>
                    <div class="form-group">
                        <label>Record ID</label>
                        <input type="text" name="record_id" value="<?php echo rand(100, 999); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Action Type</label>
                        <select name="action_type" required>
                            <option value="INSERT">INSERT</option>
                            <option value="UPDATE">UPDATE</option>
                            <option value="DELETE">DELETE</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>User ID</label>
                        <input type="text" name="user_id" value="admin">
                    </div>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <input type="text" name="description" value="Sample log entry for testing">
                </div>
                <button type="submit" class="btn btn-success">Create Sample Log</button>
            </form>
        </div>

        <div class="controls">
            <form method="get">
                <div class="filters">
                    <div class="form-group">
                        <label>Table Name</label>
                        <select name="filter_table">
                            <option value="">All Tables</option>
                            <?php foreach ($tables as $table): ?>
                                <option value="<?php echo htmlspecialchars($table); ?>" 
                                        <?php echo $filterTable === $table ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($table); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Action Type</label>
                        <select name="filter_action">
                            <option value="">All Actions</option>
                            <option value="INSERT" <?php echo $filterAction === 'INSERT' ? 'selected' : ''; ?>>INSERT</option>
                            <option value="UPDATE" <?php echo $filterAction === 'UPDATE' ? 'selected' : ''; ?>>UPDATE</option>
                            <option value="DELETE" <?php echo $filterAction === 'DELETE' ? 'selected' : ''; ?>>DELETE</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>User</label>
                        <select name="filter_user">
                            <option value="">All Users</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo htmlspecialchars($user); ?>" 
                                        <?php echo $filterUser === $user ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($user); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Date From</label>
                        <input type="date" name="filter_date_from" value="<?php echo htmlspecialchars($filterDateFrom); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Date To</label>
                        <input type="date" name="filter_date_to" value="<?php echo htmlspecialchars($filterDateTo); ?>">
                    </div>
                </div>
                
                <div class="search-box">
                    <input type="text" name="search" placeholder="Search in description, record ID, or data..." 
                           value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn">Filter</button>
                    <a href="?" class="btn btn-secondary">Clear</a>
                </div>
            </form>
        </div>

        <div class="logs-table">
            <?php if (empty($logs)): ?>
                <div class="no-data">
                    <h3>No logs found</h3>
                    <p>No database change logs match your current filters.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Timestamp</th>
                                <th>Table</th>
                                <th>Record ID</th>
                                <th>Action</th>
                                <th>User</th>
                                <th>IP Address</th>
                                <th>Old Values</th>
                                <th>New Values</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($log['id']); ?></td>
                                    <td><?php echo htmlspecialchars($log['timestamp']); ?></td>
                                    <td><?php echo htmlspecialchars($log['table_name']); ?></td>
                                    <td><?php echo htmlspecialchars($log['record_id']); ?></td>
                                    <td>
                                        <span class="action-badge action-<?php echo strtolower($log['action_type']); ?>">
                                            <?php echo htmlspecialchars($log['action_type']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($log['user_id'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($log['user_ip'] ?? 'N/A'); ?></td>
                                    <td>
                                        <?php if ($log['old_values']): ?>
                                            <div class="json-data"><?php echo htmlspecialchars(json_encode(json_decode($log['old_values']), JSON_PRETTY_PRINT)); ?></div>
                                        <?php else: ?>
                                            <em>N/A</em>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($log['new_values']): ?>
                                            <div class="json-data"><?php echo htmlspecialchars(json_encode(json_decode($log['new_values']), JSON_PRETTY_PRINT)); ?></div>
                                        <?php else: ?>
                                            <em>N/A</em>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($log['description'] ?? ''); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => 1])); ?>">First</a>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">Previous</a>
                        <?php endif; ?>

                        <?php
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);
                        
                        for ($i = $startPage; $i <= $endPage; $i++):
                        ?>
                            <?php if ($i === $page): ?>
                                <span class="current"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">Next</a>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $totalPages])); ?>">Last</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Auto-refresh functionality (optional)
        function autoRefresh() {
            if (confirm('Enable auto-refresh every 30 seconds?')) {
                setInterval(() => {
                    window.location.reload();
                }, 30000);
            }
        }
        
        // Add keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'f') {
                e.preventDefault();
                document.querySelector('input[name="search"]').focus();
            }
        });
        
        // Format JSON data for better readability
        document.querySelectorAll('.json-data').forEach(function(element) {
            try {
                const data = JSON.parse(element.textContent);
                element.textContent = JSON.stringify(data, null, 2);
            } catch (e) {
                // Leave as is if not valid JSON
            }
        });
    </script>
</body>
</html>

<?php
// Example usage functions for integration:

/*
// Example 1: Log an INSERT operation
function exampleInsert($pdo) {
    // Your insert query here
    $stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
    $stmt->execute(['John Doe', 'john@example.com']);
    $newId = $pdo->lastInsertId();
    
    // Log the change
    logDatabaseChange(
        $pdo,
        'users',
        $newId,
        'INSERT',
        null,
        ['name' => 'John Doe', 'email' => 'john@example.com'],
        $_SESSION['user_id'] ?? 'system',
        'New user registration'
    );
}

// Example 2: Log an UPDATE operation
function exampleUpdate($pdo, $userId) {
    // Get old values first
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $oldValues = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Perform update
    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
    $stmt->execute(['Jane Doe', 'jane@example.com', $userId]);
    
    // Log the change
    logDatabaseChange(
        $pdo,
        'users',
        $userId,
        'UPDATE',
        $oldValues,
        ['name' => 'Jane Doe', 'email' => 'jane@example.com'],
        $_SESSION['user_id'] ?? 'system',
        'Profile update'
    );
}

// Example 3: Log a DELETE operation
function exampleDelete($pdo, $userId) {
    // Get values before deletion
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $oldValues = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Perform deletion
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    
    // Log the change
    logDatabaseChange(
        $pdo,
        'users',
        $userId,
        'DELETE',
        $oldValues,
        null,
        $_SESSION['user_id'] ?? 'system',
        'User account deleted'
    );
}
*/
?>