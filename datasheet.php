<?php
// Main processing logic
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if ($_SERVER['REQUEST_METHOD'] != 'POST'){
        header("location: index.php");
    }
if ($_GET['access'] !== $_SESSION['access']){
    header("location: index.php");
    die();
}
if (!$_SESSION['Email']){
    header("location: index.php");
    session_destroy();
    die();    
}
include 'connect.php';
// config.php - Database configuration
class DatabaseConfig {
    private $dbhost ;   // 'localhost';
    private $username;  // 'root';
    private $password ; // 'JK';
    private $database ; // 'umat_src_poll';
    public $conn;

    public function __construct() {
        global $dbs;
        global $pass;
        global $user;
        global $host;
        $this->database = $dbs;
        $this->password = $pass;
        $this->username = $user;
        $this->dbhost = $host;
        try {
            $this->conn = new PDO("mysql:host={$this->dbhost};dbname={$this->database}", 
                                 $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
}

// ExcelImporter.php - Enhanced class for handling Excel imports
require_once 'vendor/autoload.php'; // You'll need to install PhpSpreadsheet via Composer

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ExcelImporter {
    private $db;
    private $allowedExtensions = ['xlsx', 'xls', 'csv'];
    private $maxFileSize = 50 * 1024 * 1024; // 50MB
    private $requiredEmailDomain = '@st.umat.edu.gh'; // Required email domain

    public function __construct() {
        $this->db = new DatabaseConfig();
        $this->createTable();
    }

    // Create the table if it doesn't exist
    private function createTable() {
        $sql = "CREATE TABLE IF NOT EXISTS voters (
            Index_No VARCHAR(17) PRIMARY KEY,
            Last_Name VARCHAR(255) NOT NULL,
            Other_Name VARCHAR(255),
            Student_Email VARCHAR(255) NOT NULL UNIQUE,
            Programme VARCHAR(255) NOT NULL,
            Tel VARCHAR(20) NOT NULL,
            date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            import_batch VARCHAR(50),
            INDEX idx_date_created (date_created),
            INDEX idx_import_batch (import_batch)
        )";
        
        try {
            $this->db->conn->exec($sql);
        } catch(PDOException $e) {
            throw new Exception("Error creating table: " . $e->getMessage());
        }
    }

    // Validate uploaded file
    public function validateFile($file) {
        $errors = [];

        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "No file uploaded or upload error occurred.";
            return $errors;
        }

        if ($file['size'] > $this->maxFileSize) {
            $errors[] = "File size exceeds the maximum limit of 50MB.";
        }

        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $this->allowedExtensions)) {
            $errors[] = "Invalid file type. Only Excel (.xlsx, .xls) and CSV files are allowed.";
        }

        return $errors;
    }

    // Preview Excel file data before importing
    public function previewExcelFile($filePath) {
        try {
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            
            $data = [];
            $errors = [];
            $duplicates = [];
            
            // Get existing Index_No values for duplicate checking
            $existingIndexes = $this->getExistingIndexNumbers();

            // Read data starting from row 2 (assuming row 1 has headers)
            for ($row = 2; $row <= $highestRow; $row++) {
                $rowData = [
                    'row_number' => $row,
                    'Index_No' => trim($worksheet->getCell('A' . $row)->getCalculatedValue()),
                    'Last_Name' => trim($worksheet->getCell('B' . $row)->getCalculatedValue()),
                    'Other_Name' => trim($worksheet->getCell('C' . $row)->getCalculatedValue()),
                    'Student_Email' => trim($worksheet->getCell('D' . $row)->getCalculatedValue()),
                    'Programme' => trim($worksheet->getCell('E' . $row)->getCalculatedValue()),
                    'Tel' => trim($worksheet->getCell('F' . $row)->getCalculatedValue()),
                    'status' => 'valid'
                ];

                // Skip completely empty rows
                if (empty($rowData['Index_No']) && empty($rowData['Last_Name']) && 
                    empty($rowData['Student_Email']) && empty($rowData['Programme']) && 
                    empty($rowData['Tel'])) {
                    continue;
                }

                // Validate required fields
                $rowErrors = [];
                if (empty($rowData['Index_No'])) $rowErrors[] = 'Index_No is required';
                if (empty($rowData['Last_Name'])) $rowErrors[] = 'Last_Name is required';
                if (empty($rowData['Student_Email'])) $rowErrors[] = 'Student_Email is required';
                if (empty($rowData['Programme'])) $rowErrors[] = 'Programme is required';
                if (empty($rowData['Tel'])) $rowErrors[] = 'Tel is required';
                
                // Validate email format and domain
                if (!empty($rowData['Student_Email'])) {
                    if (!filter_var($rowData['Student_Email'], FILTER_VALIDATE_EMAIL)) {
                        $rowErrors[] = 'Invalid email format';
                    } elseif (!$this->validateEmailDomain($rowData['Student_Email'])) {
                        $rowErrors[] = 'Email must end with ' . $this->requiredEmailDomain;
                    }
                }

                // Check for duplicates in database
                if (in_array($rowData['Index_No'], $existingIndexes)) {
                    $rowErrors[] = 'Index_No already exists in database';
                    $duplicates[] = $rowData['Index_No'];
                }

                if (!empty($rowErrors)) {
                    $rowData['status'] = 'error';
                    $rowData['errors'] = implode(', ', $rowErrors);
                    $errors[] = "Row {$row}: " . implode(', ', $rowErrors);
                }

                $data[] = $rowData;
            }

            return [
                'data' => $data,
                'errors' => $errors,
                'duplicates' => $duplicates,
                'total_rows' => count($data),
                'valid_rows' => count(array_filter($data, function($row) { return $row['status'] === 'valid'; })),
                'error_rows' => count(array_filter($data, function($row) { return $row['status'] === 'error'; }))
            ];

        } catch (Exception $e) {
            throw new Exception("Error previewing Excel file: " . $e->getMessage());
        }
    }

    // Validate email domain
    private function validateEmailDomain($email) {
        return str_ends_with(strtolower($email), strtolower($this->requiredEmailDomain));
    }

    // Get existing Index_No values from database
    private function getExistingIndexNumbers() {
        $sql = "SELECT Index_No FROM voters";
        $stmt = $this->db->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Process confirmed data for import
    public function importConfirmedData($data, $selectedRows = []) {
        try {
            $batchId = uniqid('batch_');
            $dataToImport = [];

            foreach ($data as $index => $row) {
                // If specific rows are selected, only import those
                if (!empty($selectedRows) && !in_array($index, $selectedRows)) {
                    continue;
                }
                
                // Only import valid rows
                if ($row['status'] === 'valid') {
                    $dataToImport[] = [
                        'Index_No' => $row['Index_No'],
                        'Last_Name' => $row['Last_Name'],
                        'Other_Name' => $row['Other_Name'],
                        'Student_Email' => $row['Student_Email'],
                        'Programme' => $row['Programme'],
                        'Tel' => $row['Tel'],
                        'batch_id' => $batchId
                    ];
                }
            }

            return $this->insertDataToDatabase($dataToImport);

        } catch (Exception $e) {
            throw new Exception("Error importing confirmed data: " . $e->getMessage());
        }
    }

    // Insert data into database
    private function insertDataToDatabase($data) {
        $sql = "INSERT INTO voters (Index_No, Last_Name, Other_Name, Student_Email, Programme, Tel, date_created, import_batch) 
              VALUES (:index_no, :last_name, :other_name, :student_email, :programme, :tel, :date_created, :batch_id)";

        try {
            $this->db->conn->beginTransaction();
            $stmt = $this->db->conn->prepare($sql);
            
            $insertedCount = 0;
            $currentDateTime = date('Y-m-d H:i:s');
            
            foreach ($data as $row) {
                $stmt->execute([
                    ':index_no' => $row['Index_No'],
                    ':last_name' => $row['Last_Name'],
                    ':other_name' => $row['Other_Name'],
                    ':student_email' => $row['Student_Email'],
                    ':programme' => $row['Programme'],
                    ':tel' => $row['Tel'],
                    ':date_created' => $currentDateTime,
                    ':batch_id' => $row['batch_id']
                ]);
                $insertedCount++;
            }
            
            $this->db->conn->commit();
            return $insertedCount;
            
        } catch (PDOException $e) {
            $this->db->conn->rollBack();
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    // Get imported data for display
    public function getImportedData($limit = 100) {
        $sql = "SELECT * FROM voters ORDER BY date_created DESC LIMIT :limit";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get import batches with dates
    public function getImportBatches() {
        $sql = "SELECT 
                    import_batch, 
                    DATE(date_created) as import_date,
                    MIN(date_created) as first_import,
                    MAX(date_created) as last_import,
                    COUNT(*) as record_count
                FROM voters 
                WHERE import_batch IS NOT NULL 
                GROUP BY import_batch, DATE(date_created)
                ORDER BY first_import DESC";
        
        $stmt = $this->db->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Delete records by date range
    public function deleteRecordsByDateRange($startDate, $endDate) {
        $sql = "DELETE FROM voters 
                WHERE DATE(date_created) BETWEEN :start_date AND :end_date";
        
        try {
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute([
                ':start_date' => $startDate,
                ':end_date' => $endDate
            ]);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new Exception("Error deleting records: " . $e->getMessage());
        }
    }

    // Delete records by specific batch
    public function deleteRecordsByBatch($batchId) {
        $sql = "DELETE FROM voters WHERE import_batch = :batch_id";
        
        try {
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute([':batch_id' => $batchId]);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new Exception("Error deleting batch records: " . $e->getMessage());
        }
    }

    // Get records by date for preview before deletion
    public function getRecordsByDateRange($startDate, $endDate) {
        $sql = "SELECT * FROM voters 
                WHERE DATE(date_created) BETWEEN :start_date AND :end_date
                ORDER BY date_created DESC";
        
        $stmt = $this->db->conn->prepare($sql);
        $stmt->execute([
            ':start_date' => $startDate,
            ':end_date' => $endDate
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

$importer = new ExcelImporter();
$message = '';
$messageType = '';
$previewData = null;
$showPreview = false;

// Handle file upload and preview
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file']) && !isset($_POST['confirm_import'])) {
    try {
        $errors = $importer->validateFile($_FILES['excel_file']);
        
        if (empty($errors)) {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileName = time() . '_' . $_FILES['excel_file']['name'];
            $filePath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['excel_file']['tmp_name'], $filePath)) {
                $previewData = $importer->previewExcelFile($filePath);
                $_SESSION['temp_file'] = $filePath;
                $_SESSION['preview_data'] = $previewData;
                $showPreview = true;
                
                $message = "File uploaded and analyzed. Please review the data below before importing.";
                $messageType = 'info';
            } else {
                $message = "Failed to upload file.";
                $messageType = 'error';
            }
        } else {
            $message = implode('<br>', $errors);
            $messageType = 'error';
        }
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        $messageType = 'error';
    }
}

// Handle confirmed import
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_import'])) {
    try {
        if (isset($_SESSION['preview_data'])) {
            $selectedRows = isset($_POST['selected_rows']) ? $_POST['selected_rows'] : [];
            $insertedCount = $importer->importConfirmedData($_SESSION['preview_data']['data'], $selectedRows);
            
            // Clean up
            if (isset($_SESSION['temp_file']) && file_exists($_SESSION['temp_file'])) {
                unlink($_SESSION['temp_file']);
            }
            unset($_SESSION['temp_file'], $_SESSION['preview_data']);
            
            $message = "Successfully imported {$insertedCount} records!";
            $messageType = 'success';
        } else {
            $message = "No preview data found. Please upload a file first.";
            $messageType = 'error';
        }
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        $messageType = 'error';
    }
}

// Handle deletion by date range
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_by_date'])) {
    try {
        $startDate = $_POST['start_date'];
        $endDate = $_POST['end_date'];
        
        if (!empty($startDate) && !empty($endDate)) {
            $deletedCount = $importer->deleteRecordsByDateRange($startDate, $endDate);
            $message = "Successfully deleted {$deletedCount} records from {$startDate} to {$endDate}.";
            $messageType = 'success';
        } else {
            $message = "Please provide both start and end dates.";
            $messageType = 'error';
        }
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        $messageType = 'error';
    }
}

// Handle deletion by batch
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_batch'])) {
    try {
        $batchId = $_POST['batch_id'];
        $deletedCount = $importer->deleteRecordsByBatch($batchId);
        $message = "Successfully deleted {$deletedCount} records from batch {$batchId}.";
        $messageType = 'success';
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        $messageType = 'error';
    }
}

// Get recent data and batches for display
$recentData = $importer->getImportedData(20);
$importBatches = $importer->getImportBatches();

// Check if we should show preview from session
if (isset($_SESSION['preview_data']) && !$showPreview) {
    $previewData = $_SESSION['preview_data'];
    $showPreview = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href= "admin.css"> -->
    <title>Enhanced Excel to Database Import System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            /* display: flex; */
            /* flex-direction: column; */
            min-height: 100vh;
            width: 100%;
            background: url(images/aa.jpg);
            background-repeat: repeat;
            background-attachment: fixed;
            top: 30px;
            background-position: left top -60px;
            background-size: cover;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: linear-gradient(135deg,rgb(102, 186, 234) 0%,rgb(75, 110, 162) 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .upload-section {
            border: 3px dashed #ddd;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            transition: border-color 0.3s;
        }
        
        .upload-section:hover {
            border-color: #667eea;
        }
        
        .file-input {
            margin: 20px 0;
        }
        
        .file-input input[type="file"] {
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 6px;
            width: 100%;
            max-width: 400px;
            font-size: 14px;
        }
        
        .btn {
            background: linear-gradient(135deg,rgb(102, 186, 234) 0%,rgb(75, 110, 162) 100%);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: transform 0.2s;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #2ed573 0%, #1e90ff 100%);
        }
        
        .message {
            padding: 15px;
            margin: 20px 0;
            border-radius: 6px;
            font-weight: 500;
        }
        
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        .message.info {
            background-color: #d1ecf1;
            color: #0c5460;
            border-left: 4px solid #17a2b8;
        }
        
        .preview-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 14px;
        }
        
        .preview-table th,
        .preview-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .preview-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            position: sticky;
            top: 0;
        }
        
        .preview-table tr:hover {
            background-color: #f5f5f5;
        }
        
        .error-row {
            background-color: #fff5f5 !important;
        }
        
        .valid-row {
            background-color: #f0fff4 !important;
        }
        
        .summary-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            border-left: 4px solid #667eea;
        }
        
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #667eea;
        }
        
        .stat-label {
            color: #666;
            margin-top: 5px;
        }
        
        .checkbox-column {
            width: 40px;
        }
        
        .section-title {
            color:rgb(75, 110, 162);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }
        
        .delete-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #555;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        
        .batch-list {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 10px;
        }
        
        .batch-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .batch-item:last-child {
            border-bottom: none;
        }
        
        .instructions {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .instructions h3 {
            color: #495057;
            margin-bottom: 15px;
        }
        
        .instructions ul {
            padding-left: 20px;
        }
        
        .instructions li {
            margin-bottom: 8px;
            color: #6c757d;
        }
        
        @media (max-width: 768px) {
            .delete-section {
                grid-template-columns: 1fr;
            }
            
            .container {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Enhanced Excel Import System</h1>
            <p>Import voter data with preview and management capabilities</p>
        </div>
        
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <!-- Upload Section -->
        <div class="card">
            <h2 class="section-title">Upload Excel File</h2>
            
            <div class="instructions">
                <h3>Instructions:</h3>
                <ul>
                    <li>Prepare your Excel file with headers: Index_No, Last_Name, Other_Name, Student_Email, Programme, Tel</li>
                    <li>Supported formats: .xlsx, .xls, .csv</li>
                    <li>Maximum file size: 50MB</li>
                    <li>You'll be able to preview and select specific rows before importing</li>
                    <li>Duplicate Index_No values will be highlighted</li>
                </ul>
            </div>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="upload-section">
                    <h3>Select File to Upload</h3>
                    <div class="file-input">
                        <input type="file" name="excel_file" accept=".xlsx,.xls,.csv" required>
                    </div>
                    <button type="submit" class="btn">Upload & Preview</button>
                </div>
            </form>
        </div>
        
        <!-- Preview Section -->
        <?php if ($showPreview && $previewData): ?>
        <div class="card">
            <h2 class="section-title">Data Preview</h2>
            
            <div class="summary-stats">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $previewData['total_rows']; ?></div>
                    <div class="stat-label">Total Rows</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $previewData['valid_rows']; ?></div>
                    <div class="stat-label">Valid Rows</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $previewData['error_rows']; ?></div>
                    <div class="stat-label">Error Rows</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($previewData['duplicates']); ?></div>
                    <div class="stat-label">Duplicates</div>
                </div>
            </div>
            
            <?php if (!empty($previewData['errors'])): ?>
                <div class="message error">
                    <strong>Validation Errors:</strong><br>
                    <?php echo implode('<br>', array_slice($previewData['errors'], 0, 10)); ?>
                    <?php if (count($previewData['errors']) > 10): ?>
                        <br><em>... and <?php echo count($previewData['errors']) - 10; ?> more errors</em>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <input type="hidden" name="confirm_import" value="1">
                
                <div style="margin: 20px 0;">
                    <label>
                        <input type="checkbox" id="select-all"> Select All Valid Rows
                    </label>
                    <button type="submit" class="btn btn-success" style="margin-left: 20px;">
                        Import Selected Rows
                    </button>
                </div>
                
                <div style="max-height: 500px; overflow-y: auto;">
                    <table class="preview-table">
                        <thead>
                            <tr>
                                <th class="checkbox-column">Select</th>
                                <th>Row</th>
                                <th>Index_No</th>
                                <th>Last_Name</th>
                                <th>Other_Name</th>
                                <th>Student_Email</th>
                                <th>Programme</th>
                                <th>Tel</th>
                                <th>Status</th>
                                <th>Errors</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($previewData['data'] as $index => $row): ?>
                            <tr class="<?php echo $row['status'] === 'valid' ? 'valid-row' : 'error-row'; ?>">
                                <td>
                                    <?php if ($row['status'] === 'valid'): ?>
                                        <input type="checkbox" name="selected_rows[]" value="<?php echo $index; ?>" class="row-checkbox">
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $row['row_number']; ?></td>
                                <td><?php echo htmlspecialchars($row['Index_No']); ?></td>
                                <td><?php echo htmlspecialchars($row['Last_Name']); ?></td>
                                <td><?php echo htmlspecialchars($row['Other_Name']); ?></td>
                                <td><?php echo htmlspecialchars($row['Student_Email']); ?></td>
                                <td><?php echo htmlspecialchars($row['Programme']); ?></td>
                                <td><?php echo htmlspecialchars($row['Tel']); ?></td>
                                <td>
                                    <span style="color: <?php echo $row['status'] === 'valid' ? 'green' : 'red'; ?>">
                                        <?php echo ucfirst($row['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo isset($row['errors']) ? htmlspecialchars($row['errors']) : ''; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
        <?php endif; ?>
        
        <!-- Delete Section -->
        <div class="card">
            <h2 class="section-title">Delete Records</h2>
            
            <div class="delete-section">
                <!-- Delete by Date Range -->
                <div>
                    <h3>Delete by Date Range</h3>
                    <form method="POST">
                        <div class="form-group">
                            <label for="start_date">Start Date:</label>
                            <input type="date" id="start_date" name="start_date" required>
                        </div>
                        <div class="form-group">
                            <label for="end_date">End Date:</label>
                            <input type="date" id="end_date" name="end_date" required>
                        </div>
                        <button type="submit" name="delete_by_date" class="btn btn-danger" 
                                onclick="return confirm('Are you sure you want to delete records in this date range?')">
                            Delete Records by Date
                        </button>
                    </form>
                </div>
                
                <!-- Delete by Batch -->
                <div>
                    <h3>Delete by Import Batch</h3>
                    <?php if (!empty($importBatches)): ?>
                        <div class="batch-list">
                            <?php foreach ($importBatches as $batch): ?>
                            <div class="batch-item">
                                <div>
                                    <strong>Batch:</strong> <?php echo htmlspecialchars($batch['import_batch']); ?><br>
                                    <small>Date: <?php echo $batch['import_date']; ?> | Records: <?php echo $batch['record_count']; ?></small>
                                </div>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="batch_id" value="<?php echo htmlspecialchars($batch['import_batch']); ?>">
                                    <button type="submit" name="delete_batch" class="btn btn-danger" 
                                            onclick="return confirm('Are you sure you want to delete this batch?')"
                                            style="padding: 5px 10px; font-size: 12px;">
                                        Delete
                                    </button>
                                </form>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>No import batches found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Handle select all functionality
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.row-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
        
        // Update select all when individual checkboxes change
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('row-checkbox')) {
                const allCheckboxes = document.querySelectorAll('.row-checkbox');
                const checkedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
                const selectAllCheckbox = document.getElementById('select-all');
                
                if (checkedCheckboxes.length === 0) {
                    selectAllCheckbox.indeterminate = false;
                    selectAllCheckbox.checked = false;
                } else if (checkedCheckboxes.length === allCheckboxes.length) {
                    selectAllCheckbox.indeterminate = false;
                    selectAllCheckbox.checked = true;
                } else {
                    selectAllCheckbox.indeterminate = true;
                }
            }
        });
        
        // Set default dates for delete functionality
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            
            if (startDateInput && !startDateInput.value) {
                startDateInput.value = today;
            }
            if (endDateInput && !endDateInput.value) {
                endDateInput.value = today;
            }
        });
    </script>
</body>
</html>