document.addEventListener('DOMContentLoaded', function() {
    const penyumbangCard = document.getElementById('penyumbangCard');
    const penerimaCard = document.getElementById('penerimaCard');
    
    penyumbangCard.addEventListener('click', function() {
        handleCardClick(this, 'loginBeri.php');
    });
    
    penerimaCard.addEventListener('click', function() {
        handleCardClick(this, 'loginTerima.php');
    });
    
    function handleCardClick(card, redirectUrl) {
        card.classList.add('loading');
        
        setTimeout(function() {
            window.location.href = redirectUrl;
        }, 500);
    }
    
    const cards = document.querySelectorAll('.choice-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-15px) scale(1.05)';
        });
        
        card.addEventListener('mouseleave', function() {
            if (!this.classList.contains('loading')) {
                this.style.transform = '';
            }
        });
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === '1') {
            penyumbangCard.click();
        } else if (e.key === '2') {
            penerimaCard.click();
        } else if (e.key === 'Escape') {
            window.location.href = 'index.php';
        }
    });
    
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