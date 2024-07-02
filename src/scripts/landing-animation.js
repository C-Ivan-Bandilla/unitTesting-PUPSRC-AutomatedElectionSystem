document.addEventListener('DOMContentLoaded', function () {
    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('fade-in', 'slide-in');
          observer.unobserve(entry.target);
        }
      });
    });

    document.querySelectorAll('.organizations .container-fluid').forEach(element => {
      observer.observe(element);
    });
});