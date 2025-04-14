// assets/js/map-search.js

document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('search-form');
    
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            performMapSearch();
        });
        
        const inputs = searchForm.querySelectorAll('input, select');
        inputs.forEach(input => {
            input.addEventListener('input', performMapSearch); // Changé en 'input' pour réactivité
        });
    }
});

function performMapSearch() {
    const searchForm = document.getElementById('search-form');
    const formData = new FormData(searchForm);
    const params = new URLSearchParams();
    
    for (const [key, value] of formData.entries()) {
        if (value) params.append(key, value);
    }
    
    fetch(`/search?${params.toString()}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        updateMap(data);
    })
    .catch(error => {
        console.error('Error:', error);
    });
}