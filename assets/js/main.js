// DOM Elements
const joinBtn = document.getElementById('joinBtn');
const headerJoinBtn = document.getElementById('headerJoinBtn');
const joinModal = document.getElementById('joinModal');
const closeModal = document.querySelector('.close');
const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
const mainNav = document.querySelector('.main-nav');

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // Check if cookie consent is needed
    if (!localStorage.getItem('cookieConsent')) {
        document.getElementById('cookieConsent').style.display = 'flex';
    }

    // Initialize carousels
    initFeaturedRecipesCarousel();
    initEventsCarousel();
});

// Modal functionality
if (joinBtn) {
    joinBtn.addEventListener('click', () => {
        joinModal.style.display = 'block';
    });
}

if (headerJoinBtn) {
    headerJoinBtn.addEventListener('click', (e) => {
        e.preventDefault();
        joinModal.style.display = 'block';
    });
}

if (closeModal) {
    closeModal.addEventListener('click', () => {
        joinModal.style.display = 'none';
    });
}

window.addEventListener('click', (e) => {
    if (e.target === joinModal) {
        joinModal.style.display = 'none';
    }
});

// Mobile menu toggle
if (mobileMenuBtn && mainNav) {
    mobileMenuBtn.addEventListener('click', () => {
        mainNav.classList.toggle('active');
    });
}

// Cookie consent
const acceptCookies = document.getElementById('acceptCookies');
if (acceptCookies) {
    acceptCookies.addEventListener('click', () => {
        localStorage.setItem('cookieConsent', 'true');
        document.getElementById('cookieConsent').style.display = 'none';
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize recipe card animations
    animateRecipeCards();

    // Setup save button functionality
    setupSaveButtons();
});

function animateRecipeCards() {
    const recipeCards = document.querySelectorAll('.recipe-card');

    // Intersection Observer for scroll animations
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                // Add delay based on index for staggered animation
                setTimeout(() => {
                    entry.target.classList.add('visible');
                    entry.target.classList.add('animate-card');
                }, index * 100);

                // Stop observing after animation
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    });

    // Observe each recipe card
    recipeCards.forEach(card => {
        observer.observe(card);
    });
}

function setupSaveButtons() {
    const saveButtons = document.querySelectorAll('.save-recipe-btn');

    saveButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Toggle saved state
            this.classList.toggle('saved');

            // Change icon
            const icon = this.querySelector('i');
            if (this.classList.contains('saved')) {
                icon.classList.remove('far');
                icon.classList.add('fas');
                // Add bounce animation
                this.style.transform = 'scale(1.3)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 300);
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
            }

            // Here you would typically send an AJAX request to save/unsave the recipe
            // const recipeId = this.getAttribute('data-recipe-id');
            // saveRecipeToDatabase(recipeId, this.classList.contains('saved'));
        });
    });
}

// Carousel functions
// function initFeaturedRecipesCarousel() {
//     // This would be replaced with actual API calls in production
//     const carousel = document.querySelector('.carousel');
//     if (carousel) {
//         // Mock data - in real app this would come from backend
//         const recipes = [
//             {
//                 title: "Avocado Toast with Poached Eggs",
//                 image: "assets/images/recipes/avocado-toast.jpg",
//                 time: "15 mins",
//                 difficulty: "easy"
//             },
//             {
//                 title: "Homemade Pizza Dough",
//                 image: "assets/images/recipes/pizza-dough.jpg",
//                 time: "2 hours",
//                 difficulty: "medium"
//             },
//             {
//                 title: "Chocolate Lava Cake",
//                 image: "assets/images/recipes/lava-cake.jpg",
//                 time: "30 mins",
//                 difficulty: "medium"
//             }
//         ];
//
//         recipes.forEach(recipe => {
//             const recipeEl = document.createElement('div');
//             recipeEl.className = 'carousel-item';
//             recipeEl.innerHTML = `
//                 <img src="${recipe.image}" alt="${recipe.title}" width="100" height="100">
//                 <div class="carousel-caption">
//                     <h3>${recipe.title}</h3>
//                     <p>${recipe.time} • ${recipe.difficulty}</p>
//                 </div>
//             `;
//             carousel.appendChild(recipeEl);
//         });
//     }
// }

function initEventsCarousel() {
    // Similar implementation for events carousel
}

// Mobile menu functionality
if (mobileMenuBtn && mainNav) {
    mobileMenuBtn.addEventListener('click', () => {
        mainNav.classList.toggle('active');
        mobileMenuBtn.innerHTML = mainNav.classList.contains('active') ?
            '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
    });
}

// Close mobile menu when clicking on a link
const navLinks = document.querySelectorAll('.nav-links a');
navLinks.forEach(link => {
    link.addEventListener('click', () => {
        if (window.innerWidth <= 768) {
            mainNav.classList.remove('active');
            mobileMenuBtn.innerHTML = '<i class="fas fa-bars"></i>';
        }
    });
});

