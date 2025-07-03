<?php
    include 'connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voted List with Search</title>
</head>
<body>

    <!-- Not Sent Table -->
    <div class="content" id="not_sent" style="display: none;">
        <div class="table-section">
            <div class="table-container">
                <div class="search-container">
                    <div class="search-box">
                        <input type="text" id="notSentSearchInput" class="search-input" placeholder="Search by Index Number or Programme" autocomplete="off">
                        <button class="clear-btn" onclick="clearNotSentSearch()">Clear</button>
                    </div>
                    <div class="search-stats" id="notSentSearchStats">
                        Showing all voters yet to receive link
                    </div>
                </div>
                <table class="voters-table">
                    <thead>
                        <tr>
                            <th colspan="5"><h3>Voters Yet To Receive Link</h3></th>
                        </tr>
                        <tr>
                            <th>Index_No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Programme</th>
                            <th>Contact</th>
                        </tr>
                    </thead>
                    <tbody id="notSentTableBody">
                    <?php
                        $getVoters="SELECT * FROM voters";
                        $result=$conn->query($getVoters);
                        $table = $dom."_sent_links";
                        foreach($result as $Pin){
                            $getVoters="SELECT * FROM $table WHERE Student_Email ='$Pin[Student_Email]'";
                            $status=$conn->query($getVoters);
                            if($status->num_rows > 0){
                                continue; // Skip if the voter has already received link
                            }
                            echo "
                            <tr>
                                <td>{$Pin['Index_No']}</td>
                                <td>{$Pin['Last_Name']} {$Pin['Other_Name']}</td>
                                <td>{$Pin['Student_Email']}</td>
                                <td>{$Pin['Programme']}</td>
                                <td>{$Pin['Tel']}</td>
                            </tr>";
                        }
                    ?>
                    </tbody>
                </table>
                <div id="notSentNoResults" class="no-results" style="display: none;">
                    <div class="no-results-icon">🔍</div>
                    <h3>No results found</h3>
                    <p>No voters found matching your search criteria.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Voted Table -->
    <div class="content" id="voted" style="display: none;">
        <div class="search-container">
            <div class="search-box">
                <input type="text" id="votedSearchInput" class="search-input" placeholder="Search by Index Number or Programme" autocomplete="off">
                <button class="clear-btn" onclick="clearVotedSearch()">Clear</button>
            </div>
            <div class="search-stats" id="votedSearchStats">
                Showing all voted students
            </div>
        </div>
        <div class="table-section">
            <div class="table-container">
                <table class="voters-table">
                    <thead>
                        <tr>
                            <th colspan="6"><h3>Voted List</h3></th>
                        </tr>
                        <tr>
                            <th>Index_No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Programme</th>
                            <th>Contact</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody id="votedTableBody">
                    <?php
                        $getVoted="SELECT * FROM voters";
                        $result=$conn->query($getVoted);
                        $table = $dom."_votes";
                        foreach($result as $Pin){
                            $getVotes="SELECT * FROM $table WHERE Student_Email ='$Pin[Student_Email]'";
                            $status=$conn->query($getVotes);
                            if($status->num_rows > 0){
                                $row = $status->fetch_assoc();
                                $time = isset($row['time']) ? $row['time'] : '';
                                echo "
                                <tr>
                                    <td>{$Pin['Index_No']}</td>
                                    <td>{$Pin['Last_Name']} {$Pin['Other_Name']}</td>
                                    <td>{$Pin['Student_Email']}</td>
                                    <td>{$Pin['Programme']}</td>
                                    <td>{$Pin['Tel']}</td>
                                    <td>{$time}</td>
                                </tr>";
                            }
                        }
                    ?>
                    </tbody>
                </table>
                <div id="votedNoResults" class="no-results" style="display: none;">
                    <div class="no-results-icon">🔍</div>
                    <h3>No results found</h3>
                    <p>No students found matching your search criteria.</p>
                </div>
            </div>
        </div>
    </div>

<script>
    // --- Not Sent Table Search ---
    const notSentSearchInput = document.getElementById('notSentSearchInput');
    const notSentTableBody = document.getElementById('notSentTableBody');
    const notSentSearchStats = document.getElementById('notSentSearchStats');
    const notSentNoResults = document.getElementById('notSentNoResults');
    let notSentOriginalRows = Array.from(notSentTableBody.getElementsByTagName('tr'));
    let notSentTotal = notSentOriginalRows.length;

    function performNotSentSearch() {
        const searchTerm = notSentSearchInput.value.trim().toLowerCase();
        if (searchTerm === '') {
            showAllNotSentRows();
            return;
        }
        let visibleCount = 0;
        notSentOriginalRows.forEach(row => {
            const indexNo = row.cells[0].textContent.toLowerCase();
            const programme = row.cells[3].textContent.toLowerCase();
            if (indexNo.includes(searchTerm) || programme.includes(searchTerm)) {
                row.style.display = '';
                row.classList.add('highlight', 'fade-in');
                visibleCount++;
            } else {
                row.style.display = 'none';
                row.classList.remove('highlight', 'fade-in');
            }
        });
        if (visibleCount > 0) {
            notSentSearchStats.textContent =  `Found ${visibleCount} voter${visibleCount !== 1 ? 's' : ''} matching "${notSentSearchInput.value}"`;
            notSentNoResults.style.display = 'none';
            notSentTableBody.style.display = '';
        } else {
            notSentSearchStats.textContent = `No results found for "${notSentSearchInput.value}"`;
            notSentNoResults.style.display = 'block';
            notSentTableBody.style.display = 'none';
        }
    }
    function showAllNotSentRows() {
        notSentOriginalRows.forEach(row => {
            row.style.display = '';
            row.classList.remove('highlight', 'fade-in');
        });
        notSentSearchStats.textContent = `Showing all ${notSentTotal} voters yet to receive link`;
        notSentNoResults.style.display = 'none';
        notSentTableBody.style.display = '';
    }
    function clearNotSentSearch() {
        notSentSearchInput.value = '';
        showAllNotSentRows();
        notSentSearchInput.focus();
    }
    notSentSearchInput.addEventListener('input', performNotSentSearch);
    notSentSearchInput.addEventListener('keyup', function(event) {
        if (event.key === 'Escape') {
            clearNotSentSearch();
        }
    });

    // --- Voted Table Search ---
    const votedSearchInput = document.getElementById('votedSearchInput');
    const votedTableBody = document.getElementById('votedTableBody');
    const votedSearchStats = document.getElementById('votedSearchStats');
    const votedNoResults = document.getElementById('votedNoResults');
    let votedOriginalRows = Array.from(votedTableBody.getElementsByTagName('tr'));
    let votedTotal = votedOriginalRows.length;

    function performVotedSearch() {
        const searchTerm = votedSearchInput.value.trim().toLowerCase();
        if (searchTerm === '') {
            showAllVotedRows();
            return;
        }
        let visibleCount = 0;
        votedOriginalRows.forEach(row => {
            const indexNo = row.cells[0].textContent.toLowerCase();
            const programme = row.cells[3].textContent.toLowerCase();
            if (indexNo.includes(searchTerm) || programme.includes(searchTerm)) {
                row.style.display = '';
                row.classList.add('highlight', 'fade-in');
                visibleCount++;
            } else {
                row.style.display = 'none';
                row.classList.remove('highlight', 'fade-in');
            }
        });
        if (visibleCount > 0) {
            votedSearchStats.textContent =  `Found ${visibleCount} student${visibleCount !== 1 ? 's' : ''} matching "${votedSearchInput.value}"`;
            votedNoResults.style.display = 'none';
            votedTableBody.style.display = '';
        } else {
            votedSearchStats.textContent = `No results found for "${votedSearchInput.value}"`;
            votedNoResults.style.display = 'block';
            votedTableBody.style.display = 'none';
        }
    }
    function showAllVotedRows() {
        votedOriginalRows.forEach(row => {
            row.style.display = '';
            row.classList.remove('highlight', 'fade-in');
        });
        votedSearchStats.textContent = `Showing all ${votedTotal} voted students`;
        votedNoResults.style.display = 'none';
        votedTableBody.style.display = '';
    }
    function clearVotedSearch() {
        votedSearchInput.value = '';
        showAllVotedRows();
        votedSearchInput.focus();
    }
    votedSearchInput.addEventListener('input', performVotedSearch);
    votedSearchInput.addEventListener('keyup', function(event) {
        if (event.key === 'Escape') {
            clearVotedSearch();
        }
    });

    // --- Animation Cleanup ---
    document.addEventListener('animationend', function(event) {
        if (event.animationName === 'fadeIn') {
            event.target.classList.remove('fade-in');
        }
    });

    // --- Initialize ---
    document.addEventListener('DOMContentLoaded', function() {
        notSentSearchInput.value = '';
        notSentSearchStats.textContent = `Showing all ${notSentTotal} voters yet to receive link`;
        votedSearchInput.value = '';
        votedSearchStats.textContent = `Showing all ${votedTotal} voted students`;
    });
</script>
</body>
</html>
