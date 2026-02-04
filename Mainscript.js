// MAIN JS - COMPLETE FIXED VERSION WITH WORKING MODALS
let currentPage = 1;
const recordsPerPage = 10;
let allInvoices = [];
let filteredInvoices = [];

// DOM Elements (will be initialized in DOMContentLoaded)
let invoicesTableBody;
let searchInput;
let totalRowsElement;

// ============================================
// MODAL FUNCTIONS - MUST BE IN GLOBAL SCOPE
// ============================================

// Edit invoice function
function editInvoice(id) {
    console.log('Edit invoice clicked with ID:', id);
    // Use query parameter without full page reload
    window.location.href = '?edit=' + id;
}

// View invoice function
function viewInvoice(id) {
    console.log('View invoice clicked with ID:', id);
    // Use query parameter without full page reload
    window.location.href = '?view=' + id;
}

// Close modal function - UPDATED with URL cleanup
function closeModal(modalId) {
    console.log('Closing modal:', modalId);
    
    if (modalId) {
        // Close specific modal
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('show');
            modal.style.display = 'none';
        }
    } else {
        // Hide all modals
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            modal.classList.remove('show');
            modal.style.display = 'none';
        });
    }
    
    // Remove body class
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    
    // Remove URL parameters without reloading page
    cleanURL();
}

// Clean URL function - removes ?edit and ?view parameters
function cleanURL() {
    const currentURL = new URL(window.location);
    const hasEditParam = currentURL.searchParams.has('edit');
    const hasViewParam = currentURL.searchParams.has('view');
    
    if (hasEditParam || hasViewParam) {
        // Remove the parameters
        currentURL.searchParams.delete('edit');
        currentURL.searchParams.delete('view');
        
        // Update URL without reloading page
        window.history.replaceState({}, '', currentURL.toString());
        
        console.log('URL cleaned:', window.location.href);
    }
}

// Show modal function - UPDATED with URL update
function showModal(modalId) {
    console.log('showModal called for:', modalId);
    
    // Wait a tiny bit to ensure DOM is ready
    setTimeout(() => {
        const modal = document.getElementById(modalId);
        if (modal) {
            console.log('Modal element found:', modalId);
            
            // Add the show class
            modal.classList.add('show');
            modal.style.display = 'flex';
            modal.style.opacity = '1';
            modal.style.visibility = 'visible';
            
            // Add body class
            document.body.classList.add('modal-open');
            document.body.style.overflow = 'hidden';
            
            console.log(modalId + ' shown successfully');
            
            // Update URL to reflect modal state (optional)
            updateURLForModal(modalId);
            
        } else {
            console.error(modalId + ' element NOT FOUND in DOM');
            
            // Try to find the modal by looking for any modal
            const allModals = document.querySelectorAll('.modal');
            console.log('All modals found in DOM:', allModals.length);
            allModals.forEach((m, index) => {
                console.log(`Modal ${index}:`, m.id, m.className);
            });
        }
    }, 50);
}
// Update URL for modal state - FIXED VERSION
function updateURLForModal(modalId) {
    const currentURL = new URL(window.location);
    const modal = document.getElementById(modalId);
    
    if (modalId === 'editInvoiceModal') {
        // Get ID from hidden input field in edit form
        const editId = modal?.querySelector('input[name="id"]')?.value;
        if (editId) {
            currentURL.searchParams.set('edit', editId);
            currentURL.searchParams.delete('view');
            window.history.replaceState({}, '', currentURL.toString());
        }
    } else if (modalId === 'viewInvoiceModal') {
        // Get ID from the modal's data attribute or from the URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const viewId = urlParams.get('view');
        
        if (viewId) {
            currentURL.searchParams.set('view', viewId);
            currentURL.searchParams.delete('edit');
            window.history.replaceState({}, '', currentURL.toString());
        } else {
            // Alternative: Try to get ID from the Edit button link in the modal
            const editLink = modal?.querySelector('a[href*="?edit="]');
            if (editLink) {
                const href = editLink.getAttribute('href');
                const match = href.match(/edit=(\d+)/);
                if (match && match[1]) {
                    currentURL.searchParams.set('view', match[1]);
                    currentURL.searchParams.delete('edit');
                    window.history.replaceState({}, '', currentURL.toString());
                }
            }
        }
    }
}

// Single initialization on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - Initializing...');
    
    // Initialize DOM elements
    invoicesTableBody = document.getElementById('invoicesTableBody');
    searchInput = document.getElementById('searchInput');
    totalRowsElement = document.getElementById('totalRows');
    
    // Check if required elements exist before proceeding
    if (!invoicesTableBody || !searchInput || !totalRowsElement) {
        console.error('Required DOM elements not found');
        return;
    }
    
    initializeTable();
    setupPagination();
    setupModalEventListeners();
    setupButtonVisibility();
    
    // Check URL parameters and show modals on page load
    setTimeout(function() {
        checkAndShowModalsFromURL();
    }, 200);
    
    // Add search functionality
    searchInput.addEventListener('input', function() {
        searchInvoices();
    });
    
    // Handle browser back/forward buttons
    window.addEventListener('popstate', function() {
        setTimeout(checkAndShowModalsFromURL, 100);
    });
    
    // Add URL cleanup when page loads with modal parameters
    // This ensures if user refreshes page with modal open, it stays clean
    window.addEventListener('load', function() {
        // Small delay to ensure everything is loaded
        setTimeout(() => {
            const urlParams = new URLSearchParams(window.location.search);
            const hasModalParams = urlParams.has('edit') || urlParams.has('view');
            
            // If no modals are shown but URL has parameters, clean it
            if (hasModalParams) {
                const modals = document.querySelectorAll('.modal');
                const anyModalVisible = Array.from(modals).some(modal => 
                    modal.style.display === 'flex' || 
                    modal.style.display === 'block' ||
                    modal.classList.contains('show')
                );
                
                if (!anyModalVisible) {
                    cleanURL();
                }
            }
        }, 500);
    });
    
    console.log('Initialization complete');
});

function initializeTable() {
    const rows = invoicesTableBody.querySelectorAll('tr');
    
    allInvoices = Array.from(rows).map((row, index) => {
        const cells = row.querySelectorAll('td');
        
        // Skip empty rows or rows with insufficient cells
        if (cells.length === 0 || cells.length < 9) return null;
        
        // Get ID from data attribute on btn-view
        const viewButton = row.querySelector('.btn-view');
        const invoiceId = viewButton?.dataset?.id || null;
        
        if (!invoiceId) return null;
        
        // Store reference to each cell for faster access
        const cellTexts = Array.from(cells).slice(0, 8).map(cell => cell.textContent.trim());
        
        return {
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
            actionContainer: row.querySelector('.action-buttons')
        };
    }).filter(invoice => invoice !== null && invoice.id !== null);
    
    filteredInvoices = [...allInvoices];
    
    // Display first page
    displayPage(currentPage);
    updatePaginationInfo();
}

function searchInvoices() {
    const searchTerm = searchInput.value.toLowerCase().trim();
    
    if (searchTerm === '') {
        filteredInvoices = [...allInvoices];
    } else {
        filteredInvoices = allInvoices.filter(invoice => 
            invoice.searchableText.includes(searchTerm)
        );
    }
    
    currentPage = 1;
    displayPage(currentPage);
    updatePaginationInfo();
}

function displayPage(page) {
    currentPage = page;
    
    const startIndex = (page - 1) * recordsPerPage;
    const endIndex = startIndex + recordsPerPage;
    const pageInvoices = filteredInvoices.slice(startIndex, endIndex);
    
    // Show all invoices first (to reset visibility)
    allInvoices.forEach(invoice => {
        invoice.rowElement.style.display = 'none';
    });
    
    // Show only invoices on current page
    pageInvoices.forEach(invoice => {
        invoice.rowElement.style.display = '';
        
        // Ensure action buttons are visible
        if (invoice.actionContainer) {
            invoice.actionContainer.style.cssText = 'display: flex !important; visibility: visible !important; opacity: 1 !important; gap: 8px; align-items: center; justify-content: center; min-width: 200px;';
            
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

function updatePaginationInfo() {
    const totalInvoices = filteredInvoices.length;
    totalRowsElement.textContent = totalInvoices;
    
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

function updatePaginationControls() {
    const totalPages = Math.ceil(filteredInvoices.length / recordsPerPage);
    const pageNumbersDiv = document.getElementById('pageNumbers');
    
    if (!pageNumbersDiv) return;
    
    pageNumbersDiv.innerHTML = '';
    
    if (totalPages <= 7) {
        // Show all pages if total pages is small
        for (let i = 1; i <= totalPages; i++) {
            pageNumbersDiv.appendChild(createPageButton(i));
        }
    } else {
        // Show first page
        pageNumbersDiv.appendChild(createPageButton(1));
        
        if (currentPage > 3) {
            const ellipsis = document.createElement('span');
            ellipsis.textContent = '...';
            ellipsis.style.padding = '0 8px';
            ellipsis.style.color = '#666';
            pageNumbersDiv.appendChild(ellipsis);
        }
        
        // Show pages around current page
        let startPage = Math.max(2, currentPage - 1);
        let endPage = Math.min(totalPages - 1, currentPage + 1);
        
        for (let i = startPage; i <= endPage; i++) {
            if (i > 1 && i < totalPages) {
                pageNumbersDiv.appendChild(createPageButton(i));
            }
        }
        
        if (currentPage < totalPages - 2) {
            const ellipsis = document.createElement('span');
            ellipsis.textContent = '...';
            ellipsis.style.padding = '0 8px';
            ellipsis.style.color = '#666';
            pageNumbersDiv.appendChild(ellipsis);
        }
        
        // Show last page
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

function updatePaginationButtonStates(totalPages) {
    const firstPageBtn = document.getElementById('firstPage');
    const prevPageBtn = document.getElementById('prevPage');
    const nextPageBtn = document.getElementById('nextPage');
    const lastPageBtn = document.getElementById('lastPage');
    
    if (firstPageBtn) {
        firstPageBtn.disabled = currentPage === 1;
        firstPageBtn.style.opacity = currentPage === 1 ? '0.5' : '1';
        firstPageBtn.style.cursor = currentPage === 1 ? 'not-allowed' : 'pointer';
    }
    
    if (prevPageBtn) {
        prevPageBtn.disabled = currentPage === 1;
        prevPageBtn.style.opacity = currentPage === 1 ? '0.5' : '1';
        prevPageBtn.style.cursor = currentPage === 1 ? 'not-allowed' : 'pointer';
    }
    
    if (nextPageBtn) {
        nextPageBtn.disabled = currentPage === totalPages || totalPages === 0;
        nextPageBtn.style.opacity = (currentPage === totalPages || totalPages === 0) ? '0.5' : '1';
        nextPageBtn.style.cursor = (currentPage === totalPages || totalPages === 0) ? 'not-allowed' : 'pointer';
    }
    
    if (lastPageBtn) {
        lastPageBtn.disabled = currentPage === totalPages || totalPages === 0;
        lastPageBtn.style.opacity = (currentPage === totalPages || totalPages === 0) ? '0.5' : '1';
        lastPageBtn.style.cursor = (currentPage === totalPages || totalPages === 0) ? 'not-allowed' : 'pointer';
    }
}

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

function highlightSearchTerm() {
    const searchTerm = searchInput.value.toLowerCase().trim();
    
    if (searchTerm === '') {
        allInvoices.forEach(invoice => {
            if (invoice.rowElement.style.display !== 'none') {
                const cells = invoice.rowElement.querySelectorAll('td');
                cells.forEach(cell => {
                    if (!cell.classList.contains('action-cell') && cell.innerHTML !== cell.textContent) {
                        cell.innerHTML = cell.textContent;
                    }
                });
            }
        });
        return;
    }
    
    const regex = new RegExp(`(${escapeRegExp(searchTerm)})`, 'gi');
    
    filteredInvoices.forEach(invoice => {
        const cells = invoice.rowElement.querySelectorAll('td');
        cells.forEach(cell => {
            if (cell.classList.contains('action-cell')) return;
            
            const originalText = cell.textContent;
            if (originalText.toLowerCase().includes(searchTerm)) {
                cell.innerHTML = originalText.replace(regex, '<span class="highlight">$1</span>');
            } else if (cell.innerHTML !== originalText) {
                cell.innerHTML = originalText;
            }
        });
    });
}

function escapeRegExp(string) {
    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

function setupModalEventListeners() {
    console.log('Setting up modal event listeners');
    
    // Close modal when clicking outside (on the overlay)
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal') && !e.target.classList.contains('modal-content')) {
            const modalId = e.target.id;
            closeModal(modalId);
        }
    });
    
    // Close modal when clicking X button
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-close')) {
            e.preventDefault();
            e.stopPropagation();
            const modal = e.target.closest('.modal');
            if (modal) {
                closeModal(modal.id);
            }
        }
    });
    
    // Close modal with ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
}

function checkAndShowModalsFromURL() {
    console.log('Checking URL for modal parameters...');
    const urlParams = new URLSearchParams(window.location.search);
    const viewId = urlParams.get('view');
    const editId = urlParams.get('edit');
    
    console.log('URL Parameters - view:', viewId, 'edit:', editId);
    
    // Check if modals exist in DOM
    const viewModal = document.getElementById('viewInvoiceModal');
    const editModal = document.getElementById('editInvoiceModal');
    
    console.log('View modal exists:', !!viewModal);
    console.log('Edit modal exists:', !!editModal);
    
    if (viewId && viewModal) {
        console.log('Showing view modal...');
        showModal('viewInvoiceModal');
    } else if (viewId && !viewModal) {
        console.error('View modal not found in DOM even though viewId exists');
        // Clean URL since modal doesn't exist
        cleanURL();
    }
    
    if (editId && editModal) {
        console.log('Showing edit modal...');
        showModal('editInvoiceModal');
    } else if (editId && !editModal) {
        console.error('Edit modal not found in DOM even though editId exists');
        // Clean URL since modal doesn't exist
        cleanURL();
    }
}

function formatPrice(input) {
    let value = input.value.replace(/[^0-9]/g, '');
    if (value === '') {
        input.value = '';
        return;
    }
    input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

function setupButtonVisibility() {
    allInvoices.forEach(invoice => {
        if (invoice.actionContainer) {
            invoice.actionContainer.style.cssText = 'display: flex !important; visibility: visible !important; opacity: 1 !important; gap: 8px; align-items: center; justify-content: center; min-width: 200px;';
            
            const buttons = invoice.actionContainer.querySelectorAll('.btn-action');
            buttons.forEach(btn => {
                btn.style.cssText = 'display: inline-flex !important; visibility: visible !important; opacity: 1 !important; align-items: center; justify-content: center; padding: 8px 12px; height: 34px; min-width: 70px; white-space: nowrap;';
            });
        }
    });
    
    document.querySelectorAll('.btn-action').forEach(button => {
        button.style.cssText = 'display: inline-flex !important; visibility: visible !important; opacity: 1;';
    });
}