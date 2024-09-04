
    document.getElementById('nav-toggle').addEventListener('click', function () {
        const navLinks = document.querySelector('.nav-links');
        navLinks.classList.toggle('active');
        this.classList.toggle('active');
        this.textContent = this.classList.contains('active') ? '✖' : '☰';

        const head = document.querySelector('.navbar-container');
        if (head) {
            head.classList.toggle('activeHead');
        }
    
        const headLong = document.querySelector('.navbar-container_long');
        if (headLong) {
            headLong.classList.toggle('activeHeadLong');
        }
        
        const activeheadBg = document.querySelector('.activeNav');
        activeheadBg.classList.toggle('activeNavBg');
    });
