// js/admin_new.js
// JavaScript untuk Admin Panel dengan Chart dan Sidebar

class AdminDashboard {
    constructor() {
        this.chart = null;
        this.currentPage = 'dashboard';
        this.init();
    }
    
    init() {
        this.setupSidebar();
        this.setupModals();
        this.setupChart();
        this.setupEventListeners();
        this.loadDashboardData();
        this.updateActiveNav();
    }
    
    setupSidebar() {
        const toggleBtn = document.querySelector('.toggle-btn');
        const sidebar = document.querySelector('.sidebar');
        
        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                sidebar.classList.toggle('collapsed');
                
                // Save state
                const isCollapsed = sidebar.classList.contains('collapsed');
                localStorage.setItem('sidebarCollapsed', isCollapsed);
                
                // Debug
                console.log('Sidebar toggled, collapsed:', isCollapsed);
            });
            
            // Restore sidebar state
            const savedState = localStorage.getItem('sidebarCollapsed');
            if (savedState === 'true') {
                sidebar.classList.add('collapsed');
            }
        }
        
        // Mobile sidebar toggle
        const mobileToggle = document.querySelector('.mobile-toggle');
        if (mobileToggle) {
            mobileToggle.addEventListener('click', () => {
                sidebar.classList.toggle('mobile-open');
            });
        }
    }
    
    setupModals() {
        // Close modal when clicking outside
        window.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal')) {
                e.target.style.display = 'none';
            }
        });
        
        // Close modal with escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const openModal = document.querySelector('.modal[style*="block"]');
                if (openModal) {
                    openModal.style.display = 'none';
                }
            }
        });
    }
    
    setupChart() {
        const ctx = document.getElementById('donationChart');
        if (!ctx) return;
        
        // Chart.js configuration
        this.chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'Donasi Makanan',
                        data: [],
                        borderColor: '#5fb3a3',
                        backgroundColor: 'rgba(95, 179, 163, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#5fb3a3',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    },
                    {
                        label: 'Makanan Diselamatkan',
                        data: [],
                        borderColor: '#27ae60',
                        backgroundColor: 'rgba(39, 174, 96, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#27ae60',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 12,
                                family: 'Arial'
                            }
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#5fb3a3',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: true
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Periode',
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        },
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Jumlah',
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        },
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    }
                },
                elements: {
                    line: {
                        borderJoinStyle: 'round'
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart'
                }
            }
        });
    }
    
    setupEventListeners() {
        // Chart period buttons
        const chartBtns = document.querySelectorAll('.chart-btn');
        chartBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                chartBtns.forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
                
                const period = e.target.dataset.period;
                this.updateChartData(period);
            });
        });
        
        // Navigation links
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const page = link.dataset.page;
                this.navigateToPage(page);
            });
        });
        
        // Logout button
        const logoutBtn = document.querySelector('.logout-btn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', () => {
                if (confirm('Apakah Anda yakin ingin logout?')) {
                    window.location.href = 'admin_dashboard.php?logout=1';
                }
            });
        }
        
        // Auto refresh data every 5 minutes
        setInterval(() => {
            this.refreshData();
        }, 300000);
    }
    
    navigateToPage(page) {
        // Update active navigation
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
        });
        document.querySelector(`[data-page="${page}"]`).classList.add('active');
        
        // Load page content
        this.currentPage = page;
        this.loadPageContent(page);
    }
    
    loadPageContent(page) {
        const contentArea = document.querySelector('.content-area');
        
        switch(page) {
            case 'dashboard':
                window.location.href = 'admin_dashboard.php';
                break;
            case 'saran':
                window.location.href = 'admin_saran.php';
                break;
            case 'users':
                window.location.href = 'admin_users.php';
                break;
            case 'donasi':
                window.location.href = 'admin_donasi.php';
                break;
            case 'settings':
                window.location.href = 'admin_settings.php';
                break;
        }
    }
    
    loadDashboardContent() {
        // Already loaded, just refresh data
        this.loadDashboardData();
    }
    
    async loadDashboardData() {
        try {
            // Show loading
            this.showLoading();
            
            // Try to fetch from API
            const response = await fetch('api/admin_stats.php');
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success) {
                this.updateStats(data.stats);
                this.updateChartData('month', data.chartData);
                this.showSuccess('Data dashboard berhasil dimuat');
            } else {
                throw new Error(data.message || 'Failed to load data');
            }
            
        } catch (error) {
            console.error('Error loading dashboard data:', error);
            
            // Fallback ke dummy data
            this.loadFallbackData();
            this.showError('Menggunakan data demo. API belum tersedia.');
        } finally {
            this.hideLoading();
        }
    }
    
    loadFallbackData() {
        // Load dummy data sebagai fallback
        const dummyStats = {
            totalDonasi: 147,
            donasiTersedia: 23,
            makananDiselamatkan: 892,
            totalUsers: 58,
            saranBaru: 5
        };
        
        this.updateStats(dummyStats);
        this.loadDummyChartData('month');
    }
    
    updateStats(stats) {
        // Update stat cards with animation
        const statElements = {
            'total-donasi': stats.totalDonasi || 0,
            'donasi-tersedia': stats.donasiTersedia || 0,
            'makanan-diselamatkan': stats.makananDiselamatkan || 0,
            'total-users': stats.totalUsers || 0
        };
        
        Object.entries(statElements).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) {
                this.animateCounter(element, value);
            }
        });
    }
    
    animateCounter(element, targetValue) {
        const startValue = parseInt(element.textContent) || 0;
        const duration = 1000;
        const startTime = performance.now();
        
        const animate = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            const currentValue = Math.floor(startValue + (targetValue - startValue) * progress);
            element.textContent = currentValue.toLocaleString();
            
            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        };
        
        requestAnimationFrame(animate);
    }
    
    async updateChartData(period = 'month', data = null) {
        if (!this.chart) return;
        
        try {
            let chartData;
            if (data) {
                chartData = data;
            } else {
                // Fetch new data based on period
                const response = await fetch(`api/chart_data.php?period=${period}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                chartData = await response.json();
            }
            
            // Update chart
            this.chart.data.labels = chartData.labels;
            this.chart.data.datasets[0].data = chartData.donasi;
            this.chart.data.datasets[1].data = chartData.diselamatkan;
            
            this.chart.update('active');
            
        } catch (error) {
            console.error('Error updating chart:', error);
            // Use dummy data for demo
            this.loadDummyChartData(period);
        }
    }
    
    loadDummyChartData(period) {
        if (!this.chart) return;
        
        let labels, donasi, diselamatkan;
        
        if (period === 'year') {
            labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            donasi = [45, 52, 38, 67, 73, 89, 95, 102, 87, 93, 108, 115];
            diselamatkan = [38, 45, 32, 58, 65, 78, 85, 89, 76, 82, 95, 102];
        } else if (period === 'month') {
            labels = Array.from({length: 30}, (_, i) => `${i + 1}`);
            donasi = Array.from({length: 30}, () => Math.floor(Math.random() * 50) + 20);
            diselamatkan = donasi.map(val => Math.floor(val * 0.8) + Math.floor(Math.random() * 10));
        } else {
            labels = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
            donasi = [12, 18, 15, 22, 28, 35, 25];
            diselamatkan = [10, 15, 12, 19, 25, 30, 22];
        }
        
        this.chart.data.labels = labels;
        this.chart.data.datasets[0].data = donasi;
        this.chart.data.datasets[1].data = diselamatkan;
        this.chart.update('active');
    }
    
    updateActiveNav() {
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('.nav-link');
        
        navLinks.forEach(link => {
            link.classList.remove('active');
            const page = link.dataset.page;
            
            if ((currentPath.includes('admin.php') || currentPath.includes('admin_dashboard.php')) && page === 'dashboard') {
                link.classList.add('active');
            } else if (currentPath.includes(`admin_${page}.php`)) {
                link.classList.add('active');
            }
        });
    }
    
    showLoading() {
        document.querySelectorAll('.stat-card, .chart-container').forEach(el => {
            el.classList.add('loading');
        });
    }
    
    hideLoading() {
        document.querySelectorAll('.stat-card, .chart-container').forEach(el => {
            el.classList.remove('loading');
        });
    }
    
    showError(message) {
        this.showToast(message, 'error');
    }
    
    showSuccess(message) {
        this.showToast(message, 'success');
    }
    
    showToast(message, type = 'info') {
        // Remove existing toast
        const existingToast = document.querySelector('.toast');
        if (existingToast) {
            existingToast.remove();
        }
        
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <div class="toast-content">
                <span class="toast-message">${message}</span>
                <button class="toast-close" onclick="this.parentElement.parentElement.remove()">&times;</button>
            </div>
        `;
        
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#27ae60' : type === 'error' ? '#e74c3c' : '#3498db'};
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            z-index: 9999;
            animation: slideInRight 0.3s ease;
            max-width: 400px;
        `;
        
        document.body.appendChild(toast);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (toast.parentNode) {
                toast.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }
        }, 5000);
    }
    
    async refreshData() {
        try {
            await this.loadDashboardData();
            this.showSuccess('Data berhasil diperbarui');
        } catch (error) {
            this.showError('Gagal memperbarui data');
        }
    }
}

// Modal Functions
function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

function showUpdateModal(id, currentStatus) {
    document.getElementById('update_saran_id').value = id;
    document.getElementById('status').value = currentStatus;
    showModal('updateModal');
}

function deleteSaran(id) {
    document.getElementById('delete_saran_id').value = id;
    showModal('deleteModal');
}

function showFullMessage(message) {
    document.getElementById('fullMessageContent').innerHTML = message.replace(/\n/g, '<br>');
    showModal('messageModal');
}

// Chart period switcher
function switchChartPeriod(period, element) {
    // Update active button
    document.querySelectorAll('.chart-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    element.classList.add('active');
    
    // Update chart
    if (window.adminDashboard) {
        window.adminDashboard.updateChartData(period);
    }
}

// Form handlers
function handleFormSubmit(formId, successMessage) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner"></span> Loading...';
        }
        
        // Form will submit normally, success message will be shown after page reload
    });
}

// Initialize dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Add CSS animations for toast
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
        
        .toast-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }
        
        .toast-close {
            background: none;
            border: none;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: background 0.3s ease;
        }
        
        .toast-close:hover {
            background: rgba(255,255,255,0.2);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e6ed;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: border-color 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #5fb3a3;
            box-shadow: 0 0 10px rgba(95, 179, 163, 0.1);
        }
        
        .message-text {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .view-full {
            color: #5fb3a3;
            cursor: pointer;
            text-decoration: underline;
            font-size: 0.85rem;
            margin-top: 5px;
            display: block;
        }
        
        .view-full:hover {
            color: #4a9289;
        }
    `;
    document.head.appendChild(style);
    
    // Initialize admin dashboard
    window.adminDashboard = new AdminDashboard();
    
    // Initialize form handlers
    handleFormSubmit('updateForm', 'Status berhasil diperbarui');
    handleFormSubmit('deleteForm', 'Data berhasil dihapus');
    
    // Auto hide messages after 5 seconds
    setTimeout(function() {
        const messages = document.querySelectorAll('.message');
        messages.forEach(function(message) {
            message.style.opacity = '0';
            setTimeout(function() {
                message.style.display = 'none';
            }, 300);
        });
    }, 5000);
    
    // Initialize chart with dummy data
    if (window.adminDashboard && window.adminDashboard.chart) {
        window.adminDashboard.loadDummyChartData('month');
    }
});

// Export for global access
window.showModal = showModal;
window.closeModal = closeModal;
window.showUpdateModal = showUpdateModal;
window.deleteSaran = deleteSaran;
window.showFullMessage = showFullMessage;
window.switchChartPeriod = switchChartPeriod;