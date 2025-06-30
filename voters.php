<?php
    include 'connect.php';
    include 'resources.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href=<?php echo ($Domain."voters.css"); ?>>
</head>
<body>
    <?php
    include 'connect.php';
    
    // Handle delete operation
    if (isset($_POST['delete_voter']) && isset($_POST['voter_id'])) {
        $voter_id = $_POST['voter_id'];
        $deleteQuery = "DELETE FROM voters WHERE id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("i", $voter_id);
        
        if ($stmt->execute()) {
            $delete_success = true;
        } else {
            $delete_error = true;
        }
        $stmt->close();
    }
    
    // Pagination settings
    $records_per_page = 50;
    $current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $offset = ($current_page - 1) * $records_per_page;
    
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

    <div class="container">
        <div class="header">
            <h1>Voters Management System</h1>
        </div>

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

        <button class="search-toggle" onclick="toggleSearch()" id="searchToggle">
            <span>🔍 Advanced Search & Filters</span>
            <span class="search-toggle-icon">▼</span>
        </button>

        <div class="search-section collapsed" id="searchSection">
            <form method="GET" action="" class="search-container">
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
                
                <div class="search">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <a href="?" class="btn btn-secondary">Clear</a>
                </div>
            </form>
        </div>

        <div class="table-section">
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
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Build the query with search conditions
                        $whereConditions = ["1=1"];
                        $params = [];
                        $types = "";
                        
                        // Search by name
                        if (!empty($_GET['search_name'])) {
                            $whereConditions[] = "(Last_Name LIKE ? OR Other_Name LIKE ?)";
                            $searchName = "%" . $_GET['search_name'] . "%";
                            $params[] = $searchName;
                            $params[] = $searchName;
                            $types .= "ss";
                        }
                        
                        // Search by index number
                        if (!empty($_GET['search_index'])) {
                            $whereConditions[] = "Index_No LIKE ?";
                            $params[] = "%" . $_GET['search_index'] . "%";
                            $types .= "s";
                        }
                        
                        // Search by programme
                        if (!empty($_GET['search_programme'])) {
                            $whereConditions[] = "Programme = ?";
                            $params[] = $_GET['search_programme'];
                            $types .= "s";
                        }
                        
                        // Search by date
                        if (!empty($_GET['search_date'])) {
                            $whereConditions[] = "DATE(date_created) = ?";
                            $params[] = $_GET['search_date'];
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
                        
                        $count = 0;
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $count++;
                                $fullName = trim($row['Last_Name'] . ' ' . $row['Other_Name']);
                                $email = $row['Student_Email'];
                                $programme = $row['Programme'] ?: 'N/A';
                                $contact = $row['Tel'] ?: 'N/A';
                                // if ($row['Status'] == null){
                                //     $status = '❓';
                                // }else{
                                //      $status = $row['Status'] == 1 ? '✅' : '❌';
                                // }
                                $importDate = date('M d, Y H:i', strtotime($row['date_created']));
                                $voterId = $row['id'] ?? $row['Index_No']; // Use id if available, fallback to Index_No
                                
                                echo "<tr>
                                    <td><span class='index-badge'>{$row['Index_No']}</span></td>
                                    <td><strong>{$fullName}</strong></td>
                                    <td class='email-cell'>{$email}</td>
                                    <td><span class='programme-badge'>{$programme}</span></td>
                                    <td class='contact-cell'>{$contact}</td>
                                    <td class='date-cell'>{$importDate}</td>
                                    <td class='actions-cell'>
                                        <button class='btn btn-danger' onclick='confirmDelete(\"{$voterId}\", \"{$fullName}\")'>
                                            Delete
                                        </button>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' class='no-results'>
                                    <div>📊</div>
                                    <div>No voters found matching your search criteria</div>
                                  </td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if ($totalRecords > 0): ?>
        <div class="pagination-section">
            <div class="pagination-info">
                Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $records_per_page, $totalRecords); ?> 
                of <?php echo $totalRecords; ?> records
            </div>
            
            <form action="datasheet.php" method="post">
                <button style="width:100%;" >Import Voters Data</button>
            </form>
            <div class="pagination-controls">
                <?php
                $queryString = http_build_query(array_merge($_GET, ['page' => '']));
                $queryString = rtrim($queryString, '=');
                
                // Previous button
                if ($current_page > 1): ?>
                    <a href="?<?php echo $queryString . '=' . ($current_page - 1); ?>" class="page-btn">Previous</a>
                <?php endif; ?>
                
                <?php
                // Page numbers
                $start_page = max(1, $current_page - 2);
                $end_page = min($totalPages, $current_page + 2);
                
                for ($i = $start_page; $i <= $end_page; $i++): ?>
                    <a href="?<?php echo $queryString . '=' . $i; ?>" 
                       class="page-btn <?php echo $i === $current_page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                
                <!-- Next button -->
                <?php if ($current_page < $totalPages): ?>
                    <a href="?<?php echo $queryString . '=' . ($current_page + 1); ?>" class="page-btn">Next</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="stats-bar">
            <div class="total-count">
                Page <?php echo $current_page; ?> of <?php echo max(1, $totalPages); ?> 
                | Total: <?php echo $totalRecords; ?> voter<?php echo $totalRecords !== 1 ? 's' : ''; ?>
            </div>
            <div>
                Last Updated: <?php echo date('M d, Y H:i'); ?>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h3>⚠️ Confirm Deletion</h3>
            <p>Are you sure you want to delete the voter record for <strong id="voterName"></strong>?</p>
            <p style="color: #e74c3c; font-size: 14px;">This action cannot be undone.</p>
            <div class="modal-buttons">
                <button class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="voter_id" id="deleteVoterId">
                    <button type="submit" name="delete_voter" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Toggle search section
        function toggleSearch() {
            const searchSection = document.getElementById('searchSection');
            const searchToggle = document.getElementById('searchToggle');
            
            if (searchSection.classList.contains('collapsed')) {
                searchSection.classList.remove('collapsed');
                searchSection.classList.add('expanded');
                searchToggle.classList.add('active');
                searchToggle.innerHTML = '<span>🔍 Hide Search & Filters</span><span class="search-toggle-icon">▼</span>';
            } else {
                searchSection.classList.remove('expanded');
                searchSection.classList.add('collapsed');
                searchToggle.classList.remove('active');
                searchToggle.innerHTML = '<span>🔍 Advanced Search & Filters</span><span class="search-toggle-icon">▼</span>';
            }
        }

        // Auto-expand search if filters are applied
        function checkAndExpandSearch() {
            const urlParams = new URLSearchParams(window.location.search);
            const hasFilters = urlParams.get('search_name') || 
                             urlParams.get('search_index') || 
                             urlParams.get('search_programme') || 
                             urlParams.get('search_date');
            
            if (hasFilters) {
                const searchSection = document.getElementById('searchSection');
                const searchToggle = document.getElementById('searchToggle');
                searchSection.classList.remove('collapsed');
                searchSection.classList.add('expanded');
                searchToggle.classList.add('active');
                searchToggle.innerHTML = '<span>🔍 Hide Search & Filters</span><span class="search-toggle-icon">▼</span>';
            }
        }

        // Modal functions
        function confirmDelete(voterId, voterName) {
            document.getElementById('deleteVoterId').value = voterId;
            document.getElementById('voterName').textContent = voterName;
            document.getElementById('deleteModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('deleteModal');
            if (event.target === modal) {
                closeModal();
            }
        }

        // Add interactive features
        document.addEventListener('DOMContentLoaded', function() {
            // Check if search should be expanded
            checkAndExpandSearch();

            // Highlight search terms in results
            const searchName = '<?php echo htmlspecialchars($_GET['search_name'] ?? ''); ?>';
            if (searchName) {
                const nameColumns = document.querySelectorAll('td:nth-child(2)');
                nameColumns.forEach(cell => {
                    const text = cell.innerHTML;
                    const regex = new RegExp(`(${searchName})`, 'gi');
                    cell.innerHTML = text.replace(regex, '<mark style="background: #fff3cd; padding: 2px 4px; border-radius: 3px;">$1</mark>');
                });
            }
            
            // Add hover effects to table rows
            const tableRows = document.querySelectorAll('.voters-table tbody tr');
            tableRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.boxShadow = '0 4px 12px rgba(0,0,0,0.1)';
                });
                
                row.addEventListener('mouseleave', function() {
                    this.style.boxShadow = 'none';
                });
            });

            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                if (alert.style.display === 'block') {
                    setTimeout(() => {
                        alert.style.display = 'none';
                    }, 5000);
                }
            });
        });
    </script>
</body>
</html>