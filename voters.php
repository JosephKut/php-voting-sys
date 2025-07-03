<?php
    include 'connect.php';
    include 'resources.php';
?>
<html lang="en">
<head>
    <link rel="stylesheet" href=<?php echo ($Domain."voters.css"); ?>>
    <style>
        .search-container { transition: max-height 0.3s, opacity 0.3s; overflow: hidden; }
        .search-container.hide { max-height: 0; opacity: 0; padding: 0; margin: 0; pointer-events: none; }
        .toggle-search-btn { margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Voters Management System</h1>
        </div>

        <button class="toggle-search-btn" id="toggleSearchBtn" onclick="toggleSearchPanel()">Hide Search Panel</button>
        <div class="table-section">
            <div class="table-container">
                <div class="search-container" id="searchPanel">
                    <div class="search-box">
                        <input type="text" id="notSentSearchInput" class="search-input" placeholder="Search by Index Number" autocomplete="off">
                        <input type="text" id="nameSearchInput" class="search-input" placeholder="Search by Name" autocomplete="off" style="margin-left:10px;">
                        <input type="date" id="importDateSearchInput" class="search-input" placeholder="Search by Import Date" style="margin-left:10px;">
                        <button class="clear-btn" onclick="clearNotSentSearch()">Clear</button>
                    </div>
                    <div class="search-stats" id="SearchStats">
                        Showing all voters yet to receive link
                    </div>
                </div>
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
                            $getVoters="SELECT * FROM voters";
                            $result=$conn->query($getVoters);
                            foreach($result as $Pin){
                                $fullName = trim($Pin['Last_Name'] . ' ' . $Pin['Other_Name']);
                                $email = $Pin['Student_Email'];
                                $programme = $Pin['Programme'] ?: 'N/A';
                                $contact = $Pin['Tel'] ?: 'N/A';
                                $importDate = date('Y-m-d', strtotime($Pin['date_created']));
                                $importDateDisplay = date('M d, Y H:i', strtotime($Pin['date_created']));
                                $voterId = $Pin['Index_No'];
                                echo "
                                <tr data-importdate='{$importDate}'>
                                    <td>{$voterId}</td>
                                    <td>{$fullName}</td>
                                    <td>{$email}</td>
                                    <td>{$programme}</td>
                                    <td>{$contact}</td>
                                    <td>{$importDateDisplay}</td>
                                </tr>";
                            }
                        ?>
                    </tbody>
                </table>
                <div id="NoResults" class="no-results" style="display: none;">
                    <div class="no-results-icon">🔍📊</div>
                    <h3>No results found</h3>
                    <p>No voters found matching your search criteria.</p>
                </div>
            </div>
        </div>
        <div style="margin: 20px auto; width: 60%;">
            <?php
                $_SESSION['access'] = hash('sha256', uniqid(mt_rand(), true));
            ?>
            <form action="datasheet.php?access=<?php echo $_SESSION['access']; ?>" method="post">
                <input type="hidden" name="hash" value="<?php echo $_SESSION['access']; ?>">
                <button style="width:100%;" >Import Voters Data</button>
            </form>
        </div>
    </div>
    <script>
        const notSentSearchInput = document.getElementById('notSentSearchInput');
        const nameSearchInput = document.getElementById('nameSearchInput');
        const importDateSearchInput = document.getElementById('importDateSearchInput');
        const votersTableBody = document.getElementById('votersTableBody');
        const SearchStats = document.getElementById('SearchStats');
        const NoResults = document.getElementById('NoResults');
        let notSentOriginalRows = Array.from(votersTableBody.getElementsByTagName('tr'));
        let notSentTotal = notSentOriginalRows.length;

        function performNotSentSearch() {
            const indexTerm = notSentSearchInput.value.trim().toLowerCase();
            const nameTerm = nameSearchInput.value.trim().toLowerCase();
            const importDateTerm = importDateSearchInput.value;
            let visibleCount = 0;
            notSentOriginalRows.forEach(row => {
                const indexNo = row.cells[0].textContent.toLowerCase();
                const name = row.cells[1].textContent.toLowerCase();
                const rowImportDate = row.getAttribute('data-importdate');
                const indexMatch = indexNo.includes(indexTerm) || indexTerm === '';
                const nameMatch = name.includes(nameTerm) || nameTerm === '';
                const dateMatch = !importDateTerm || rowImportDate === importDateTerm;
                if (indexMatch && nameMatch && dateMatch) {
                    row.style.display = '';
                    row.classList.add('highlight', 'fade-in');
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                    row.classList.remove('highlight', 'fade-in');
                }
            });
            if (visibleCount > 0) {
                SearchStats.textContent =  `Found ${visibleCount} voter${visibleCount !== 1 ? 's' : ''} matching your search`;
                NoResults.style.display = 'none';
                votersTableBody.style.display = '';
            } else {
                SearchStats.textContent = `No results found for your search`;
                NoResults.style.display = 'block';
                votersTableBody.style.display = 'none';
            }
        }
        function showAllNotSentRows() {
            notSentOriginalRows.forEach(row => {
                row.style.display = '';
                row.classList.remove('highlight', 'fade-in');
            });
            SearchStats.textContent = `Showing all ${notSentTotal} voters yet to receive link`;
            NoResults.style.display = 'none';
            votersTableBody.style.display = '';
        }
        function clearNotSentSearch() {
            notSentSearchInput.value = '';
            nameSearchInput.value = '';
            importDateSearchInput.value = '';
            showAllNotSentRows();
            notSentSearchInput.focus();
        }
        notSentSearchInput.addEventListener('input', performNotSentSearch);
        nameSearchInput.addEventListener('input', performNotSentSearch);
        importDateSearchInput.addEventListener('change', performNotSentSearch);
        notSentSearchInput.addEventListener('keyup', function(event) {
            if (event.key === 'Escape') {
                clearNotSentSearch();
            }
        });
        nameSearchInput.addEventListener('keyup', function(event) {
            if (event.key === 'Escape') {
                clearNotSentSearch();
            }
        });
        importDateSearchInput.addEventListener('keyup', function(event) {
            if (event.key === 'Escape') {
                clearNotSentSearch();
            }
        });

        // Toggle search panel visibility
        function toggleSearchPanel() {
            const panel = document.getElementById('searchPanel');
            const btn = document.getElementById('toggleSearchBtn');
            panel.classList.toggle('hide');
            if (panel.classList.contains('hide')) {
                btn.textContent = 'Show Search Panel';
            } else {
                btn.textContent = 'Hide Search Panel';
            }
        }
    </script>
</body>
</html>