<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voters Management System</title>
    <style>
        /* Admin.css Aligned Styling */
        :root {
            --primary-color: #0d563c;
            --secondary-color: #f7c033;
            --text-light: #ffffff;
            --text-dark: #333333;
            --accent-color: #1a8350;
            --danger-color: #d9534f;
            --success-color: #5cb85c;
            --border-radius: 8px;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', 'Segoe UI', sans-serif;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            width: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="20" cy="20" r="1" fill="%23ffffff" opacity="0.1"/><circle cx="80" cy="40" r="1" fill="%23ffffff" opacity="0.05"/><circle cx="40" cy="80" r="1" fill="%23ffffff" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="%23134e32"/><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            background-repeat: repeat;
            background-attachment: fixed;
            color: var(--text-light);
            background-size: cover;
        }

        .container {
            max-width: 1400px;
            margin: 20px auto;
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            border: 1px solid rgba(255, 255, 255, 0.1);
            overflow: hidden;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            background-color: rgba(0, 0, 0, 0.25);
            box-shadow: var(--shadow);
        }

        .header h1 {
            color: var(--secondary-color);
            font-weight: 600;
            letter-spacing: 1px;
            margin: 0;
            font-size: 1.8rem;
        }

        .header p {
            color: var(--text-light);
            opacity: 0.8;
            font-size: 0.9rem;
        }

        .alert {
            padding: 15px 20px;
            margin: 20px;
            border-radius: var(--border-radius);
            font-weight: 500;
            display: none;
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .alert-success {
            color: var(--success-color);
            border-color: var(--success-color);
        }

        .alert-error {
            color: var(--danger-color);
            border-color: var(--danger-color);
        }

        .search-toggle {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-light);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 15px 30px;
            width: 100%;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .search-toggle:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: var(--secondary-color);
            color: var(--secondary-color);
        }

        .search-toggle-icon {
            transition: transform 0.3s ease;
            font-size: 18px;
        }

        .search-toggle.active .search-toggle-icon {
            transform: rotate(180deg);
        }

        .search-section {
            background-color: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            overflow: hidden;
            transition: all 0.4s ease;
        }

        .search-section.collapsed {
            max-height: 0;
            padding: 0 30px;
        }

        .search-section.expanded {
            max-height: 500px;
            padding: 30px;
        }

        .search-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            align-items: end;
        }

        .search-group {
            display: flex;
            flex-direction: column;
        }

        .search-group label {
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--secondary-color);
            font-size: 0.9rem;
        }

        .search-input, .search-select {
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: var(--border-radius);
            color: var(--text-light);
            font-size: 14px;
            transition: var(--transition);
        }

        .search-input:focus, .search-select:focus {
            outline: none;
            border-color: var(--secondary-color);
            background: rgba(255, 255, 255, 0.15);
        }

        .search-input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .search-select option {
            background: var(--primary-color);
            color: var(--text-light);
        }

        .btn {
            padding: 10px 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: var(--border-radius);
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-block;
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-light);
        }

        .btn:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: var(--secondary-color);
            color: var(--secondary-color);
        }

        .btn-primary {
            background: rgba(247, 192, 51, 0.2);
            color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .btn-primary:hover {
            background: var(--secondary-color);
            color: var(--primary-color);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-light);
        }

        .btn-danger {
            background: rgba(217, 83, 79, 0.2);
            color: var(--danger-color);
            border-color: var(--danger-color);
            padding: 6px 12px;
            font-size: 12px;
        }

        .btn-danger:hover {
            background: var(--danger-color);
            color: var(--text-light);
        }

        .table-section {
            height: 500px;
            overflow-y: auto;
            padding: 25px;
            background-color: rgba(255, 255, 255, 0.05);
        }

        .table-container {
            overflow-x: auto;
            border-radius: var(--border-radius);
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .voters-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .voters-table th {
            background: rgba(0, 0, 0, 0.3);
            color: var(--secondary-color);
            padding: 15px;
            text-align: left;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 12px;
            position: sticky;
            top: 0;
            z-index: 10;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .voters-table td {
            padding: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            vertical-align: middle;
            color: var(--text-light);
        }

        .voters-table tr:hover {
            background: rgba(255, 255, 255, 0.1);
            transition: var(--transition);
        }

        .voters-table tr:nth-child(even) {
            background: rgba(255, 255, 255, 0.02);
        }

        .index-badge {
            background: linear-gradient(135deg, var(--accent-color), var(--primary-color));
            color: var(--text-light);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
        }

        .email-cell {
            color: var(--secondary-color);
            font-weight: 500;
        }

        .programme-badge {
            background: linear-gradient(135deg, var(--secondary-color), #e6ac00);
            color: var(--primary-color);
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
        }

        .contact-cell {
            font-family: 'Courier New', monospace;
            font-weight: 500;
            color: var(--text-light);
        }

        .date-cell {
            color: rgba(255, 255, 255, 0.7);
            font-size: 12px;
        }

        .actions-cell {
            text-align: center;
            width: 100px;
        }

        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: rgba(255, 255, 255, 0.6);
            font-size: 18px;
        }

        .no-results div:first-child {
            font-size: 48px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .pagination-section {
            background-color: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .pagination-info {
            color: var(--text-light);
            font-weight: 500;
        }

        .pagination-controls {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .page-btn {
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: var(--border-radius);
            color: var(--text-light);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .page-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: var(--secondary-color);
            color: var(--secondary-color);
        }

        .page-btn.active {
            background: var(--secondary-color);
            color: var(--primary-color);
            border-color: var(--secondary-color);
        }

        .stats-bar {
            background-color: rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            color: var(--text-light);
        }

        .total-count {
            font-weight: 600;
            color: var(--secondary-color);
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }

        .modal-content {
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin: 15% auto;
            padding: 30px;
            border-radius: var(--border-radius);
            width: 90%;
            max-width: 500px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            color: var(--text-light);
        }

        .modal-content h3 {
            color: var(--danger-color);
            margin-bottom: 15px;
            font-size: 1.5rem;
        }

        .modal-content p {
            margin-bottom: 20px;
            color: var(--text-light);
        }

        .modal-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--secondary-color);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #e6ac00;
        }

        /* Responsive adjustments */
        @media (max-width: 950px) {
            .search-container {
                grid-template-columns: 1fr;
            }
            
            .header {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
            
            .table-section {
                padding: 15px;
            }
            
            .voters-table th,
            .voters-table td {
                padding: 10px 8px;
                font-size: 12px;
            }
            
            .pagination-section {
                flex-direction: column;
                text-align: center;
            }

            .container {
                margin: 10px;
            }
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 1.5rem;
            }
            
            .search-toggle {
                padding: 12px 20px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <?php
    // Include your database connection and PHP logic here
    // This is just the styled template - you'll need to integrate your existing PHP code
    ?>

    <div class="container">
        <div class="header">
            <div>
                <h1>Voters Management System</h1>
                <p>Comprehensive voter database with advanced search capabilities</p>
            </div>
        </div>

        <div class="alert alert-success" style="display: block;">
            ✓ Voter record deleted successfully!
        </div>

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
                           placeholder="Enter first or last name...">
                </div>
                
                <div class="search-group">
                    <label for="search_index">Search by Index No</label>
                    <input type="text" 
                           id="search_index" 
                           name="search_index" 
                           class="search-input" 
                           placeholder="Enter index number...">
                </div>
                
                <div class="search-group">
                    <label for="search_programme">Filter by Programme</label>
                    <select id="search_programme" name="search_programme" class="search-select">
                        <option value="">All Programmes</option>
                        <option value="Computer Science">Computer Science</option>
                        <option value="Information Technology">Information Technology</option>
                        <option value="Software Engineering">Software Engineering</option>
                    </select>
                </div>
                
                <div class="search-group">
                    <label for="search_date">Import Date</label>
                    <input type="date" 
                           id="search_date" 
                           name="search_date" 
                           class="search-input">
                </div>
                
                <div class="search-group">
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
                        <tr>
                            <td><span class="index-badge">CS2021001</span></td>
                            <td><strong>John Doe</strong></td>
                            <td class="email-cell">john.doe@student.edu</td>
                            <td><span class="programme-badge">Computer Science</span></td>
                            <td class="contact-cell">+233 123 456 789</td>
                            <td class="date-cell">Jun 28, 2025 14:30</td>
                            <td class="actions-cell">
                                <button class="btn btn-danger" onclick="confirmDelete('1', 'John Doe')">
                                    Delete
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="index-badge">IT2021002</span></td>
                            <td><strong>Jane Smith</strong></td>
                            <td class="email-cell">jane.smith@student.edu</td>
                            <td><span class="programme-badge">Information Technology</span></td>
                            <td class="contact-cell">+233 987 654 321</td>
                            <td class="date-cell">Jun 28, 2025 14:25</td>
                            <td class="actions-cell">
                                <button class="btn btn-danger" onclick="confirmDelete('2', 'Jane Smith')">
                                    Delete
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="index-badge">SE2021003</span></td>
                            <td><strong>Michael Johnson</strong></td>
                            <td class="email-cell">michael.johnson@student.edu</td>
                            <td><span class="programme-badge">Software Engineering</span></td>
                            <td class="contact-cell">+233 555 123 456</td>
                            <td class="date-cell">Jun 28, 2025 14:20</td>
                            <td class="actions-cell">
                                <button class="btn btn-danger" onclick="confirmDelete('3', 'Michael Johnson')">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="pagination-section">
            <div class="pagination-info">
                Showing 1 to 50 of 150 records
            </div>
            
            <div class="pagination-controls">
                <a href="#" class="page-btn">Previous</a>
                <a href="#" class="page-btn active">1</a>
                <a href="#" class="page-btn">2</a>
                <a href="#" class="page-btn">3</a>
                <a href="#" class="page-btn">Next</a>
            </div>
        </div>

        <div class="stats-bar">
            <div class="total-count">
                Page 1 of 3 | Total: 150 voters
            </div>
            <div>
                Last Updated: Jun 28, 2025 14:30
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h3>⚠️ Confirm Deletion</h3>
            <p>Are you sure you want to delete the voter record for <strong id="voterName">John Doe</strong>?</p>
            <p style="color: #d9534f; font-size: 14px;">This action cannot be undone.</p>
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

            // Add hover effects to table rows
            const tableRows = document.querySelectorAll('.voters-table tbody tr');
            tableRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.boxShadow = '0 4px 12px rgba(247, 192, 51, 0.2)';
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
                        alert.style.opacity = '0';
                        setTimeout(() => {
                            alert.style.display = 'none';
                        }, 300);
                    }, 5000);
                }
            });
        });
    </script>
</body>
</html>