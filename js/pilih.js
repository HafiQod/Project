document.addEventListener('DOMContentLoaded', function() {
    const penyumbangCard = document.getElementById('penyumbangCard');
    const penerimaCard = document.getElementById('penerimaCard');
    
    // Add click event listeners
    penyumbangCard.addEventListener('click', function() {
        handleCardClick(this, 'loginBeri.php');
    });
    
    penerimaCard.addEventListener('click', function() {
        handleCardClick(this, 'loginTerima.php');
    });
    
    // Handle card click with loading animation
    function handleCardClick(card, redirectUrl) {
        // Add loading class
        card.classList.add('loading');
        
        // Add loading effect
        setTimeout(function() {
            window.location.href = redirectUrl;
        }, 500);
    }
    
    // Add hover sound effect (optional)
    const cards = document.querySelectorAll('.choice-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            // Add scale effect on hover
            this.style.transform = 'translateY(-15px) scale(1.05)';
        });
        
        card.addEventListener('mouseleave', function() {
            // Reset transform when not hovering
            if (!this.classList.contains('loading')) {
                this.style.transform = '';
            }
        });
    });
    
    // Add keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === '1') {
            penyumbangCard.click();
        } else if (e.key === '2') {
            penerimaCard.click();
        } else if (e.key === 'Escape') {
            window.location.href = 'index.php';
        }
    });
    
    // Add touch support for mobile
    cards.forEach(card => {
        card.addEventListener('touchstart', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });
        
        card.addEventListener('touchend', function() {
            setTimeout(() => {
                if (!this.classList.contains('loading')) {
                    this.style.transform = '';
                }
            }, 100);
        });
    });
});