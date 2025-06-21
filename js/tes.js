        function closeMessage() {
            document.getElementById('message').classList.remove('show');
        }

        setTimeout(() => {
            const message = document.getElementById('message');
            if (message) {
                message.classList.remove('show');
            }
        }, 5000);

        const observerOptions = {
            threshold: 0.15,
            rootMargin: '0px 0px -50px 0px'
        };

        const bidirectionalObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    entry.target.classList.remove('hidden');
                } else {
                    if (entry.boundingClientRect.top > 0) {
                        entry.target.classList.remove('visible');
                        entry.target.classList.add('hidden');
                    } else {
                        entry.target.classList.remove('visible');
                        entry.target.classList.remove('hidden');
                    }
                }
            });
        }, observerOptions);

        document.querySelectorAll('.fade-in, .fade-in-left, .fade-in-right, .fade-in-scale').forEach(element => {
            bidirectionalObserver.observe(element);
        });

        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        document.querySelector('.contact-form').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.textContent;
            
            submitBtn.textContent = 'Mengirim...';
            submitBtn.disabled = true;
            
        });

        let ticking = false;
        
        function updateParallax() {
            const scrolled = window.pageYOffset;
            const hero = document.querySelector('.hero');
            const rate = scrolled * 0.5;
            
            hero.style.transform = `translateY(${rate}px)`;
            ticking = false;
        }

        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(updateParallax);
                ticking = true;
            }
        });

        const heroButton = document.querySelector('.btn-start');
        let mouseX = 0;
        let mouseY = 0;
        let isHovering = false;

        heroButton.addEventListener('mouseenter', () => {
            isHovering = true;
        });

        heroButton.addEventListener('mouseleave', () => {
            isHovering = false;
        });

        heroButton.addEventListener('mousemove', (e) => {
            const rect = heroButton.getBoundingClientRect();
            mouseX = (e.clientX - rect.left - rect.width / 2) / 10;
            mouseY = (e.clientY - rect.top - rect.height / 2) / 10;
        });

        function animateButton() {
            const time = Date.now() * 0.002;
            const floatY = Math.sin(time) * 3;
            
            if (isHovering) {
                heroButton.style.transform = `translateY(${floatY}px) translateX(${mouseX}px) rotateX(${mouseY}deg) rotateY(${mouseX}deg)`;
            } else {
                heroButton.style.transform = `translateY(${floatY}px)`;
            }
            
            requestAnimationFrame(animateButton);
        }
        
        animateButton();

        const statsSection = document.querySelector('.stats');
        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const statItems = entry.target.querySelectorAll('.stat-item');
                    statItems.forEach((item, index) => {
                        setTimeout(() => {
                            item.classList.add('visible');
                            item.classList.remove('hidden');
                        }, index * 150);
                    });
                }
            });
        }, { threshold: 0.2 });

        if (statsSection) {
            statsObserver.observe(statsSection);
        }

        const scrollTopBtn = document.createElement('button');
        scrollTopBtn.innerHTML = '↑';
        scrollTopBtn.className = 'scroll-top-btn';
        scrollTopBtn.style.cssText = `
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: #5fb3a3;
            color: white;
            border: none;
            border-radius: 50%;
            font-size: 20px;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(95, 179, 163, 0.3);
        `;
        
        document.body.appendChild(scrollTopBtn);

        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                scrollTopBtn.style.opacity = '1';
                scrollTopBtn.style.visibility = 'visible';
            } else {
                scrollTopBtn.style.opacity = '0';
                scrollTopBtn.style.visibility = 'hidden';
            }
        });

        scrollTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        setTimeout(function() {
            const message = document.getElementById('message');
            if (message) {
                message.classList.remove('show');
            }
        }, 5000);

        function closeMessage() {
            const message = document.getElementById('message');
            if (message) {
                message.classList.remove('show');
            }
        }

        document.getElementById('contactForm').addEventListener('submit', function() {
            const submitBtn = document.getElementById('submitBtn');
            const btnText = submitBtn.querySelector('.btn-text');
            const btnLoading = submitBtn.querySelector('.btn-loading');
            
            submitBtn.disabled = true;
            btnText.style.display = 'none';
            btnLoading.style.display = 'inline';
        });