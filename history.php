<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'Final_DR');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get filter parameters
$search = $_GET['search'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$type = $_GET['type'] ?? '';

// Build query with filters
$query = "SELECT * FROM history WHERE 1=1";
$params = [];
$types = "";

if (!empty($search)) {
    $query .= " AND (si_number LIKE ? OR dr_number LIKE ? OR delivered_to LIKE ? OR particulars LIKE ?)";
    $search_param = "%" . $search . "%";
    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
    $types .= "ssss";
}

if (!empty($date_from)) {
    $query .= " AND si_date >= ?";
    $params[] = $date_from;
    $types .= "s";
}

if (!empty($date_to)) {
    $query .= " AND si_date <= ?";
    $params[] = $date_to;
    $types .= "s";
}

if (!empty($type)) {
    $query .= " AND type = ?";
    $params[] = $type;
    $types .= "s";
}

// Order by latest first
$query .= " ORDER BY created_at DESC";

// Prepare and execute
$stmt = $conn->prepare($query);

if ($stmt) {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $activities = $result->fetch_all(MYSQLI_ASSOC);
} else {
    // Fallback
    $result = $conn->query($query);
    $activities = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Activity Log</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="HistoryStyle.css">
    <style>
        .highlight {
            background-color: yellow;
            font-weight: bold;
            padding: 1px 2px;
            border-radius: 2px;
        }

        .pagination-controls {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 20px;
            justify-content: center;
        }

        .pagination-btn {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            color: #4a5568;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            min-width: 36px;
            text-align: center;
        }

        .pagination-btn:hover:not(:disabled) {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .pagination-btn.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
            font-weight: bold;
        }

        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .pagination-info {
            text-align: center;
            color: #718096;
            font-size: 14px;
            margin-top: 10px;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #a0aec0;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #e2e8f0;
        }

        .empty-state h3 {
            margin: 10px 0;
            color: #4a5568;
        }

        .empty-state p {
            margin: 0;
            color: #718096;
        }

        .filter-input,
        .filter-date,
        .filter-select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-history"></i> History Activity Log</h1>
            <p>Monitor all history table activities</p>
        </div>

        <!-- Simple Filters -->
        <div class="filters">
            <div class="filter-group">
                <label class="filter-label">Search</label>
                <input type="text" class="filter-input" id="searchInput"
                    placeholder="Search SI#, DR#, delivered to, particulars..."
                    autocomplete="off">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="filter-group">
                    <label class="filter-label">Date From</label>
                    <input type="date" class="filter-date" id="dateFrom">
                </div>

                <div class="filter-group">
                    <label class="filter-label">Date To</label>
                    <input type="date" class="filter-date" id="dateTo">
                </div>
            </div>

            <div class="filter-group">
                <label class="filter-label">Type</label>
                <select class="filter-select" id="typeFilter">
                    <option value="">All Types</option>
                    <option value="usedmachine">Used Machine</option>
                    <option value="bnew">Brand New</option>
                    <option value="pulloutandreplacement">Pull Out and Replacement Machine</option>
                    <option value="replacementmachine">Replacement Machine</option>
                    <option value="pulloutmachine">Pull Out Machine</option>
                    <option value="drwithprice">DR with Price</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="table-container">
            <table class="activity-table" id="activityTable">
                <thead>
                    <tr>
                        <!-- <th>ID</th> -->
                        <th>SI Number</th>
                        <th>DR Number</th>
                        <th>Delivered To</th>
                        <th>SI Date</th>
                        <th>Type</th>
                        <th>Created At</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php foreach ($activities as $activity): ?>
                        <?php
                        // Format the date for display
                        $display_date = !empty($activity['si_date']) ? date('M d, Y', strtotime($activity['si_date'])) : '';
                        // Format for data attribute (YYYY-MM-DD)
                        $data_date = !empty($activity['si_date']) ? date('Y-m-d', strtotime($activity['si_date'])) : '';
                        ?>
                        <tr data-id="<?php echo $activity['id']; ?>"
                            data-searchable="<?php echo htmlspecialchars(strtolower($activity['si_number'] . ' ' . $activity['dr_number'] . ' ' . $activity['delivered_to'] . ' ' . $activity['particulars'] . ' ' . $activity['type'] . ' ' . $activity['status'])); ?>"
                            data-type="<?php echo htmlspecialchars($activity['type']); ?>"
                            data-date="<?php echo htmlspecialchars($data_date); ?>">
                            <td><strong class="si-number"><?php echo htmlspecialchars($activity['si_number']); ?></strong></td>
                            <td><strong class="dr-number"><?php echo htmlspecialchars($activity['dr_number']); ?></strong></td>
                            <td class="delivered-to"><?php echo htmlspecialchars($activity['delivered_to']); ?></td>
                            <td class="si-date"><?php echo htmlspecialchars($display_date); ?></td>
                            <td>
                                <span class="action-badge badge-view">
                                    <i class="fas fa-cogs"></i>
                                    <?php echo htmlspecialchars($activity['type']); ?>
                                </span>
                            </td>
                            <td class="time-cell">
                                <span class="date"><?php echo date('M d, Y', strtotime($activity['created_at'])); ?></span>
                                <span class="time"><?php echo date('H:i:s', strtotime($activity['created_at'])); ?></span>
                            </td>
                            <td>
                                <?php if (!empty($activity['status'])): ?>
                                    <span class="action-badge badge-view">
                                        <i class="fa-solid fa-file"></i>
                                        <strong><?php echo htmlspecialchars($activity['status']); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn-view-details" onclick="viewDetails(<?php echo htmlspecialchars(json_encode($activity)); ?>)">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination" id="paginationContainer">
                <div class="pagination-info" id="paginationInfo">
                    Showing 0 of 0 records
                </div>
                <div class="pagination-controls" id="paginationControls">
                    <!-- Will be populated by JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <script>
        // ============================================
        // MAIN JS - CLIENT-SIDE SEARCH & FILTERING
        // ============================================
        let currentPage = 1;
        const recordsPerPage = 10;
        let allActivities = [];
        let filteredActivities = [];
        let searchTimeout;

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('History Activity Log - Initializing...');

            // Get all table rows
            const rows = document.querySelectorAll('#tableBody tr');
            console.log(`Found ${rows.length} rows`);

            // Initialize the table data
            allActivities = Array.from(rows).map((row, index) => {
                const cells = row.querySelectorAll('td');

                // Get the date from data attribute
                const dateStr = row.getAttribute('data-date');
                const type = row.getAttribute('data-type');
                const searchable = row.getAttribute('data-searchable');

                console.log(`Row ${index}: date="${dateStr}", type="${type}"`);

                return {
                    element: row,
                    searchableText: searchable || '',
                    type: type || '',
                    date: dateStr || '',
                    id: row.getAttribute('data-id') || ''
                };
            });

            filteredActivities = [...allActivities];

            // Setup event listeners
            const searchInput = document.getElementById('searchInput');
            const dateFrom = document.getElementById('dateFrom');
            const dateTo = document.getElementById('dateTo');
            const typeFilter = document.getElementById('typeFilter');

            // Search with debouncing
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    filterTable();
                }, 300);
            });

            // Date filters - use 'input' event for immediate response
            dateFrom.addEventListener('input', filterTable);
            dateTo.addEventListener('input', filterTable);
            typeFilter.addEventListener('change', filterTable);

            // Display first page
            displayPage(currentPage);
            updatePaginationInfo();
            setupPagination();

            console.log('Initialization complete');
        });

        function filterTable() {
            console.log('filterTable called');

            const searchInput = document.getElementById('searchInput');
            const dateFrom = document.getElementById('dateFrom');
            const dateTo = document.getElementById('dateTo');
            const typeFilter = document.getElementById('typeFilter');

            const searchTerm = searchInput.value.toLowerCase().trim();
            const typeValue = typeFilter.value;
            const dateFromValue = dateFrom.value; // YYYY-MM-DD
            const dateToValue = dateTo.value; // YYYY-MM-DD

            console.log('Filters:', {
                searchTerm: searchTerm,
                typeValue: typeValue,
                dateFromValue: dateFromValue,
                dateToValue: dateToValue
            });

            filteredActivities = allActivities.filter(activity => {
                // Debug each activity
                console.log('Checking activity:', {
                    id: activity.id,
                    date: activity.date,
                    type: activity.type,
                    searchableText: activity.searchableText.substring(0, 50) + '...'
                });

                // Search filter
                if (searchTerm && !activity.searchableText.includes(searchTerm)) {
                    console.log(`Activity ${activity.id} filtered out by search`);
                    return false;
                }

                // Type filter
                if (typeValue && activity.type !== typeValue) {
                    console.log(`Activity ${activity.id} filtered out by type`);
                    return false;
                }

                // Date range filter
                if (activity.date) {
                    if (dateFromValue && activity.date < dateFromValue) {
                        console.log(`Activity ${activity.id} filtered out by dateFrom: ${activity.date} < ${dateFromValue}`);
                        return false;
                    }
                    if (dateToValue && activity.date > dateToValue) {
                        console.log(`Activity ${activity.id} filtered out by dateTo: ${activity.date} > ${dateToValue}`);
                        return false;
                    }
                }

                console.log(`Activity ${activity.id} passed all filters`);
                return true;
            });

            console.log(`Filtered ${filteredActivities.length} of ${allActivities.length} activities`);

            currentPage = 1;
            displayPage(currentPage);
            updatePaginationInfo();
            updatePaginationControls();
            highlightSearchTerm(searchTerm);
        }

        function displayPage(page) {
            currentPage = page;

            const startIndex = (page - 1) * recordsPerPage;
            const endIndex = startIndex + recordsPerPage;
            const pageActivities = filteredActivities.slice(startIndex, endIndex);

            // Hide all rows first
            allActivities.forEach(activity => {
                activity.element.style.display = 'none';
            });

            // Show only rows on current page
            pageActivities.forEach(activity => {
                activity.element.style.display = '';
            });

            // Show/hide empty state
            const existingEmptyRow = document.getElementById('emptyStateRow');
            if (filteredActivities.length === 0 && !existingEmptyRow) {
                const emptyRow = document.createElement('tr');
                emptyRow.id = 'emptyStateRow';
                emptyRow.innerHTML = `
                    <td colspan="8" style="text-align: center; padding: 40px;">
                        <div class="empty-state">
                            <i class="fas fa-database"></i>
                            <h3>No records found</h3>
                            <p>Try changing your search criteria</p>
                        </div>
                    </td>
                `;
                document.getElementById('tableBody').appendChild(emptyRow);
            } else if (filteredActivities.length > 0 && existingEmptyRow) {
                existingEmptyRow.remove();
            }
        }

        function updatePaginationInfo() {
            const totalActivities = filteredActivities.length;
            const start = Math.min((currentPage - 1) * recordsPerPage + 1, totalActivities);
            const end = Math.min(currentPage * recordsPerPage, totalActivities);

            document.getElementById('paginationInfo').textContent =
                `Showing ${start} to ${end} of ${totalActivities} records`;

            // Hide pagination if no records
            const paginationContainer = document.getElementById('paginationContainer');
            if (paginationContainer) {
                paginationContainer.style.display = totalActivities === 0 ? 'none' : 'block';
            }
        }

        function setupPagination() {
            const paginationControls = document.getElementById('paginationControls');
            if (!paginationControls) return;

            paginationControls.innerHTML = `
                <button class="pagination-btn" id="firstPage" title="First Page">
                    <i class="fas fa-angle-double-left"></i>
                </button>
                <button class="pagination-btn" id="prevPage" title="Previous Page">
                    <i class="fas fa-angle-left"></i>
                </button>
                <div id="pageNumbers"></div>
                <button class="pagination-btn" id="nextPage" title="Next Page">
                    <i class="fas fa-angle-right"></i>
                </button>
                <button class="pagination-btn" id="lastPage" title="Last Page">
                    <i class="fas fa-angle-double-right"></i>
                </button>
            `;

            attachPaginationListeners();
            updatePaginationControls();
        }

        function attachPaginationListeners() {
            document.getElementById('firstPage')?.addEventListener('click', () => goToPage(1));
            document.getElementById('prevPage')?.addEventListener('click', prevPage);
            document.getElementById('nextPage')?.addEventListener('click', nextPage);
            document.getElementById('lastPage')?.addEventListener('click', goToLastPage);
        }

        function updatePaginationControls() {
            const totalPages = Math.ceil(filteredActivities.length / recordsPerPage);
            const pageNumbersDiv = document.getElementById('pageNumbers');

            if (!pageNumbersDiv) return;

            pageNumbersDiv.innerHTML = '';

            if (totalPages <= 7) {
                for (let i = 1; i <= totalPages; i++) {
                    pageNumbersDiv.appendChild(createPageButton(i));
                }
            } else {
                pageNumbersDiv.appendChild(createPageButton(1));

                if (currentPage > 3) {
                    pageNumbersDiv.appendChild(createEllipsis());
                }

                let startPage = Math.max(2, currentPage - 1);
                let endPage = Math.min(totalPages - 1, currentPage + 1);

                for (let i = startPage; i <= endPage; i++) {
                    if (i > 1 && i < totalPages) {
                        pageNumbersDiv.appendChild(createPageButton(i));
                    }
                }

                if (currentPage < totalPages - 2) {
                    pageNumbersDiv.appendChild(createEllipsis());
                }

                if (totalPages > 1) {
                    pageNumbersDiv.appendChild(createPageButton(totalPages));
                }
            }

            updatePaginationButtonStates(totalPages);
        }

        function createPageButton(pageNumber) {
            const pageBtn = document.createElement('button');
            pageBtn.className = `pagination-btn ${pageNumber === currentPage ? 'active' : ''}`;
            pageBtn.textContent = pageNumber;
            pageBtn.title = `Go to page ${pageNumber}`;
            pageBtn.addEventListener('click', () => goToPage(pageNumber));
            return pageBtn;
        }

        function createEllipsis() {
            const ellipsis = document.createElement('span');
            ellipsis.textContent = '...';
            ellipsis.style.padding = '0 8px';
            ellipsis.style.color = '#666';
            ellipsis.style.alignSelf = 'center';
            return ellipsis;
        }

        function updatePaginationButtonStates(totalPages) {
            const firstPageBtn = document.getElementById('firstPage');
            const prevPageBtn = document.getElementById('prevPage');
            const nextPageBtn = document.getElementById('nextPage');
            const lastPageBtn = document.getElementById('lastPage');

            [firstPageBtn, prevPageBtn].forEach(btn => {
                if (btn) {
                    btn.disabled = currentPage === 1;
                    btn.style.opacity = currentPage === 1 ? '0.5' : '1';
                    btn.style.cursor = currentPage === 1 ? 'not-allowed' : 'pointer';
                }
            });

            [nextPageBtn, lastPageBtn].forEach(btn => {
                if (btn) {
                    btn.disabled = currentPage === totalPages || totalPages === 0;
                    btn.style.opacity = (currentPage === totalPages || totalPages === 0) ? '0.5' : '1';
                    btn.style.cursor = (currentPage === totalPages || totalPages === 0) ? 'not-allowed' : 'pointer';
                }
            });
        }

        function goToPage(page) {
            const totalPages = Math.ceil(filteredActivities.length / recordsPerPage);
            if (page >= 1 && page <= totalPages && page !== currentPage) {
                displayPage(page);
                updatePaginationInfo();
                updatePaginationControls();
            }
        }

        function prevPage() {
            if (currentPage > 1) {
                goToPage(currentPage - 1);
            }
        }

        function nextPage() {
            const totalPages = Math.ceil(filteredActivities.length / recordsPerPage);
            if (currentPage < totalPages) {
                goToPage(currentPage + 1);
            }
        }

        function goToLastPage() {
            const totalPages = Math.ceil(filteredActivities.length / recordsPerPage);
            goToPage(totalPages);
        }

        function highlightSearchTerm(searchTerm) {
            if (searchTerm === '') {
                // Remove highlights
                allActivities.forEach(activity => {
                    const cells = activity.element.querySelectorAll('td');
                    cells.forEach(cell => {
                        if (cell.dataset.originalHtml) {
                            cell.innerHTML = cell.dataset.originalHtml;
                        }
                    });
                });
                return;
            }

            const regex = new RegExp(`(${escapeRegExp(searchTerm)})`, 'gi');

            filteredActivities.forEach(activity => {
                const cells = activity.element.querySelectorAll('td');
                cells.forEach((cell, index) => {
                    // Skip action column (last column)
                    if (index === cells.length - 1) return;

                    const originalHtml = cell.dataset.originalHtml || cell.innerHTML;
                    const container = document.createElement('div');
                    container.innerHTML = originalHtml;

                    const walker = document.createTreeWalker(container, NodeFilter.SHOW_TEXT, null, false);
                    let node;
                    let changed = false;
                    while ((node = walker.nextNode())) {
                        if (regex.test(node.nodeValue)) {
                            const replaced = node.nodeValue.replace(regex, '<span class="highlight">$1</span>');
                            const temp = document.createElement('span');
                            temp.innerHTML = replaced;
                            const frag = document.createDocumentFragment();
                            while (temp.firstChild) frag.appendChild(temp.firstChild);
                            node.parentNode.replaceChild(frag, node);
                            changed = true;
                        }
                        regex.lastIndex = 0;
                    }

                    cell.innerHTML = changed ? container.innerHTML : originalHtml;
                });
            });
        }

        function escapeRegExp(string) {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

        function viewDetails(activity) {
            let details = `ID: ${activity.id}\n`;
            details += `SI Number: ${activity.si_number}\n`;
            details += `DR Number: ${activity.dr_number}\n`;
            details += `Delivered To: ${activity.delivered_to}\n`;
            details += `TIN: ${activity.tin}\n`;
            details += `Address: ${activity.address}\n`;
            details += `Terms: ${activity.terms}\n`;
            details += `SI Date: ${activity.si_date}\n`;
            details += `Type: ${activity.type}\n`;
            if (activity.status) details += `Status: ${activity.status}\n`;
            details += `Created At: ${activity.created_at}\n\n`;
            details += `Particulars:\n${activity.particulars}`;

            const modal = document.createElement('div');
            modal.style.position = 'fixed';
            modal.style.top = '0';
            modal.style.left = '0';
            modal.style.right = '0';
            modal.style.bottom = '0';
            modal.style.background = 'rgba(0,0,0,0.5)';
            modal.style.display = 'flex';
            modal.style.justifyContent = 'center';
            modal.style.alignItems = 'center';
            modal.style.zIndex = '1000';

            modal.innerHTML = `
                <div style="background: white; padding: 20px; border-radius: 8px; max-width: 500px; max-height: 80vh; overflow-y: auto;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h3 style="margin: 0;">Record Details</h3>
                        <button onclick="this.parentElement.parentElement.parentElement.remove()" 
                                style="background: none; border: none; font-size: 20px; cursor: pointer;">×</button>
                    </div>
                    <div style="white-space: pre-wrap; font-family: monospace; font-size: 14px;">${details}</div>
                    <div style="margin-top: 15px; text-align: center;">
                        <button onclick="this.parentElement.parentElement.parentElement.remove()" 
                                style="background: #667eea; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;">
                            Close
                        </button>
                    </div>
                </div>
            `;

            document.body.appendChild(modal);
        }
    </script>
</body>

</html>