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

    // Function to get voters data with search functionality
    function getVotersData($conn, $current_page = 1, $records_per_page = 50, $search_params = []) {
        $offset = ($current_page - 1) * $records_per_page;
        
        // Build WHERE clause based on search parameters
        $whereConditions = [];
        $bindParams = [];
        $paramTypes = '';

        // Search by name (Last_Name or Other_Name)
        if (!empty($search_params['name'])) {
            $whereConditions[] = "(Last_Name LIKE ? OR Other_Name LIKE ? OR CONCAT(Last_Name, ' ', Other_Name) LIKE ?)";
            $nameParam = '%' . $search_params['name'] . '%';
            $bindParams[] = $nameParam;
            $bindParams[] = $nameParam;
            $bindParams[] = $nameParam;
            $paramTypes .= 'sss';
        }

        // Search by programme
        if (!empty($search_params['programme'])) {
            $whereConditions[] = "Programme LIKE ?";
            $bindParams[] = '%' . $search_params['programme'] . '%';
            $paramTypes .= 's';
        }

        // Search by index number
        if (!empty($search_params['index_no'])) {
            $whereConditions[] = "Index_No LIKE ?";
            $bindParams[] = '%' . $search_params['index_no'] . '%';
            $paramTypes .= 's';
        }

        // Search by imported date (date range)
        if (!empty($search_params['date_from'])) {
            $whereConditions[] = "DATE(date_created) >= ?";
            $bindParams[] = $search_params['date_from'];
            $paramTypes .= 's';
        }

        if (!empty($search_params['date_to'])) {
            $whereConditions[] = "DATE(date_created) <= ?";
            $bindParams[] = $search_params['date_to'];
            $paramTypes .= 's';
        }

        // Build the WHERE clause
        $whereClause = '';
        if (!empty($whereConditions)) {
            $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        }

        // Get total count for pagination
        $countQuery = "SELECT COUNT(*) as total FROM voters $whereClause";
        if (!empty($bindParams)) {
            $countStmt = $conn->prepare($countQuery);
            $countStmt->bind_param($paramTypes, ...$bindParams);
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
        $mainQuery = "SELECT * FROM voters $whereClause ORDER BY date_created DESC LIMIT ? OFFSET ?";
        $bindParams[] = $records_per_page;
        $bindParams[] = $offset;
        $paramTypes .= 'ii';

        $voters = [];
        if (!empty($whereConditions) || !empty($bindParams)) {
            $stmt = $conn->prepare($mainQuery);
            $stmt->bind_param($paramTypes, ...$bindParams);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $voters[] = $row;
                }
            }
            $stmt->close();
        } else {
            $mainQuery = "SELECT * FROM voters ORDER BY date_created DESC LIMIT $records_per_page OFFSET $offset";
            $result = $conn->query($mainQuery);
            
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $voters[] = $row;
                }
            }
        }

        return [
            'voters' => $voters,
            'totalRecords' => $totalRecords,
            'totalPages' => $totalPages,
            'currentPage' => $current_page,
            'offset' => $offset,
            'records_per_page' => $records_per_page,
            'search_params' => $search_params
        ];
    }

    // Pagination settings
    $records_per_page = 50;
    $current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

    // Get search parameters
    $search_params = [
        'name' => isset($_GET['search_name']) ? trim($_GET['search_name']) : '',
        'programme' => isset($_GET['search_programme']) ? trim($_GET['search_programme']) : '',
        'index_no' => isset($_GET['search_index']) ? trim($_GET['search_index']) : '',
        'date_from' => isset($_GET['date_from']) ? $_GET['date_from'] : '',
        'date_to' => isset($_GET['date_to']) ? $_GET['date_to'] : ''
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

                // Highlight search terms
                if (!empty($search_params['name'])) {
                    $fullName = highlightSearchTerm($fullName, $search_params['name']);
                }
                if (!empty($search_params['programme'])) {
                    $programme = highlightSearchTerm($programme, $search_params['programme']);
                }
                if (!empty($search_params['index_no'])) {
                    $indexDisplay = highlightSearchTerm($row['Index_No'], $search_params['index_no']);
                } else {
                    $indexDisplay = $row['Index_No'];
                }

                $tableRows .= "<tr>
                    <td><span class='index-badge'>{$indexDisplay}</span></td>
                    <td><strong>{$fullName}</strong></td>
                    <td class='email-cell'>{$email}</td>
                    <td><span class='programme-badge'>{$programme}</span></td>
                    <td class='contact-cell'>{$contact}</td>
                    <td class='date-cell'>{$importDate}</td>
                </tr>";
            }
        } else {
            $searchActive = !empty(array_filter($search_params));
            $noResultsMessage = $searchActive ? 'No voters found matching your search criteria' : 'No voters found';
            $tableRows = "<tr><td colspan='6' class='no-results'>
                            <div>📊</div>
                            <div>{$noResultsMessage}</div>
                          </td></tr>";
        }

        // Generate pagination HTML
        $paginationHTML = '';
        if ($data['totalRecords'] > 0) {
            // Build search query string for pagination links
            $searchQuery = http_build_query(array_filter($search_params));
            
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
                'showing_from' => $data['totalRecords'] > 0 ? $data['offset'] + 1 : 0,
                'showing_to' => min($data['offset'] + $data['records_per_page'], $data['totalRecords']),
                'total_records' => $data['totalRecords'],
                'current_page' => $data['currentPage'],
                'total_pages' => max(1, $data['totalPages'])
            ]
        ]);
        exit;
    }

    // Function to highlight search terms
    function highlightSearchTerm($text, $searchTerm) {
        if (empty($searchTerm)) return $text;
        return preg_replace('/(' . preg_quote($searchTerm, '/') . ')/i', '<mark>$1</mark>', $text);
    }

    // Get initial data for page load
    $initialData = getVotersData($conn, $current_page, $records_per_page, $search_params);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href=<?php echo ($Domain."voters.css"); ?>>
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

        <!-- Search Section -->
        <div class="search-section">
            <form id="searchForm" class="search-form">
                <div class="search-field">
                    <label for="search_name">Name</label>
                    <input type="text" id="search_name" name="search_name" 
                           placeholder="Search by name..." 
                           value="<?php echo htmlspecialchars($search_params['name']); ?>">
                </div>
                
                <div class="search-field">
                    <label for="search_programme">Programme</label>
                    <input type="text" id="search_programme" name="search_programme" 
                           placeholder="Search by programme..." 
                           value="<?php echo htmlspecialchars($search_params['programme']); ?>">
                </div>
                
                <div class="search-field">
                    <label for="search_index">Index Number</label>
                    <input type="text" id="search_index" name="search_index" 
                           placeholder="Search by index no..." 
                           value="<?php echo htmlspecialchars($search_params['index_no']); ?>">
                </div>
                
                <div class="search-field">
                    <label for="date_from">Date From</label>
                    <input type="date" id="date_from" name="date_from" 
                           value="<?php echo htmlspecialchars($search_params['date_from']); ?>">
                </div>
                
                <div class="search-field">
                    <label for="date_to">Date To</label>
                    <input type="date" id="date_to" name="date_to" 
                           value="<?php echo htmlspecialchars($search_params['date_to']); ?>">
                </div>
                
                <div class="search-buttons">
                    <button type="submit" class="search-btn primary">
                        🔍 Search
                    </button>
                    <button type="button" class="search-btn secondary" onclick="clearSearch()">
                        ✕ Clear
                    </button>
                </div>
            </form>
        </div>

        <!-- Active Search Indicator -->
        <?php if (!empty(array_filter($search_params))): ?>
        <div class="search-active" id="searchActiveIndicator">
            <p class="search-active-text">
                🔍 Search active - Found <?php echo $initialData['totalRecords']; ?> result(s)
            </p>
        </div>
        <?php endif; ?>

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

                                // Highlight search terms
                                if (!empty($search_params['name'])) {
                                    $fullName = highlightSearchTerm($fullName, $search_params['name']);
                                }
                                if (!empty($search_params['programme'])) {
                                    $programme = highlightSearchTerm($programme, $search_params['programme']);
                                }
                                if (!empty($search_params['index_no'])) {
                                    $indexDisplay = highlightSearchTerm($row['Index_No'], $search_params['index_no']);
                                } else {
                                    $indexDisplay = $row['Index_No'];
                                }

                                echo "<tr>
                                    <td><span class='index-badge'>{$indexDisplay}</span></td>
                                    <td><strong>{$fullName}</strong></td>
                                    <td class='email-cell'>{$email}</td>
                                    <td><span class='programme-badge'>{$programme}</span></td>
                                    <td class='contact-cell'>{$contact}</td>
                                    <td class='date-cell'>{$importDate}</td>
                                </tr>";
                            }
                        } else {
                            $searchActive = !empty(array_filter($search_params));
                            $noResultsMessage = $searchActive ? 'No voters found matching your search criteria' : 'No voters found';
                            echo "<tr><td colspan='6' class='no-results'>
                                    <div>📊</div>
                                    <div>{$noResultsMessage}</div>
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
                Showing <?php echo $initialData['totalRecords'] > 0 ? $initialData['offset'] + 1 : 0; ?> to <?php echo min($initialData['offset'] + $initialData['records_per_page'], $initialData['totalRecords']); ?> 
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
                if ($initialData['currentPage'] > 1): ?>
                    <button class="page-btn" onclick="loadPage(<?php echo $initialData['currentPage'] - 1; ?>)">Previous</button>
                <?php endif; ?>
                <?php
                $start_page = max(1, $initialData['currentPage'] - 2);
                $end_page = min($initialData['totalPages'], $initialData['currentPage'] + 2);
                for ($i = $start_page; $i <= $end_page; $i++): ?>
                    <button class="page-btn <?php echo $i === $initialData['currentPage'] ? 'active' : ''; ?>" 
                            onclick="loadPage(<?php echo $i; ?>)">
                        <?php echo $i; ?>
                    </button>
                <?php endfor; ?>
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

// Initialize search parameters from PHP
<?php if (!empty($search_params)): ?>
currentSearchParams = <?php echo json_encode($search_params); ?>;
<?php endif; ?>

// Load page with AJAX and search parameters
function loadPage(page) {
    showLoading();
    const params = new URLSearchParams();
    params.append('page', page);
    params.append('ajax', '1');
    
    // Add search parameters
    Object.keys(currentSearchParams).forEach(key => {
        if (currentSearchParams[key]) {
            params.append('search_' + key, currentSearchParams[key]);
        }
    });
    
    // Add date parameters separately
    if (currentSearchParams.date_from) {
        params.append('date_from', currentSearchParams.date_from);
    }
    if (currentSearchParams.date_to) {
        params.append('date_to', currentSearchParams.date_to);
    }

    fetch('?' + params.toString())
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('votersTableBody').innerHTML = data.tableRows;
                document.getElementById('paginationControls').innerHTML = data.paginationHTML;
                const info = data.paginationInfo;
                document.getElementById('paginationInfo').textContent =
                    `Showing ${info.showing_from} to ${info.showing_to} of ${info.total_records} records`;
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
                currentPage = page;
                document.getElementById('votersTableBody').classList.add('fade-in');
                addHoverEffects();
                
                // Update search active indicator
                updateSearchActiveIndicator(info.total_records);
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

// Handle search form submission
document.getElementById('searchForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Get search parameters
    currentSearchParams = {
        name: document.getElementById('search_name').value.trim(),
        programme: document.getElementById('search_programme').value.trim(),
        index_no: document.getElementById('search_index').value.trim(),
        date_from: document.getElementById('date_from').value,
        date_to: document.getElementById('date_to').value
    };
    
    // Reset to first page and load results
    currentPage = 1;
    loadPage(1);
});

// Clear search function
function clearSearch() {
    document.getElementById('searchForm').reset();
    currentSearchParams = {};
    currentPage = 1;
    loadPage(1);
    
    // Hide search active indicator
    const indicator = document.getElementById('searchActiveIndicator');
    if (indicator) {
        indicator.style.display = 'none';
    }
}

// Update search active indicator
function updateSearchActiveIndicator(totalRecords) {
    let indicator = document.getElementById('searchActiveIndicator');
    const hasActiveSearch = Object.values(currentSearchParams).some(val => val && val.trim() !== '');
    
    if (hasActiveSearch) {
        if (!indicator) {
            // Create indicator if it doesn't exist
            indicator = document.createElement('div');
            indicator.id = 'searchActiveIndicator';
            indicator.className = 'search-active';
            document.querySelector('.search-section').insertAdjacentElement('afterend', indicator);
        }
        indicator.innerHTML = `<p class="search-active-text">🔍 Search active - Found ${totalRecords} result(s)</p>`;
        indicator.style.display = 'block';
    } else if (indicator) {
        indicator.style.display = 'none';
    }
}

// Pagination buttons use AJAX
document.addEventListener('DOMContentLoaded', function() {
    addHoverEffects();
    
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        if (alert.style.display === 'block') {
            setTimeout(() => {
                alert.style.display = 'none';
            }, 5000);
        }
    });
});

// Handle pagination clicks
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('page-btn')) {
        e.preventDefault();
        let page = parseInt(e.target.textContent);
        if (e.target.textContent === 'Previous') {
            page = currentPage - 1;
        } else if (e.target.textContent === 'Next') {
            page = currentPage + 1;
        }
        if (!isNaN(page)) {
            loadPage(page);
        }
    }
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
    alertContainer.innerHTML = '';
    alertContainer.appendChild(alertDiv);
    setTimeout(() => {
        alertDiv.style.display = 'none';
    }, 5000);
}

// Modal functions
function closeModal() {
    document.getElementById('deleteModal').style.display = 'none';
    currentVoterId = null;
}
window.closeModal = closeModal;

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('deleteModal');
    if (event.target === modal) {
        closeModal();
    }
}

// Add hover effects to table rows
function addHoverEffects() {
    const tableRows = document.querySelectorAll('.voters-table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.boxShadow = '0 4px 12px rgba(0,0,0,0.1)';
        });
        row.addEventListener('mouseleave', function() {
            this.style.boxShadow = 'none';
        });
    });
}

// Make deleteVoter globally accessible
window.deleteVoter = deleteVoter;

// Enable Enter key for search
document.addEventListener('keypress', function(e) {
    if (e.key === 'Enter' && e.target.closest('#searchForm')) {
        e.preventDefault();
        document.getElementById('searchForm').dispatchEvent(new Event('submit'));
    }
});

// Auto-search functionality (optional - searches as user types)
// Uncomment the following code if you want real-time search

let searchTimeout;
function setupAutoSearch() {
    const searchInputs = document.querySelectorAll('#searchForm input[type="text"]');
    searchInputs.forEach(input => {
        input.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('searchForm').dispatchEvent(new Event('submit'));
            }, 500); // Wait 500ms after user stops typing
        });
    });
}

// Call setupAutoSearch after DOM is loaded
document.addEventListener('DOMContentLoaded', setupAutoSearch);
    </script>
</body>
</html>