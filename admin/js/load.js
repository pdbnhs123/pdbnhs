document.addEventListener('DOMContentLoaded', function() {
    const loadingScreen = document.getElementById('loadingScreen');
    const progressBar = document.querySelector('.progress-bar');

    window.addEventListener('load', function() {
        const duration = 1000; // duration in milliseconds
        const startTime = performance.now();

        // Animate progress bar from 0% to 100% over 3 seconds
        function animateProgress(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1); // value between 0 and 1
            progressBar.style.width = (progress * 100).toFixed(2) + '%';

            if (progress < 1) {
                requestAnimationFrame(animateProgress);
            } else {
                // Progress complete
                // Start fade out
                loadingScreen.classList.add('fade-out');
                // When fade-out transition ends, hide overlay
                loadingScreen.addEventListener('transitionend', () => {
                    loadingScreen.style.display = 'none';
                });
            }
        }

        requestAnimationFrame(animateProgress);
    });
});