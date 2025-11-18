/**
 * Kyle-HMS Main JavaScript
 * Global functions and utilities
 */

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    initScrollTopButton();
    initNavbarScroll();
    initTooltips();
    initConfirmations();
});

/**
 * Scroll to top button
 */
function initScrollTopButton() {
    const scrollBtn = document.getElementById('scrollTopBtn');
    
    if (scrollBtn) {
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                scrollBtn.classList.add('show');
            } else {
                scrollBtn.classList.remove('show');
            }
        });
        
        scrollBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
}

/**
 * Navbar scroll effect
 */
function initNavbarScroll() {
    const navbar = document.getElementById('mainNav');
    
    if (navbar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }
}

/**
 * Initialize Bootstrap tooltips
 */
function initTooltips() {
    const tooltipTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="tooltip"]')
    );
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * Initialize confirmation dialogs
 */
function initConfirmations() {
    const confirmLinks = document.querySelectorAll('[data-confirm]');
    
    confirmLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const message = this.getAttribute('data-confirm');
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
}

/**
 * Show loading spinner
 */
function showLoading(element) {
    const spinner = document.createElement('div');
    spinner.className = 'spinner-border spinner-border-sm me-2';
    spinner.setAttribute('role', 'status');
    
    if (element) {
        element.disabled = true;
        element.insertBefore(spinner, element.firstChild);
    }
}

/**
 * Hide loading spinner
 */
function hideLoading(element) {
    if (element) {
        element.disabled = false;
        const spinner = element.querySelector('.spinner-border');
        if (spinner) {
            spinner.remove();
        }
    }
}

/**
 * Show toast notification
 */
function showToast(message, type = 'info') {
    const toastHtml = `
        <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    const toastElement = toastContainer.lastElementChild;
    const toast = new bootstrap.Toast(toastElement, { autohide: true, delay: 3000 });
    toast.show();
    
    toastElement.addEventListener('hidden.bs.toast', function() {
        this.remove();
    });
}

/**
 * Format currency
 */
function formatCurrency(amount, currency = '$') {
    return currency + parseFloat(amount).toFixed(2);
}

/**
 * Format date
 */
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

/**
 * Format time
 */
function formatTime(timeString) {
    const [hours, minutes] = timeString.split(':');
    const hour = parseInt(hours);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const displayHour = hour % 12 || 12;
    return `${displayHour}:${minutes} ${ampm}`;
}

/**
 * Debounce function
 */
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

/**
 * AJAX helper function
 */
async function fetchData(url, options = {}) {
    try {
        const response = await fetch(url, {
            ...options,
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return await response.json();
    } catch (error) {
        console.error('Fetch error:', error);
        throw error;
    }
}

/**
 * Form validation helper
 */
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return false;
    }
    
    return true;
}

/**
 * Smooth scroll to element
 */
function scrollToElement(elementId, offset = 80) {
    const element = document.getElementById(elementId);
    if (element) {
        const elementPosition = element.getBoundingClientRect().top + window.pageYOffset;
        const offsetPosition = elementPosition - offset;
        
        window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
        });
    }
}

/**
 * Copy to clipboard
 */
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showToast('Copied to clipboard!', 'success');
    }, function(err) {
        console.error('Copy failed:', err);
        showToast('Failed to copy', 'danger');
    });
}

/**
 * Auto-dismiss alerts
 */
function autoDismissAlerts() {
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
}

// Auto-dismiss alerts on page load
document.addEventListener('DOMContentLoaded', autoDismissAlerts);

/**
 * Prevent double form submission
 */
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form[data-prevent-double-submit]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                showLoading(submitBtn);
                
                // Re-enable after 3 seconds as fallback
                setTimeout(() => {
                    submitBtn.disabled = false;
                    hideLoading(submitBtn);
                }, 3000);
            }
        });
    });
});

/**
 * Character counter for textareas
 */
document.addEventListener('DOMContentLoaded', function() {
    const textareas = document.querySelectorAll('textarea[data-max-length]');
    
    textareas.forEach(textarea => {
        const maxLength = parseInt(textarea.getAttribute('data-max-length'));
        const counter = document.createElement('small');
        counter.className = 'form-text text-muted';
        textarea.parentNode.appendChild(counter);
        
        function updateCounter() {
            const remaining = maxLength - textarea.value.length;
            counter.textContent = `${remaining} characters remaining`;
            
            if (remaining < 0) {
                counter.classList.add('text-danger');
                counter.classList.remove('text-muted');
            } else {
                counter.classList.add('text-muted');
                counter.classList.remove('text-danger');
            }
        }
        
        updateCounter();
        textarea.addEventListener('input', updateCounter);
    });
});

/**
 * Print functionality
 */
function printElement(elementId) {
    const element = document.getElementById(elementId);
    if (!element) return;
    
    const printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write('<html><head><title>Print</title>');
    printWindow.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">');
    printWindow.document.write('</head><body>');
    printWindow.document.write(element.innerHTML);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}

/**
 * Export table to CSV
 */
function exportTableToCSV(tableId, filename = 'export.csv') {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    rows.forEach(row => {
        const cols = row.querySelectorAll('td, th');
        const csvRow = [];
        
        cols.forEach(col => {
            csvRow.push('"' + col.textContent.trim().replace(/"/g, '""') + '"');
        });
        
        csv.push(csvRow.join(','));
    });
    
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    
    if (navigator.msSaveBlob) {
        navigator.msSaveBlob(blob, filename);
    } else {
        link.href = URL.createObjectURL(blob);
        link.download = filename;
        link.click();
    }
}

// Export functions for use in other files
window.KyleHMS = {
    showLoading,
    hideLoading,
    showToast,
    formatCurrency,
    formatDate,
    formatTime,
    debounce,
    fetchData,
    validateForm,
    scrollToElement,
    copyToClipboard,
    printElement,
    exportTableToCSV
};