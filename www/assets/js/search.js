function getSearchParams() {
    const searchForm = document.getElementById('search-form');
    const formData = new FormData(searchForm);
    const params = new URLSearchParams();
    
    for (const [key, value] of formData.entries()) {
        if (value && value !== 'all') {
            params.append(key, value);
        }
    }
    
    return params;
}

function updateUrlWithSearchParams() {
    const params = getSearchParams();
    const currentPage = document.body.dataset.currentPage || 'list';
    window.history.pushState({}, '', `/?page=${currentPage}&${params.toString()}`);
}

function performSearch() {
    const params = getSearchParams();
    
    fetch(`/?page=search&${params.toString()}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        // Vérifier si l'utilisateur est admin
        const isAdmin = document.querySelector('.user-greeting')?.textContent.includes('Admin');
        
        if (typeof updateSearchResults === 'function') {
            // Passer l'information isAdmin à la fonction d'affichage
            updateSearchResults(data, isAdmin);
        }
        
        if (typeof updateMap === 'function') {
            updateMap(data, isAdmin);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Initialisation commune
function initSearchForm() {
    const searchForm = document.getElementById('search-form');
    
    if (searchForm) {
        const urlParams = new URLSearchParams(window.location.search);
        
        ['search', 'arrondissement', 'status', 'site_type'].forEach(param => {
            const element = searchForm.querySelector(`[name="${param}"]`);
            if (element && urlParams.has(param)) {
                element.value = urlParams.get(param);
            }
        });
        
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            updateUrlWithSearchParams();
            performSearch();
        });
        
        const inputs = searchForm.querySelectorAll('input, select');
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                updateUrlWithSearchParams();
                performSearch();
            });
        });
        
        if (urlParams.toString()) {
            performSearch();
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const body = document.body;
    const page = new URLSearchParams(window.location.search).get('page') || 'list';
    body.dataset.currentPage = page;
    
    initSearchForm();
});