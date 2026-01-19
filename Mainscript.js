//
// MAIN JS - IMPROVED VERSION
//
let currentPage = 1;
const recordsPerPage = 10;
let allInvoices = [];
let filteredInvoices = [];

// DOM Elements
const invoicesTableBody = document.getElementById('invoicesTableBody');
const searchInput = document.getElementById('searchInput');
const totalRowsElement = document.getElementById('totalRows');

// Debounce function for search optimization
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Single initialization on page load
let initialized = false;
document.addEventListener('DOMContentLoaded', function() {
    if (initialized) return;
    initialized = true;
    
    initializeTable();
    setupPagination();
    checkAndShowModals();
    setupModalEventListeners();
    setupButtonVisibility();
    
    // Add debounced search for better performance
    const debouncedSearch = debounce(searchInvoices, 300);
    searchInput.addEventListener('input', debouncedSearch);
    
    // Handle browser back/forward buttons
    window.addEventListener('popstate', checkAndShowModals);
});

function initializeTable() {
    const rows = invoicesTableBody.querySelectorAll('tr');
    const invoiceMap = new Map();
    
    allInvoices = Array.from(rows).map((row, index) => {
        const cells = row.querySelectorAll('td');
        
        // Skip empty rows or rows with insufficient cells
        if (cells.length === 0 || cells.length < 9) return null;
        
        // Get ID from data attribute on btn-view
        const viewButton = row.querySelector('.btn-view');
        const invoiceId = viewButton?.dataset?.id || 
                         (viewButton?.getAttribute('onclick')?.match(/\d+/)?.[0] || null);
        
        if (!invoiceId) return null;
        
        // Store reference to each cell for faster access
        const cellTexts = Array.from(cells).slice(0, 8).map(cell => cell.textContent.trim());
        
        const invoice = {
            id: invoiceId,
            index: index,
            si_number: cellTexts[0],
            dr_number: cellTexts[1],
            delivered_to: cellTexts[2],
            tin: cellTexts[3],
            address: cellTexts[4],
            terms: cellTexts[5],
            particulars: cellTexts[6],
            si_date: cellTexts[7],
            rowElement: row,
            searchableText: cellTexts.join(' ').toLowerCase(),
            actionContainer: row.querySelector('.action-buttons') // Cache button container
        };
        
        invoiceMap.set(invoice, true); // For O(1) lookup
        return invoice;
    }).filter(invoice => invoice !== null && invoice.id !== null);
    
    filteredInvoices = [...allInvoices];
    
    // Show all rows initially
    showAllRows();
    updatePaginationInfo();
}

function showAllRows() {
    allInvoices.forEach(invoice => {
        invoice.rowElement.style.display = '';
    });
}

// Optimized search function
function searchInvoices() {
    const searchTerm = searchInput.value.toLowerCase().trim();
    
    if (searchTerm === '') {
        filteredInvoices = [...allInvoices];
    } else {
        // Use pre-computed searchable text for faster searching
        filteredInvoices = allInvoices.filter(invoice => 
            invoice.searchableText.includes(searchTerm)
        );
    }
    
    currentPage = 1;
    displayPage(currentPage);
}

// Optimized display page function
function displayPage(page) {
    currentPage = page;
    
    // Calculate pagination range
    const startIndex = (page - 1) * recordsPerPage;
    const endIndex = startIndex + recordsPerPage;
    const pageInvoices = filteredInvoices.slice(startIndex, endIndex);
    
    // Create a Set for O(1) lookup of invoices on current page
    const pageInvoicesSet = new Set(pageInvoices);
    
    // Show/hide rows efficiently
    allInvoices.forEach(invoice => {
        const isVisible = pageInvoicesSet.has(invoice);
        const display = isVisible ? 'table-row' : 'none';
        
        if (invoice.rowElement.style.display !== display) {
            invoice.rowElement.style.display = display;
        }
        
        // Ensure buttons and container are visible for visible rows
        if (isVisible && invoice.actionContainer) {
            // Set container styles
            invoice.actionContainer.style.cssText = 'display: flex !important; visibility: visible !important; opacity: 1 !important; gap: 8px; align-items: center; justify-content: center; min-width: 200px;';
            
            // Set individual button styles
            const buttons = invoice.actionContainer.querySelectorAll('.btn-action');
            buttons.forEach(btn => {
                btn.style.cssText = 'display: inline-flex !important; visibility: visible !important; opacity: 1 !important; align-items: center; justify-content: center; padding: 8px 12px; height: 34px; min-width: 70px; white-space: nowrap;';
            });
        }
    });
    
    updatePaginationInfo();
    updatePaginationControls();
    highlightSearchTerm();
}

// Update pagination information
function updatePaginationInfo() {
    const totalInvoices = filteredInvoices.length;
    totalRowsElement.textContent = totalInvoices;
    
    // Handle empty state
    const existingEmptyRow = document.getElementById('emptyStateRow');
    
    if (totalInvoices === 0 && !existingEmptyRow) {
        const emptyRow = document.createElement('tr');
        emptyRow.id = 'emptyStateRow';
        emptyRow.innerHTML = `
            <td colspan="9" style="text-align: center; padding: 40px;">
                <div class="empty-state">
                    <i class="fas fa-file-alt"></i>
                    <h3>No invoices found</h3>
                    <p>No invoices match your search criteria</p>
                </div>
            </td>
        `;
        invoicesTableBody.appendChild(emptyRow);
    } else if (totalInvoices > 0 && existingEmptyRow) {
        existingEmptyRow.remove();
    }
}

// Setup pagination controls
function setupPagination() {
    const existingPagination = document.querySelector('.pagination-controls');
    if (existingPagination) {
        existingPagination.remove();
    }
    
    const paginationDiv = document.querySelector('.pagination');
    const controlsDiv = document.createElement('div');
    controlsDiv.className = 'pagination-controls';
    controlsDiv.innerHTML = `
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
    paginationDiv.appendChild(controlsDiv);
    
    // Add event listeners immediately
    attachPaginationListeners();
    updatePaginationControls();
}

function attachPaginationListeners() {
    const firstPageBtn = document.getElementById('firstPage');
    const prevPageBtn = document.getElementById('prevPage');
    const nextPageBtn = document.getElementById('nextPage');
    const lastPageBtn = document.getElementById('lastPage');
    
    if (firstPageBtn) firstPageBtn.addEventListener('click', () => goToPage(1));
    if (prevPageBtn) prevPageBtn.addEventListener('click', prevPage);
    if (nextPageBtn) nextPageBtn.addEventListener('click', nextPage);
    if (lastPageBtn) lastPageBtn.addEventListener('click', goToLastPage);
}

// Update pagination controls
function updatePaginationControls() {
    const totalPages = Math.ceil(filteredInvoices.length / recordsPerPage);
    const pageNumbersDiv = document.getElementById('pageNumbers');
    
    if (!pageNumbersDiv) return;
    
    pageNumbersDiv.innerHTML = '';
    
    // Always show first page
    if (currentPage > 1) {
        const firstPageBtn = createPageButton(1);
        pageNumbersDiv.appendChild(firstPageBtn);
        
        // Add ellipsis if needed
        if (currentPage > 3) {
            const ellipsis = document.createElement('span');
            ellipsis.textContent = '...';
            ellipsis.style.padding = '0 8px';
            pageNumbersDiv.appendChild(ellipsis);
        }
    }
    
    // Show pages around current page
    let startPage = Math.max(2, currentPage - 1);
    let endPage = Math.min(totalPages - 1, currentPage + 1);
    
    for (let i = startPage; i <= endPage; i++) {
        if (i > 1 && i < totalPages) {
            pageNumbersDiv.appendChild(createPageButton(i));
        }
    }
    
    // Always show last page
    if (totalPages > 1 && currentPage < totalPages) {
        // Add ellipsis if needed
        if (currentPage < totalPages - 2) {
            const ellipsis = document.createElement('span');
            ellipsis.textContent = '...';
            ellipsis.style.padding = '0 8px';
            pageNumbersDiv.appendChild(ellipsis);
        }
        
        if (totalPages > 1) {
            pageNumbersDiv.appendChild(createPageButton(totalPages));
        }
    }
    
    // Update button states
    updatePaginationButtonStates(totalPages);
}

function createPageButton(pageNumber) {
    const pageBtn = document.createElement('button');
    pageBtn.className = `pagination-btn ${pageNumber === currentPage ? 'active' : ''}`;
    pageBtn.textContent = pageNumber;
    pageBtn.addEventListener('click', () => goToPage(pageNumber));
    return pageBtn;
}

function updatePaginationButtonStates(totalPages) {
    const firstPageBtn = document.getElementById('firstPage');
    const prevPageBtn = document.getElementById('prevPage');
    const nextPageBtn = document.getElementById('nextPage');
    const lastPageBtn = document.getElementById('lastPage');
    
    if (firstPageBtn) {
        firstPageBtn.disabled = currentPage === 1;
        firstPageBtn.title = currentPage === 1 ? 'Already on first page' : 'First Page';
    }
    
    if (prevPageBtn) {
        prevPageBtn.disabled = currentPage === 1;
        prevPageBtn.title = currentPage === 1 ? 'No previous page' : 'Previous Page';
    }
    
    if (nextPageBtn) {
        nextPageBtn.disabled = currentPage === totalPages || totalPages === 0;
        nextPageBtn.title = currentPage === totalPages ? 'No next page' : 'Next Page';
    }
    
    if (lastPageBtn) {
        lastPageBtn.disabled = currentPage === totalPages || totalPages === 0;
        lastPageBtn.title = currentPage === totalPages ? 'Already on last page' : 'Last Page';
    }
}

// Navigation functions
function goToPage(page) {
    const totalPages = Math.ceil(filteredInvoices.length / recordsPerPage);
    if (page >= 1 && page <= totalPages && page !== currentPage) {
        displayPage(page);
    }
}

function prevPage() {
    if (currentPage > 1) {
        goToPage(currentPage - 1);
    }
}

function nextPage() {
    const totalPages = Math.ceil(filteredInvoices.length / recordsPerPage);
    if (currentPage < totalPages) {
        goToPage(currentPage + 1);
    }
}

function goToLastPage() {
    const totalPages = Math.ceil(filteredInvoices.length / recordsPerPage);
    goToPage(totalPages);
}

// Optimized highlight search term
// function highlightSearchTerm() {
//     const searchTerm = searchInput.value.toLowerCase().trim();
    
//     if (searchTerm === '') {
//         // Remove highlights from all visible rows
//         allInvoices.forEach(invoice => {
//             if (invoice.rowElement.style.display !== 'none') {
//                 const cells = invoice.rowElement.querySelectorAll('td');
//                 cells.forEach(cell => {
//                     if (cell.innerHTML !== cell.textContent) {
//                         cell.innerHTML = cell.textContent;
//                     }
//                 });
//             }
//         });
//         return;
//     }
    
//     const regex = new RegExp(`(${escapeRegExp(searchTerm)})`, 'gi');
    
//     // Only highlight filtered invoices (much faster than checking all)
//     filteredInvoices.forEach(invoice => {
//         const cells = invoice.rowElement.querySelectorAll('td');
//         cells.forEach(cell => {
//             const originalText = cell.textContent;
//             if (originalText.toLowerCase().includes(searchTerm)) {
//                 cell.innerHTML = originalText.replace(regex, '<span class="highlight">$1</span>');
//             } else if (cell.innerHTML !== originalText) {
//                 cell.innerHTML = originalText;
//             }
//         });
//     });
// }
// Optimized highlight search term
function highlightSearchTerm() {
    const searchTerm = searchInput.value.toLowerCase().trim();
    
    if (searchTerm === '') {
        // Remove highlights from all visible rows
        allInvoices.forEach(invoice => {
            if (invoice.rowElement.style.display !== 'none') {
                const cells = invoice.rowElement.querySelectorAll('td');
                cells.forEach(cell => {
                    // Skip the action cell - don't modify it
                    if (!cell.classList.contains('action-cell') && cell.innerHTML !== cell.textContent) {
                        cell.innerHTML = cell.textContent;
                    }
                });
            }
        });
        return;
    }
    
    const regex = new RegExp(`(${escapeRegExp(searchTerm)})`, 'gi');
    
    // Only highlight filtered invoices (much faster than checking all)
    filteredInvoices.forEach(invoice => {
        const cells = invoice.rowElement.querySelectorAll('td');
        cells.forEach(cell => {
            // Skip the action cell - don't modify it
            if (cell.classList.contains('action-cell')) {
                return;
            }
            
            const originalText = cell.textContent;
            if (originalText.toLowerCase().includes(searchTerm)) {
                cell.innerHTML = originalText.replace(regex, '<span class="highlight">$1</span>');
            } else if (cell.innerHTML !== originalText) {
                cell.innerHTML = originalText;
            }
        });
    });
}


// Utility function to escape regex special characters
function escapeRegExp(string) {
    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

// View and Edit invoice functions
function viewInvoice(id) {
    window.location.href = `?view=${id}`;
}

function editInvoice(id) {
    window.location.href = `?edit=${id}`;
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        // Remove the show class
        modal.classList.remove('show');
        modal.style.display = 'none';
        
        // Remove URL parameters when closing modal
        const url = new URL(window.location);
        url.searchParams.delete('view');
        url.searchParams.delete('edit');
        window.history.replaceState({}, '', url);
        
        // Restore body scrolling
        document.body.style.overflow = '';
    }
}

// Setup modal event listeners (called once during initialization)
function setupModalEventListeners() {
    // Use event delegation to avoid multiple listeners
    document.addEventListener('click', function(e) {
        // Close modal when clicking outside (on the modal backdrop)
        if (e.target.classList.contains('modal')) {
            closeModal(e.target.id);
        }
        
        // Close modal with close button
        if (e.target.closest('.modal-close')) {
            const modal = e.target.closest('.modal');
            if (modal) {
                closeModal(modal.id);
            }
        }
    }, true); // Use capture phase for better event handling
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modals = document.querySelectorAll('.modal.show');
            modals.forEach(modal => {
                closeModal(modal.id);
            });
        }
    });
}

// Check and show modals
function checkAndShowModals() {
    const urlParams = new URLSearchParams(window.location.search);
    const viewModal = document.getElementById('viewInvoiceModal');
    const editModal = document.getElementById('editInvoiceModal');

    // Handle view modal
    if (urlParams.has('view')) {
        if (viewModal) {
            viewModal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    } else if (viewModal) {
        viewModal.classList.remove('show');
    }

    // Handle edit modal
    if (urlParams.has('edit')) {
        if (editModal) {
            editModal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    } else if (editModal) {
        editModal.classList.remove('show');
    }
}

// Setup button visibility (called once during initialization)
function setupButtonVisibility() {
    // Ensure buttons are visible on page load
    allInvoices.forEach(invoice => {
        if (invoice.actionContainer) {
            invoice.actionContainer.style.cssText = 'display: flex !important; visibility: visible !important; opacity: 1 !important; gap: 8px; align-items: center; justify-content: center; min-width: 200px;';
            
            // Set individual button styles
            const buttons = invoice.actionContainer.querySelectorAll('.btn-action');
            buttons.forEach(btn => {
                btn.style.cssText = 'display: inline-flex !important; visibility: visible !important; opacity: 1 !important; align-items: center; justify-content: center; padding: 8px 12px; height: 34px; min-width: 70px; white-space: nowrap;';
            });
        }
    });
    
    // Set display property for all action buttons
    document.querySelectorAll('.btn-action').forEach(button => {
        button.style.cssText = 'display: inline-flex !important; visibility: visible !important; opacity: 1;';
    });
}

// Ensure action buttons are visible (lightweight, cached reference)
function ensureActionButtonsVisible(row) {
    if (!row) return;
    
    const actionButtonsContainer = row.querySelector('.action-buttons');
    if (actionButtonsContainer) {
        actionButtonsContainer.style.cssText = 'display: flex !important; visibility: visible !important; opacity: 1 !important; gap: 8px; align-items: center; justify-content: center; min-width: 200px;';
        
        // Ensure individual buttons are styled
        const buttons = actionButtonsContainer.querySelectorAll('.btn-action');
        buttons.forEach(btn => {
            btn.style.cssText = 'display: inline-flex !important; visibility: visible !important; opacity: 1 !important; align-items: center; justify-content: center; padding: 8px 12px; height: 34px; min-width: 70px; white-space: nowrap;';
        });
    }
}