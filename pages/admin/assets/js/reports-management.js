/**
 * Reports Generation Module JavaScript
 * Handles chart rendering and report interactions
 */

class ReportsManager {
    constructor() {
        this.charts = {};
        this.init();
    }

    init() {
        this.bindEvents();
        this.initializeCharts();
        this.setupDefaultDates();
    }

    // Chart initialization
    initializeCharts() {
        // User registrations chart
        this.initUserRegistrationsChart();
        
        // Book circulation chart
        this.initBookCirculationChart();
        
        // Popular categories chart
        this.initPopularCategoriesChart();
        
        // Monthly activity chart
        this.initMonthlyActivityChart();
    }

    initUserRegistrationsChart() {
        const ctx = document.getElementById('userRegistrationsChart');
        if (!ctx) return;

        this.charts.userRegistrations = new Chart(ctx, {
            type: 'line',
            data: {
                labels: window.chartData?.userRegistrations?.labels || [],
                datasets: [{
                    label: 'New Users',
                    data: window.chartData?.userRegistrations?.data || [],
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'User Registrations Over Time'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    initBookCirculationChart() {
        const ctx = document.getElementById('bookCirculationChart');
        if (!ctx) return;

        this.charts.bookCirculation = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: window.chartData?.bookCirculation?.labels || [],
                datasets: [{
                    label: 'Books Borrowed',
                    data: window.chartData?.bookCirculation?.data || [],
                    backgroundColor: '#10B981',
                    borderColor: '#059669',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Book Circulation Statistics'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    initPopularCategoriesChart() {
        const ctx = document.getElementById('popularCategoriesChart');
        if (!ctx) return;

        this.charts.popularCategories = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: window.chartData?.popularCategories?.labels || [],
                datasets: [{
                    data: window.chartData?.popularCategories?.data || [],
                    backgroundColor: [
                        '#3B82F6',
                        '#10B981', 
                        '#F59E0B',
                        '#EF4444',
                        '#8B5CF6',
                        '#EC4899'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    title: {
                        display: true,
                        text: 'Popular Book Categories'
                    }
                }
            }
        });
    }

    initMonthlyActivityChart() {
        const ctx = document.getElementById('monthlyActivityChart');
        if (!ctx) return;

        this.charts.monthlyActivity = new Chart(ctx, {
            type: 'line',
            data: {
                labels: window.chartData?.monthlyActivity?.labels || [],
                datasets: [
                    {
                        label: 'Borrowings',
                        data: window.chartData?.monthlyActivity?.borrowings || [],
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        fill: false,
                        tension: 0.4
                    },
                    {
                        label: 'Returns',
                        data: window.chartData?.monthlyActivity?.returns || [],
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: false,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Monthly Library Activity'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // Report generation and display
    async generateReport() {
        const reportType = document.getElementById('reportType').value;
        const dateFrom = document.getElementById('dateFrom').value;
        const dateTo = document.getElementById('dateTo').value;
        
        if (!reportType) {
            this.showAlert('Please select a report type', 'error');
            return;
        }
        
        this.showLoading();
        
        try {
            const formData = new FormData();
            formData.append('action', 'generate_report');
            formData.append('report_type', reportType);
            formData.append('date_from', dateFrom);
            formData.append('date_to', dateTo);
            
            const response = await fetch(window.location.href, {
                method: 'POST',
                body: formData
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            this.hideLoading();
            
            if (result.success) {
                this.displayReport(result.data, reportType);
                this.showAlert('Report generated successfully', 'success');
            } else {
                this.showAlert(result.message || 'Error generating report', 'error');
            }
        } catch (error) {
            this.hideLoading();
            this.showAlert('Error generating report: ' + error.message, 'error');
        }
    }

    generateQuickReport(reportType, period) {
        let dateFrom = '';
        let dateTo = '';
        
        if (period === 'month') {
            const now = new Date();
            const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
            const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
            
            dateFrom = firstDay.toISOString().split('T')[0];
            dateTo = lastDay.toISOString().split('T')[0];
        }
        
        document.getElementById('reportType').value = reportType;
        document.getElementById('dateFrom').value = dateFrom;
        document.getElementById('dateTo').value = dateTo;
        
        this.generateReport();
    }

    displayReport(data, reportType) {
        const reportResults = document.getElementById('reportResults');
        const reportContent = document.getElementById('reportContent');
        
        let html = '';
        
        if (data.length === 0) {
            html = `
                <div class="text-center py-12">
                    <div class="text-gray-400 mb-4">
                        <i class="fas fa-chart-bar text-6xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">No data found</h3>
                    <p class="text-gray-500">No data available for the selected criteria</p>
                </div>
            `;
        } else {
            html = '<div class="overflow-x-auto">';
            html += '<table class="w-full border-collapse border border-gray-300">';
            
            // Table headers by type
            html += '<thead class="bg-gray-50"><tr>';
            
            switch (reportType) {
                case 'user_registrations':
                    html += '<th class="border border-gray-300 px-4 py-3 text-left font-semibold">Date</th>';
                    html += '<th class="border border-gray-300 px-4 py-3 text-left font-semibold">New Registrations</th>';
                    break;
                case 'borrowing_activity':
                    html += '<th class="border border-gray-300 px-4 py-3 text-left font-semibold">Date</th>';
                    html += '<th class="border border-gray-300 px-4 py-3 text-left font-semibold">Books Borrowed</th>';
                    break;
                case 'popular_books':
                    html += '<th class="border border-gray-300 px-4 py-3 text-left font-semibold">Book Title</th>';
                    html += '<th class="border border-gray-300 px-4 py-3 text-left font-semibold">Author</th>';
                    html += '<th class="border border-gray-300 px-4 py-3 text-left font-semibold">Borrow Count</th>';
                    break;
            }
            
            html += '</tr></thead><tbody>';
            
            data.forEach(row => {
                html += '<tr class="hover:bg-gray-50">';
                Object.values(row).forEach(cell => {
                    html += `<td class="border border-gray-300 px-4 py-3">${cell}</td>`;
                });
                html += '</tr>';
            });
            
            html += '</tbody></table></div>';
            html += `<div class="mt-4 p-4 bg-blue-50 rounded-lg">`;
            html += `<p class="text-sm text-blue-800"><strong>Total Records:</strong> ${data.length}</p>`;
            html += `</div>`;
        }
        
        reportContent.innerHTML = html;
        reportResults.classList.remove('hidden');
        reportResults.scrollIntoView({ behavior: 'smooth' });
    }

    async exportReport() {
        const reportType = document.getElementById('reportType').value;
        const dateFrom = document.getElementById('dateFrom').value;
        const dateTo = document.getElementById('dateTo').value;
        
        if (!reportType) {
            this.showAlert('Please select a report type first', 'error');
            return;
        }
        
        this.showLoading();
        
        try {
            const formData = new FormData();
            formData.append('action', 'export_report');
            formData.append('report_type', reportType);
            formData.append('date_from', dateFrom);
            formData.append('date_to', dateTo);
            
            const response = await fetch(window.location.href, {
                method: 'POST',
                body: formData
            });
            
            this.hideLoading();
            
            if (response.ok) {
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `${reportType}_report_${new Date().toISOString().split('T')[0]}.csv`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
                
                this.showAlert('Report exported successfully', 'success');
            } else {
                const result = await response.json();
                this.showAlert(result.message || 'Error exporting report', 'error');
            }
        } catch (error) {
            this.hideLoading();
            this.showAlert('Error exporting report', 'error');
        }
    }

    clearResults() {
        const results = document.getElementById('reportResults');
        if (results) {
            results.classList.add('hidden');
        }
    }

    showLoading() {
        const modal = document.getElementById('loadingModal');
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    hideLoading() {
        const modal = document.getElementById('loadingModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    setupDefaultDates() {
        const now = new Date();
        const thirtyDaysAgo = new Date(now.getTime() - (30 * 24 * 60 * 60 * 1000));
        
        const fromInput = document.getElementById('dateFrom');
        const toInput = document.getElementById('dateTo');
        
        if (fromInput) fromInput.value = thirtyDaysAgo.toISOString().split('T')[0];
        if (toInput) toInput.value = now.toISOString().split('T')[0];
    }

    // Additional report generation method for different formats
    async generateReportFile(reportType, format = 'pdf') {
        try {
            const formData = new FormData();
            formData.append('action', 'generate_report');
            formData.append('type', reportType);
            formData.append('format', format);
            
            const response = await fetch(window.location.href, {
                method: 'POST',
                body: formData
            });
            
            if (response.ok) {
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `${reportType}_report.${format}`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
                
                this.showAlert(`${reportType} report generated successfully`, 'success');
            } else {
                this.showAlert('Error generating report', 'error');
            }
        } catch (error) {
            this.showAlert('Error generating report', 'error');
        }
    }

    // Print functionality
    printReport() {
        const printContent = document.querySelector('.print-area');
        if (!printContent) {
            this.showAlert('No content available for printing', 'warning');
            return;
        }

        const originalContents = document.body.innerHTML;
        const printableContents = printContent.innerHTML;

        document.body.innerHTML = `
            <html>
            <head>
                <title>Library System Report</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .stat-card { border: 1px solid #ddd; padding: 15px; margin: 10px 0; }
                    .chart-container { page-break-inside: avoid; margin: 20px 0; }
                    @media print {
                        .no-print { display: none !important; }
                        .page-break { page-break-before: always; }
                    }
                </style>
            </head>
            <body>
                <h1>Garden Library System Report</h1>
                <p>Generated on: ${new Date().toLocaleDateString()}</p>
                ${printableContents}
            </body>
            </html>
        `;

        window.print();
        document.body.innerHTML = originalContents;
        
        // Re-initialize after print
        setTimeout(() => {
            location.reload();
        }, 100);
    }

    // Export functionality
    async exportData(format) {
        try {
            const formData = new FormData();
            formData.append('action', 'export_data');
            formData.append('format', format);
            
            const response = await fetch(window.location.href, {
                method: 'POST',
                body: formData
            });
            
            if (response.ok) {
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `library_data.${format}`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
                
                this.showAlert(`Data exported as ${format.toUpperCase()}`, 'success');
            } else {
                this.showAlert('Error exporting data', 'error');
            }
        } catch (error) {
            this.showAlert('Error exporting data', 'error');
        }
    }

    // Date range filtering
    applyDateFilter() {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        
        if (!startDate || !endDate) {
            this.showAlert('Please select both start and end dates', 'warning');
            return;
        }
        
        if (new Date(startDate) > new Date(endDate)) {
            this.showAlert('Start date cannot be after end date', 'warning');
            return;
        }
        
        const params = new URLSearchParams(window.location.search);
        params.set('start_date', startDate);
        params.set('end_date', endDate);
        
        window.location.search = params.toString();
    }

    clearDateFilter() {
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
        
        const params = new URLSearchParams(window.location.search);
        params.delete('start_date');
        params.delete('end_date');
        
        window.location.search = params.toString();
    }

    // UI helpers
    showAlert(message, type) {
        const alertContainer = document.getElementById('alertContainer');
        if (!alertContainer) return;
        
        const alertDiv = document.createElement('div');
        alertDiv.className = 'transform translate-x-full transition-transform duration-300 mb-2';
        
        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500',
            info: 'bg-blue-500'
        };
        
        const icons = {
            success: 'check-circle',
            error: 'exclamation-triangle',
            warning: 'exclamation-circle',
            info: 'info-circle'
        };
        
        const bgColor = colors[type] || colors.info;
        const icon = icons[type] || icons.info;
        
        alertDiv.innerHTML = `
            <div class="flex items-center text-white ${bgColor} px-4 py-3 rounded-lg">
                <i class="fas fa-${icon} mr-2"></i>
                <span>${message}</span>
            </div>
        `;
        
        alertContainer.appendChild(alertDiv);
        
        setTimeout(() => {
            alertDiv.classList.remove('translate-x-full');
        }, 100);
        
        setTimeout(() => {
            alertDiv.classList.add('translate-x-full');
            setTimeout(() => alertDiv.remove(), 300);
        }, 3000);
    }

    // Event binding
    bindEvents() {
        // Form submission
        const reportForm = document.getElementById('reportForm');
        if (reportForm) {
            reportForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.generateReport();
            });
        }

        // Date filter buttons
        const applyFilterBtn = document.getElementById('applyDateFilter');
        const clearFilterBtn = document.getElementById('clearDateFilter');
        
        if (applyFilterBtn) {
            applyFilterBtn.addEventListener('click', () => this.applyDateFilter());
        }
        
        if (clearFilterBtn) {
            clearFilterBtn.addEventListener('click', () => this.clearDateFilter());
        }

        // Setup default dates
        this.setupDefaultDates();
    }

    // Chart refresh
    refreshCharts() {
        Object.values(this.charts).forEach(chart => {
            if (chart) {
                chart.update();
            }
        });
    }

    // Cleanup
    destroy() {
        Object.values(this.charts).forEach(chart => {
            if (chart) {
                chart.destroy();
            }
        });
        this.charts = {};
    }
}

// Global functions for backward compatibility
let reportsManager;

function generateQuickReport(reportType, period) {
    reportsManager.generateQuickReport(reportType, period);
}

function generateReport() {
    reportsManager.generateReport();
}

function exportReport() {
    reportsManager.exportReport();
}

function clearResults() {
    reportsManager.clearResults();
}

function showLoading() {
    reportsManager.showLoading();
}

function hideLoading() {
    reportsManager.hideLoading();
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    reportsManager = new ReportsManager();
});

// Handle page unload
window.addEventListener('beforeunload', () => {
    if (reportsManager) {
        reportsManager.destroy();
    }
});
