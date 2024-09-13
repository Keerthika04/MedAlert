let currentSlideIndex = 1;
showSlide(currentSlideIndex);

function moveSlide(n) {
    showSlide(currentSlideIndex += n);
}

function currentSlide(n) {
    showSlide(currentSlideIndex = n);
}

function showSlide(n) {
    let slides = document.querySelectorAll('.slide');
    let dots = document.querySelectorAll('.dot');
    if (n > slides.length) { currentSlideIndex = 1 }
    if (n < 1) { currentSlideIndex = slides.length }
    slides.forEach((slide, index) => {
        slide.style.display = (index + 1 === currentSlideIndex) ? 'block' : 'none';
        slide.style.opacity = (index + 1 === currentSlideIndex) ? '1' : '0';
    });
    dots.forEach((dot, index) => {
        dot.className = (index + 1 === currentSlideIndex) ? 'dot active' : 'dot';
    });
}
