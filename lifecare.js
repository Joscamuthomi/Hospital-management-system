document.addEventListener('DOMContentLoaded', function () {
    const slides = document.querySelectorAll('.slide');
    let currentIndex = 0;
    
    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.classList.remove('active');
            slide.classList.remove('exit');
            slide.style.transform = 'scale(0.8) translateX(100%)'; // Reset position
        });

        slides[index].classList.add('active');
        slides[index].style.transform = 'scale(1.05) translateX(0)'; // Center the active slide
    }

    function transitionSlides() {
        // Add exit class to current slide
        slides[currentIndex].classList.add('exit');
        slides[currentIndex].style.transform = 'scale(0.8) translateX(-100%)'; // Move out to the left

        // Calculate next slide index
        let nextIndex = (currentIndex + 1) % slides.length;
        slides[nextIndex].classList.add('active');
        slides[nextIndex].style.transform = 'scale(1.1) translateX(0)'; // Move in from the right and scale up

        currentIndex = nextIndex;

        // Set a timeout to transition to the next slide after 4 seconds
        setTimeout(transitionSlides, 4000); // Adjust timing as needed
    }

    // Show the initial slide and start the transition
    showSlide(currentIndex);
    setTimeout(transitionSlides, 4000); // Start the slideshow after 4 seconds
});
document.addEventListener('DOMContentLoaded', function() {
    const prevButton = document.querySelector('.prev-button');
    const nextButton = document.querySelector('.next-button');
    const carouselWrapper = document.querySelector('.carousel-wrapper');
    const servicesContainer = document.querySelector('.services-container');
  
    let currentIndex = 0;
    let itemWidth = servicesContainer.querySelector('.service-item').offsetWidth + 20; // width + margin
    const totalItems = servicesContainer.children.length;
  
    function updateCarousel() {
      const offset = -currentIndex * itemWidth;
      servicesContainer.style.transform = `translateX(${offset}px)`;
    }
  
    function updateItemWidth() {
      itemWidth = servicesContainer.querySelector('.service-item').offsetWidth + 20; // update width
      updateCarousel();
    }
  
    prevButton.addEventListener('click', function() {
      if (currentIndex > 0) {
        currentIndex--;
      } else {
        currentIndex = totalItems - Math.floor(carouselWrapper.offsetWidth / itemWidth); // Loop to the last visible items
      }
      updateCarousel();
    });
  
    nextButton.addEventListener('click', function() {
      if (currentIndex < totalItems - Math.floor(carouselWrapper.offsetWidth / itemWidth)) {
        currentIndex++;
      } else {
        currentIndex = 0; // Loop to the first item
      }
      updateCarousel();
    });
  
    window.addEventListener('resize', updateItemWidth); // Update item width on resize
  });
  