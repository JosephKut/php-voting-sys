<?php
    include 'connect.php';
    include 'resources.php';

    // Check if this is an AJAX request
    $isAjax = isset($_GET['ajax']) && $_GET['ajax'] === '1';
    
    // Handle delete operation
    if (isset($_POST['delete_voter']) && isset($_POST['voter_id'])) {
        $voter_id = $_POST['voter_id'];
        $deleteQuery = "DELETE FROM voters WHERE id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("i", $voter_id);
        
        if ($stmt->execute()) {
            if ($isAjax) {
                echo json_encode(['success' => true, 'message' => 'Voter record deleted successfully!']);
                exit;
            }
            $delete_success = true;
        } else {
            if ($isAjax) {
                echo json_encode(['success' => false, 'message' => 'Error deleting voter record. Please try again.']);
                exit;
            }
            $delete_error = true;
        }
        $stmt->close();
    }

    // Function to get voters data
    function getVotersData($conn, $current_page = 1, $records_per_page = 50, $search_params = []) {
        $offset = ($current_page - 1) * $records_per_page;
        
        // Build the query with search conditions
        $whereConditions = ["1=1"];
        $params = [];
        $types = "";
        
        // Search by name
        if (!empty($search_params['search_name'])) {
            $whereConditions[] = "(Last_Name LIKE ? OR Other_Name LIKE ?)";
            $searchName = "%" . $search_params['search_name'] . "%";
            $params[] = $searchName;
            $params[] = $searchName;
            $types .= "ss";
        }
        
        // Search by index number
        if (!empty($search_params['search_index'])) {
            $whereConditions[] = "Index_No LIKE ?";
            $params[] = "%" . $search_params['search_index'] . "%";
            $types .= "s";
        }
        
        // Search by programme
        if (!empty($search_params['search_programme'])) {
            $whereConditions[] = "Programme = ?";
            $params[] = $search_params['search_programme'];
            $types .= "s";
        }
        
        // Search by date
        if (!empty($search_params['search_date'])) {
            $whereConditions[] = "DATE(date_created) = ?";
            $params[] = $search_params['search_date'];
            $types .= "s";
        }
        
        // Get total count for pagination
        $countQuery = "SELECT COUNT(*) as total FROM voters WHERE " . implode(" AND ", $whereConditions);
        if (!empty($params)) {
            $countStmt = $conn->prepare($countQuery);
            $countStmt->bind_param($types, ...$params);
            $countStmt->execute();
            $countResult = $countStmt->get_result();
            $totalRecords = $countResult->fetch_assoc()['total'];
            $countStmt->close();
        } else {
            $countResult = $conn->query($countQuery);
            $totalRecords = $countResult->fetch_assoc()['total'];
        }
        
        $totalPages = ceil($totalRecords / $records_per_page);
        
        // Main query with pagination
        $getVoters = "SELECT * FROM voters WHERE " . implode(" AND ", $whereConditions) . " ORDER BY date_created DESC LIMIT ? OFFSET ?";
        $paginationParams = $params;
        $paginationParams[] = $records_per_page;
        $paginationParams[] = $offset;
        $paginationTypes = $types . "ii";
        
        if (!empty($params)) {
            $stmt = $conn->prepare($getVoters);
            $stmt->bind_param($paginationTypes, ...$paginationParams);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $getVoters = "SELECT * FROM voters ORDER BY date_created DESC LIMIT $records_per_page OFFSET $offset";
            $result = $conn->query($getVoters);
        }
        
        $voters = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $voters[] = $row;
            }
        }
        
        return [
            'voters' => $voters,
            'totalRecords' => $totalRecords,
            'totalPages' => $totalPages,
            'currentPage' => $current_page,
            'offset' => $offset,
            'records_per_page' => $records_per_page
        ];
    }

    // Pagination settings
    $records_per_page = 50;
    $current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    
    // Get search parameters
    $search_params = [
        'search_name' => $_GET['search_name'] ?? '',
        'search_index' => $_GET['search_index'] ?? '',
        'search_programme' => $_GET['search_programme'] ?? '',
        'search_date' => $_GET['search_date'] ?? ''
    ];

    // If this is an AJAX request, return JSON data
    if ($isAjax) {
        $data = getVotersData($conn, $current_page, $records_per_page, $search_params);
        
        // Generate HTML for table rows
        $tableRows = '';
        if (!empty($data['voters'])) {
            foreach ($data['voters'] as $row) {
                $fullName = trim($row['Last_Name'] . ' ' . $row['Other_Name']);
                $email = $row['Student_Email'];
                $programme = $row['Programme'] ?: 'N/A';
                $contact = $row['Tel'] ?: 'N/A';
                $importDate = date('M d, Y H:i', strtotime($row['date_created']));
                $voterId = $row['id'] ?? $row['Index_No'];
                
                $tableRows .= "<tr>
                    <td><span class='index-badge'>{$row['Index_No']}</span></td>
                    <td><strong>{$fullName}</strong></td>
                    <td class='email-cell'>{$email}</td>
                    <td><span class='programme-badge'>{$programme}</span></td>
                    <td class='contact-cell'>{$contact}</td>
                    <td class='date-cell'>{$importDate}</td>
                </tr>";
            }
        } else {
            $tableRows = "<tr><td colspan='6' class='no-results'>
                            <div>📊</div>
                            <div>No voters found matching your search criteria</div>
                          </td></tr>";
        }
        
        // Generate pagination HTML
        $paginationHTML = '';
        if ($data['totalRecords'] > 0) {
            // Previous button
            if ($data['currentPage'] > 1) {
                $paginationHTML .= '<button class="page-btn" onclick="loadPage(' . ($data['currentPage'] - 1) . ')">Previous</button>';
            }
            
            // Page numbers
            $start_page = max(1, $data['currentPage'] - 2);
            $end_page = min($data['totalPages'], $data['currentPage'] + 2);
            
            for ($i = $start_page; $i <= $end_page; $i++) {
                $activeClass = $i === $data['currentPage'] ? 'active' : '';
                $paginationHTML .= '<button class="page-btn ' . $activeClass . '" onclick="loadPage(' . $i . ')">' . $i . '</button>';
            }
            
            // Next button
            if ($data['currentPage'] < $data['totalPages']) {
                $paginationHTML .= '<button class="page-btn" onclick="loadPage(' . ($data['currentPage'] + 1) . ')">Next</button>';
            }
        }
        
        // Return JSON response
        echo json_encode([
            'success' => true,
            'tableRows' => $tableRows,
            'paginationHTML' => $paginationHTML,
            'paginationInfo' => [
                'showing_from' => $data['offset'] + 1,
                'showing_to' => min($data['offset'] + $data['records_per_page'], $data['totalRecords']),
                'total_records' => $data['totalRecords'],
                'current_page' => $data['currentPage'],
                'total_pages' => max(1, $data['totalPages'])
            ]
        ]);
        exit;
    }

    // Get initial data for page load
    $initialData = getVotersData($conn, $current_page, $records_per_page, $search_params);
    
    // Get unique programmes for dropdown
    $programmeQuery = "SELECT DISTINCT Programme FROM voters WHERE Programme IS NOT NULL AND Programme != '' ORDER BY Programme";
    $programmeResult = $conn->query($programmeQuery);
    $programmes = [];
    if ($programmeResult) {
        while ($row = $programmeResult->fetch_assoc()) {
            $programmes[] = $row['Programme'];
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voters Management System</title>
    <link rel="stylesheet" href="<?php echo ($Domain."voters.css"); ?>">
    <style>
        /* Loading spinner styles */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .table-section {
            position: relative;
        }
        
        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Search section styles */
        .search-section {
            margin: 20px 0;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .search-section.collapsed {
            max-height: 0;
            margin: 0;
        }
        
        .search-section.expanded {
            max-height: 500px;
        }
        
        .search-toggle {
            width: 100%;
            padding: 12px 20px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 16px;
            margin-bottom: 10px;
        }
        
        .search-toggle:hover {
            background: #e9ecef;
        }
        
        .search-toggle.active {
            background: #007bff;
            color: white;
        }
        
        .search-toggle-icon {
            transition: transform 0.3s ease;
        }
        
        .search-toggle.active .search-toggle-icon {
            transform: rotate(180deg);
        }
        
        .search-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
        }
        
        .search-group {
            display: flex;
            flex-direction: column;
        }
        
        .search-group label {
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }
        
        .search-input, .search-select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .search-input:focus, .search-select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
        }
        
        .search-actions {
            grid-column: span 2;
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            background: #0056b3;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #545b62;
        }

        /* Alert styles */
        .alert {
            padding: 12px 20px;
            margin: 10px 0;
            border-radius: 4px;
            display: none;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Voters Management System</h1>
        </div>

        <div id="alertContainer">
            <?php if (isset($delete_success)): ?>
            <div class="alert alert-success" style="display: block;">
                ✓ Voter record deleted successfully!
            </div>
            <?php endif; ?>

            <?php if (isset($delete_error)): ?>
            <div class="alert alert-error" style="display: block;">
                ✗ Error deleting voter record. Please try again.
            </div>
            <?php endif; ?>
        </div>

        <button class="search-toggle" onclick="toggleSearch()" id="searchToggle">
            <span>🔍 Advanced Search & Filters</span>
            <span class="search-toggle-icon">▼</span>
        </button>

        <div class="search-section collapsed" id="searchSection">
            <div class="search-container">
                <div class="search-group">
                    <label for="search_name">Search by Name</label>
                    <input type="text" 
                           id="search_name" 
                           name="search_name" 
                           class="search-input" 
                           placeholder="Enter first or last name..."
                           value="<?php echo htmlspecialchars($_GET['search_name'] ?? ''); ?>">
                </div>
                
                <div class="search-group">
                    <label for="search_index">Search by Index No</label>
                    <input type="text" 
                           id="search_index" 
                           name="search_index" 
                           class="search-input" 
                           placeholder="Enter index number..."
                           value="<?php echo htmlspecialchars($_GET['search_index'] ?? ''); ?>">
                </div>
                
                <div class="search-group">
                    <label for="search_programme">Filter by Programme</label>
                    <select id="search_programme" name="search_programme" class="search-select">
                        <option value="">All Programmes</option>
                        <?php foreach ($programmes as $programme): ?>
                            <option value="<?php echo htmlspecialchars($programme); ?>" 
                                    <?php echo (($_GET['search_programme'] ?? '') === $programme) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($programme); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="search-group">
                    <label for="search_date">Import Date</label>
                    <input type="date" 
                           id="search_date" 
                           name="search_date" 
                           class="search-input"
                           value="<?php echo htmlspecialchars($_GET['search_date'] ?? ''); ?>">
                </div>
                
                <div class="search-actions">
                    <button type="button" class="btn btn-primary" onclick="performSearch()">Search</button>
                    <button type="button" class="btn btn-secondary" onclick="clearSearch()">Clear</button>
                </div>
            </div>
        </div>

        <div class="table-section">
            <div class="loading-overlay" id="loadingOverlay">
                <div class="spinner"></div>
            </div>
            
            <div class="table-container">
                <table class="voters-table">
                    <thead>
                        <tr>
                            <th>Index No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Programme</th>
                            <th>Contact</th>
                            <th>Import Date</th>
                        </tr>
                    </thead>
                    <tbody id="votersTableBody">
                        <?php
                        if (!empty($initialData['voters'])) {
                            foreach ($initialData['voters'] as $row) {
                                $fullName = trim($row['Last_Name'] . ' ' . $row['Other_Name']);
                                $email = $row['Student_Email'];
                                $programme = $row['Programme'] ?: 'N/A';
                                $contact = $row['Tel'] ?: 'N/A';
                                $importDate = date('M d, Y H:i', strtotime($row['date_created']));
                                $voterId = $row['id'] ?? $row['Index_No'];
                                
                                echo "<tr>
                                    <td><span class='index-badge'>{$row['Index_No']}</span></td>
                                    <td><strong>{$fullName}</strong></td>
                                    <td class='email-cell'>{$email}</td>
                                    <td><span class='programme-badge'>{$programme}</span></td>
                                    <td class='contact-cell'>{$contact}</td>
                                    <td class='date-cell'>{$importDate}</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='no-results'>
                                    <div>📊</div>
                                    <div>No voters found matching your search criteria</div>
                                  </td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if ($initialData['totalRecords'] > 0): ?>
        <div class="pagination-section">
            <div class="pagination-info" id="paginationInfo">
                Showing <?php echo $initialData['offset'] + 1; ?> to <?php echo min($initialData['offset'] + $initialData['records_per_page'], $initialData['totalRecords']); ?> 
                of <?php echo $initialData['totalRecords']; ?> records
            </div>
            
            <?php
                $_SESSION['access'] = hash('sha256', uniqid(mt_rand(), true));
            ?>
            <form action="datasheet.php?access=<?php echo $_SESSION['access']; ?>" method="post">
                <input type="hidden" name="hash" value="<?php echo $_SESSION['access']; ?>">
                <button style="width:100%;" >Import Voters Data</button>
            </form>
            
            <div class="pagination-controls" id="paginationControls">
                <?php
                // Previous button
                if ($initialData['currentPage'] > 1): ?>
                    <button class="page-btn" onclick="loadPage(<?php echo $initialData['currentPage'] - 1; ?>)">Previous</button>
                <?php endif; ?>
                
                <?php
                // Page numbers
                $start_page = max(1, $initialData['currentPage'] - 2);
                $end_page = min($initialData['totalPages'], $initialData['currentPage'] + 2);
                
                for ($i = $start_page; $i <= $end_page; $i++): ?>
                    <button class="page-btn <?php echo $i === $initialData['currentPage'] ? 'active' : ''; ?>" 
                            onclick="loadPage(<?php echo $i; ?>)">
                        <?php echo $i; ?>
                    </button>
                <?php endfor; ?>
                
                <!-- Next button -->
                <?php if ($initialData['currentPage'] < $initialData['totalPages']): ?>
                    <button class="page-btn" onclick="loadPage(<?php echo $initialData['currentPage'] + 1; ?>)">Next</button>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="stats-bar" id="statsBar">
            <div class="total-count">
                Page <?php echo $initialData['currentPage']; ?> of <?php echo max(1, $initialData['totalPages']); ?> 
                | Total: <?php echo $initialData['totalRecords']; ?> voter<?php echo $initialData['totalRecords'] !== 1 ? 's' : ''; ?>
            </div>
            <div>
                Last Updated: <?php echo date('M d, Y H:i'); ?>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal" style="display: none;">
        <div class="modal-content">
            <h3>⚠️ Confirm Deletion</h3>
            <p>Are you sure you want to delete the voter record for <strong id="voterName"></strong>?</p>
            <p style="color: #e74c3c; font-size: 14px;">This action cannot be undone.</p>
            <div class="modal-buttons">
                <button class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button class="btn btn-danger" onclick="deleteVoter()">Delete</button>
            </div>
        </div>
    </div>

    <script>
let currentVoterId = null;
let currentPage = 1;
let currentSearchParams = {};

// Get current search parameters from form
function getSearchParams() {
    return {
        search_name: document.getElementById('search_name').value.trim(),
        search_index: document.getElementById('search_index').value.trim(),
        search_programme: document.getElementById('search_programme').value.trim(),
        search_date: document.getElementById('search_date').value.trim()
    };
}

// Load page with current search parameters
function loadPage(page) {
    showLoading();
    currentPage = page;
    currentSearchParams = getSearchParams();

    // Build URL parameters
    const params = new URLSearchParams();
    
    // Add search parameters
    Object.keys(currentSearchParams).forEach(key => {
        if (currentSearchParams[key]) {
            params.append(key, currentSearchParams[key]);
        }
    });
    
    params.append('page', page);
    params.append('ajax', '1');

    fetch('?' + params.toString())
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Update table content
                document.getElementById('votersTableBody').innerHTML = data.tableRows;
                document.getElementById('paginationControls').innerHTML = data.paginationHTML;
                
                // Update pagination info
                const info = data.paginationInfo;
                document.getElementById('paginationInfo').textContent =
                    `Showing ${info.showing_from} to ${info.showing_to} of ${info.total_records} records`;
                
                // Update stats bar
                document.getElementById('statsBar').innerHTML = `
                    <div class="total-count">
                        Page ${info.current_page} of ${info.total_pages} 
                        | Total: ${info.total_records} voter${info.total_records !== 1 ? 's' : ''}
                    </div>
                    <div>
                        Last Updated: ${new Date().toLocaleString('en-US', {
                            month: 'short',
                            day: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        })}
                    </div>
                `;
                
                // Add visual effects
                document.getElementById('votersTableBody').classList.add('fade-in');
                setTimeout(() => {
                    document.getElementById('votersTableBody').classList.remove('fade-in');
                }, 300);
                
                highlightSearchTerms();
                addHoverEffects();
                
                // Update URL without reloading page
                const urlParams = new URLSearchParams();
                Object.keys(currentSearchParams).forEach(key => {
                    if (currentSearchParams[key]) {
                        urlParams.append(key, currentSearchParams[key]);
                    }
                });
                if (page > 1) {
                    urlParams.append('page', page);
                }
                
                const newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
                window.history.replaceState({}, '', newUrl);
            } else {
                throw new Error(data.message || 'Unknown error occurred');
            }
        })
        .catch(error => {
            console.error('Error loading page:', error);
            showAlert('Error loading data. Please try again.', 'error');
        })
        .finally(() => {
            hideLoading();
        });
}

// Perform search
function performSearch() {
    currentPage = 1;
    loadPage(1);
}

// Clear search function
function clearSearch() {
    document.getElementById('search_name').value = '';
    document.getElementById('search_index').value = '';
    document.getElementById('search_programme').value = '';
    document.getElementById('search_date').value = '';
    currentPage = 1;
    loadPage(1);
}

// Add event listeners for search inputs
document.addEventListener('DOMContentLoaded', function() {
    // Add enter key support for search inputs
    const searchInputs = document.querySelectorAll('.search-input');
    searchInputs.forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performSearch();
            }
        });
    });
    
    // Add change event for select dropdown
    document.getElementById('search_programme').addEventListener('change', function() {
        // Auto-search when programme is selected/changed
        performSearch();
    });
    
    // Auto-expand search if filters are applied
    checkAndExpandSearch();
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        if (alert.style.display === 'block') {
            setTimeout(() => {
                alert.style.display = 'none';
            }, 5000);
        }
    });
    
    // Initial load
    highlightSearchTerms();
    addHoverEffects();
});

// Delete voter function
function deleteVoter() {
    if (!currentVoterId) return;
    showLoading();
    const formData = new FormData();
    formData.append('delete_voter', '1');
    formData.append('voter_id', currentVoterId);
    formData.append('ajax', '1');
    
    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            return { success: true, message: 'Voter record deleted successfully!' };
        }
    })
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            closeModal();
            setTimeout(() => {
                loadPage(currentPage);
            }, 500);
        } else {
            showAlert(data.message || 'Error deleting voter record.', 'error');
        }
    })
    .catch(error => {
        console.error('Error deleting voter:', error);
        showAlert('Error deleting voter. Please try again.', 'error');
    })
    .finally(() => {
        hideLoading();
    });
}

// Make confirmDelete function globally accessible
window.confirmDelete = function(voterId, voterName) {
    currentVoterId = voterId;
    document.getElementById('voterName').textContent = voterName;
    document.getElementById('deleteModal').style.display = 'block';
}

// Show loading overlay
function showLoading() {
    document.getElementById('loadingOverlay').style.display = 'flex';
}

// Hide loading overlay
function hideLoading() {
    document.getElementById('loadingOverlay').style.display = 'none';
}

// Show alert function
function showAlert(message, type) {
    const alertContainer = document.getElementById('alertContainer');
    const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
    const alertIcon = type === 'success' ? '✓' : '✗';
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert ${alertClass}`;
    alertDiv.style.display = 'block';
    alertDiv.innerHTML = `${alertIcon} ${message}`;
    // alertContainer.innerHTML
    }