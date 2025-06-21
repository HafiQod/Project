// js/homepage.js
// JavaScript untuk homepage functionality

class FoodHomepage {
    constructor() {
        this.currentFilter = {
            category: 'Semua',
            status: 'Semua'
        };
        this.isLoading = false;
        this.allCards = [];
        
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        this.setupLazyLoading();
        this.setupInfiniteScroll();
        this.loadInitialData();
        this.updateActiveNav();
    }
    
    setupEventListeners() {
        // Filter change events
        const categoryFilter = document.getElementById('categoryFilter');
        const statusFilter = document.getElementById('statusFilter');
        
        if (categoryFilter) {
            categoryFilter.addEventListener('change', (e) => {
                this.currentFilter.category = e.target.value;
                this.applyFilters();
            });
        }
        
        if (statusFilter) {
            statusFilter.addEventListener('change', (e) => {
                this.currentFilter.status = e.target.value;
                this.applyFilters();
            });
        }
        
        // Card click events
        document.addEventListener('click', (e) => {
            const card = e.target.closest('.food-card');
            if (card) {
                this.handleCardClick(card);
            }
        });
        
        // Profile icon click
        const profileIcon = document.querySelector('.profile-icon');
        if (profileIcon) {
            profileIcon.addEventListener('click', () => {
                this.showProfileMenu();
            });
        }
        
        // Refresh on pull down (mobile)
        this.setupPullToRefresh();
        
        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.key === 'r' && e.ctrlKey) {
                e.preventDefault();
                this.refreshData();
            }
        });
    }
    
    setupLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        observer.unobserve(img);
                    }
                });
            });
            
            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    }
    
    setupInfiniteScroll() {
        const observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting && !this.isLoading) {
                this.loadMoreData();
            }
        }, {
            rootMargin: '100px'
        });
        
        const sentinel = document.querySelector('.scroll-sentinel');
        if (sentinel) {
            observer.observe(sentinel);
        }
    }
    
    setupPullToRefresh() {
        let startY = 0;
        let currentY = 0;
        let pullDistance = 0;
        const pullThreshold = 60;
        
        document.addEventListener('touchstart', (e) => {
            if (window.scrollY === 0) {
                startY = e.touches[0].clientY;
            }
        });
        
        document.addEventListener('touchmove', (e) => {
            if (window.scrollY === 0 && startY) {
                currentY = e.touches[0].clientY;
                pullDistance = currentY - startY;
                
                if (pullDistance > 0) {
                    e.preventDefault();
                    this.showPullToRefreshIndicator(pullDistance, pullThreshold);
                }
            }
        });
        
        document.addEventListener('touchend', () => {
            if (pullDistance > pullThreshold) {
                this.refreshData();
            }
            this.hidePullToRefreshIndicator();
            startY = 0;
            pullDistance = 0;
        });
    }
    
    loadInitialData() {
        this.showLoadingState();
        
        // Simulate API call delay
        setTimeout(() => {
            this.hideLoadingState();
            this.updateCardsDisplay();
        }, 1000);
    }
    
    loadMoreData() {
        if (this.isLoading) return;
        
        this.isLoading = true;
        this.showLoadMoreIndicator();
        
        // Simulate loading more data
        setTimeout(() => {
            this.hideLoadMoreIndicator();
            this.isLoading = false;
            // In real implementation, append new cards here
        }, 1500);
    }
    
    refreshData() {
        this.showRefreshIndicator();
        
        // Simulate refresh
        setTimeout(() => {
            this.hideRefreshIndicator();
            this.updateCardsDisplay();
            this.showToast('Data berhasil diperbarui', 'success');
        }, 1000);
    }
    
    applyFilters() {
        this.showLoadingState();
        
        // Simulate filter processing
        setTimeout(() => {
            this.hideLoadingState();
            this.updateCardsDisplay();
            this.updateURL();
        }, 500);
    }
    
    updateCardsDisplay() {
        const cardsContainer = document.querySelector('.cards-grid');
        if (!cardsContainer) return;
        
        // Get all cards
        this.allCards = Array.from(cardsContainer.querySelectorAll('.food-card'));
        
        // Apply filters
        this.allCards.forEach(card => {
            const shouldShow = this.shouldShowCard(card);
            card.style.display = shouldShow ? 'block' : 'none';
        });
        
        // Show empty state if no cards visible
        this.updateEmptyState();
        
        // Update results count
        this.updateResultsCount();
    }
    
    shouldShowCard(card) {
        const category = card.dataset.category || '';
        const status = card.dataset.status || '';
        
        const categoryMatch = this.currentFilter.category === 'Semua' || 
                             category === this.currentFilter.category;
        const statusMatch = this.currentFilter.status === 'Semua' || 
                           status === this.currentFilter.status;
        
        return categoryMatch && statusMatch;
    }
    
    updateEmptyState() {
        const visibleCards = this.allCards.filter(card => 
            card.style.display !== 'none'
        );
        
        let emptyState = document.querySelector('.empty-state');
        
        if (visibleCards.length === 0) {
            if (!emptyState) {
                emptyState = this.createEmptyState();
                document.querySelector('.main-content').appendChild(emptyState);
            }
            emptyState.style.display = 'block';
        } else if (emptyState) {
            emptyState.style.display = 'none';
        }
    }
    
    createEmptyState() {
        const emptyState = document.createElement('div');
        emptyState.className = 'empty-state';
        emptyState.innerHTML = `
            <svg viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
            </svg>
            <h3>Tidak ada makanan ditemukan</h3>
            <p>Coba ubah filter atau periksa kembali nanti untuk melihat donasi makanan terbaru.</p>
        `;
        return emptyState;
    }
    
    updateResultsCount() {
        const visibleCards = this.allCards.filter(card => 
            card.style.display !== 'none'
        );
        
        let countElement = document.querySelector('.results-count');
        if (!countElement) {
            countElement = document.createElement('div');
            countElement.className = 'results-count';
            countElement.style.cssText = `
                color: #666;
                font-size: 0.9rem;
                margin-bottom: 15px;
                text-align: center;
            `;
            const sectionTitle = document.querySelector('.section-title');
            if (sectionTitle) {
                sectionTitle.parentNode.insertBefore(countElement, sectionTitle.nextSibling);
            }
        }
        
        const total = this.allCards.length;
        const visible = visibleCards.length;
        countElement.textContent = `Menampilkan ${visible} dari ${total} donasi makanan`;
    }
    
    handleCardClick(card) {
        const foodId = card.dataset.id;
        if (foodId) {
            // Add click animation
            card.style.transform = 'scale(0.98)';
            setTimeout(() => {
                card.style.transform = '';
            }, 150);
            
            // Show food detail modal or navigate
            this.showFoodDetail(foodId);
        }
    }
    
    showFoodDetail(foodId) {
        // Create and show modal with food details
        const modal = this.createFoodDetailModal(foodId);
        document.body.appendChild(modal);
        
        // Show modal with animation
        setTimeout(() => {
            modal.classList.add('show');
        }, 10);
    }
    
    createFoodDetailModal(foodId) {
        const modal = document.createElement('div');
        modal.className = 'food-detail-modal';
        modal.innerHTML = `
            <div class="modal-overlay" onclick="this.parentElement.remove()"></div>
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Detail Makanan</h3>
                    <button class="close-btn" onclick="this.closest('.food-detail-modal').remove()">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Detail untuk makanan ID: ${foodId}</p>
                    <p>Fitur ini akan diimplementasi sesuai kebutuhan.</p>
                </div>
            </div>
        `;
        
        // Add modal styles
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease;
        `;
        
        modal.classList.add('show');
        return modal;
    }
    
    showProfileMenu() {
        const existingMenu = document.querySelector('.profile-menu');
        if (existingMenu) {
            existingMenu.remove();
            return;
        }
        
        const menu = document.createElement('div');
        menu.className = 'profile-menu';
        menu.innerHTML = `
            <div class="menu-item" onclick="window.location.href='profile.php'">
                <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                <span>Profil Saya</span>
            </div>
            <div class="menu-item" onclick="window.location.href='settings.php'">
                <svg viewBox="0 0 24 24"><path d="M19.14,12.94c0.04-0.3,0.06-0.61,0.06-0.94c0-0.32-0.02-0.64-0.07-0.94l2.03-1.58c0.18-0.14,0.23-0.41,0.12-0.61 l-1.92-3.32c-0.12-0.22-0.37-0.29-0.59-0.22l-2.39,0.96c-0.5-0.38-1.03-0.7-1.62-0.94L14.4,2.81c-0.04-0.24-0.24-0.41-0.48-0.41 h-3.84c-0.24,0-0.43,0.17-0.47,0.41L9.25,5.35C8.66,5.59,8.12,5.92,7.63,6.29L5.24,5.33c-0.22-0.08-0.47,0-0.59,0.22L2.74,8.87 C2.62,9.08,2.66,9.34,2.86,9.48l2.03,1.58C4.84,11.36,4.8,11.69,4.8,12s0.02,0.64,0.07,0.94l-2.03,1.58 c-0.18,0.14-0.23,0.41-0.12,0.61l1.92,3.32c0.12,0.22,0.37,0.29,0.59,0.22l2.39-0.96c0.5,0.38,1.03,0.7,1.62,0.94l0.36,2.54 c0.05,0.24,0.24,0.41,0.48,0.41h3.84c0.24,0,0.44-0.17,0.47-0.41l0.36-2.54c0.59-0.24,1.13-0.56,1.62-0.94l2.39,0.96 c0.22,0.08,0.47,0,0.59-0.22l1.92-3.32c0.12-0.22,0.07-0.47-0.12-0.61L19.14,12.94z M12,15.6c-1.98,0-3.6-1.62-3.6-3.6 s1.62-3.6,3.6-3.6s3.6,1.62,3.6,3.6S13.98,15.6,12,15.6z"/></svg>
                <span>Pengaturan</span>
            </div>
            <div class="menu-item" onclick="this.closest('.profile-menu').remove(); logout();">
                <svg viewBox="0 0 24 24"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.59L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/></svg>
                <span>Logout</span>
            </div>
        `;
        
        // Position and style the menu
        menu.style.cssText = `
            position: absolute;
            top: 60px;
            right: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            z-index: 1000;
            min-width: 180px;
            animation: slideDown 0.3s ease;
        `;
        
        document.body.appendChild(menu);
        
        // Close menu when clicking outside
        setTimeout(() => {
            document.addEventListener('click', function closeMenu(e) {
                if (!menu.contains(e.target) && !e.target.closest('.profile-icon')) {
                    menu.remove();
                    document.removeEventListener('click', closeMenu);
                }
            });
        }, 100);
    }
    
    updateActiveNav() {
        const currentPage = window.location.pathname.split('/').pop();
        const navItems = document.querySelectorAll('.nav-item');
        
        navItems.forEach(item => {
            item.classList.remove('active');
            if (item.getAttribute('href') === currentPage) {
                item.classList.add('active');
            }
        });
    }
    
    updateURL() {
        const url = new URL(window.location);
        url.searchParams.set('category', this.currentFilter.category);
        url.searchParams.set('status', this.currentFilter.status);
        window.history.replaceState({}, '', url);
    }
    
    showLoadingState() {
        const cardsContainer = document.querySelector('.cards-grid');
        if (cardsContainer) {
            cardsContainer.innerHTML = this.createLoadingCards();
        }
    }
    
    hideLoadingState() {
        // This will be called after actual data is loaded
        // The loading cards will be replaced with real content
    }
    
    createLoadingCards() {
        return `
            <div class="loading-card">
                <div class="loading-skeleton skeleton-header"></div>
                <div class="loading-skeleton skeleton-image"></div>
                <div class="loading-skeleton skeleton-text"></div>
                <div class="loading-skeleton skeleton-text-small"></div>
            </div>
            <div class="loading-card">
                <div class="loading-skeleton skeleton-header"></div>
                <div class="loading-skeleton skeleton-image"></div>
                <div class="loading-skeleton skeleton-text"></div>
                <div class="loading-skeleton skeleton-text-small"></div>
            </div>
        `;
    }
    
    showLoadMoreIndicator() {
        let indicator = document.querySelector('.load-more-indicator');
        if (!indicator) {
            indicator = document.createElement('div');
            indicator.className = 'load-more-indicator';
            indicator.innerHTML = `
                <div class="spinner"></div>
                <span>Memuat lebih banyak...</span>
            `;
            indicator.style.cssText = `
                text-align: center;
                padding: 20px;
                color: #666;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
            `;
            document.querySelector('.main-content').appendChild(indicator);
        }
    }
    
    hideLoadMoreIndicator() {
        const indicator = document.querySelector('.load-more-indicator');
        if (indicator) {
            indicator.remove();
        }
    }
    
    showRefreshIndicator() {
        let indicator = document.querySelector('.refresh-indicator');
        if (!indicator) {
            indicator = document.createElement('div');
            indicator.className = 'refresh-indicator';
            indicator.innerHTML = `
                <div class="spinner"></div>
                <span>Memperbarui data...</span>
            `;
            indicator.style.cssText = `
                position: fixed;
                top: 80px;
                left: 50%;
                transform: translateX(-50%);
                background: white;
                padding: 10px 20px;
                border-radius: 25px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                z-index: 1000;
                display: flex;
                align-items: center;
                gap: 10px;
                font-size: 0.9rem;
                color: #333;
            `;
            document.body.appendChild(indicator);
        }
    }
    
    hideRefreshIndicator() {
        const indicator = document.querySelector('.refresh-indicator');
        if (indicator) {
            indicator.remove();
        }
    }
    
    showPullToRefreshIndicator(distance, threshold) {
        let indicator = document.querySelector('.pull-refresh-indicator');
        if (!indicator) {
            indicator = document.createElement('div');
            indicator.className = 'pull-refresh-indicator';
            indicator.innerHTML = '↓ Tarik untuk memperbarui';
            indicator.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                background: #5fb3a3;
                color: white;
                text-align: center;
                padding: 10px;
                transform: translateY(-100%);
                transition: transform 0.3s ease;
                z-index: 1000;
            `;
            document.body.appendChild(indicator);
        }
        
        const progress = Math.min(distance / threshold, 1);
        indicator.style.transform = `translateY(${-100 + (progress * 100)}%)`;
        
        if (progress >= 1) {
            indicator.innerHTML = '↑ Lepas untuk memperbarui';
        } else {
            indicator.innerHTML = '↓ Tarik untuk memperbarui';
        }
    }
    
    hidePullToRefreshIndicator() {
        const indicator = document.querySelector('.pull-refresh-indicator');
        if (indicator) {
            indicator.style.transform = 'translateY(-100%)';
            setTimeout(() => {
                indicator.remove();
            }, 300);
        }
    }
    
    showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.textContent = message;
        toast.style.cssText = `
            position: fixed;
            bottom: 100px;
            left: 50%;
            transform: translateX(-50%);
            background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#007bff'};
            color: white;
            padding: 12px 24px;
            border-radius: 25px;
            z-index: 1000;
            font-size: 0.9rem;
            animation: slideUp 0.3s ease;
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 3000);
    }
}

// Global functions
function logout() {
    if (confirm('Apakah Anda yakin ingin logout?')) {
        // Determine user type from current page
        const isProvider = window.location.pathname.includes('Beri');
        const logoutUrl = isProvider ? 'logoutBeri.php' : 'logoutTerima.php';
        window.location.href = logoutUrl;
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Add necessary CSS animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateX(-50%) translateY(20px); }
            to { opacity: 1; transform: translateX(-50%) translateY(0); }
        }
        
        .spinner {
            width: 16px;
            height: 16px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #5fb3a3;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .menu-item:last-child {
            border-bottom: none;
        }
        
        .menu-item:hover {
            background-color: #f8f9fa;
        }
        
        .menu-item svg {
            width: 18px;
            height: 18px;
            fill: #666;
        }
        
        .menu-item span {
            font-size: 0.9rem;
            color: #333;
        }
        
        .food-detail-modal.show {
            opacity: 1;
        }
        
        .modal-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            border-radius: 15px;
            padding: 20px;
            max-width: 90%;
            max-height: 90%;
            overflow: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #999;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        
        .close-btn:hover {
            background: #f0f0f0;
            color: #333;
        }
    `;
    document.head.appendChild(style);
    
    // Initialize the homepage
    new FoodHomepage();
});