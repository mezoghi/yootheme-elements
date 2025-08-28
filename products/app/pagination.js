/**
 * Pagination functionality for YOOtheme Products Element
 * Integrates with multi-select filtering system
 */
(function() {
    'use strict';

    // Initialize pagination when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initPagination();
    });

    function initPagination() {
        const containers = document.querySelectorAll('[data-pagination="true"]');
        
        containers.forEach(container => {
            const paginationContainer = container.querySelector('.pagination-container');
            if (!paginationContainer) return;

            const gridItems = container.querySelectorAll('.js-filter > div');
            const pagination = paginationContainer.querySelector('.uk-pagination');
            const loadMoreBtn = paginationContainer.querySelector('.load-more-btn');
            
            // Store original pagination style
            container.setAttribute('data-original-pagination-style', container.getAttribute('data-pagination-style') || 'default');
            
            if (pagination) {
                initStandardPagination(container, gridItems, pagination);
            } else if (loadMoreBtn) {
                initLoadMorePagination(container, gridItems, loadMoreBtn);
            }
            
            // Initialize first page display
            showFirstPage(container, gridItems);
        });
    }
    
    function showFirstPage(container, gridItems) {
        const itemsPerPage = parseInt(container.getAttribute('data-items-per-page')) || 6;
        
        gridItems.forEach((item, index) => {
            if (index < itemsPerPage) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }

    function initStandardPagination(container, gridItems, pagination) {
        const totalPages = parseInt(pagination.getAttribute('data-total-pages'));
        let currentPage = 1;

        // Handle page clicks
        pagination.addEventListener('click', function(e) {
            e.preventDefault();
            const target = e.target.closest('li');
            if (!target) return;

            if (target.classList.contains('pagination-prev') && currentPage > 1) {
                goToPage(currentPage - 1);
            } else if (target.classList.contains('pagination-next') && currentPage < totalPages) {
                goToPage(currentPage + 1);
            } else if (target.classList.contains('pagination-page')) {
                const page = parseInt(target.getAttribute('data-page'));
                if (page && page !== currentPage) {
                    goToPage(page);
                }
            }
        });

        function goToPage(page) {
            currentPage = page;
            updatePaginationUI();
            showPageItems(page);
            updatePageInfo();
        }

        function updatePaginationUI() {
            // Update active page
            pagination.querySelectorAll('.pagination-page').forEach(item => {
                item.classList.toggle('uk-active', parseInt(item.getAttribute('data-page')) === currentPage);
            });

            // Update prev/next buttons
            const prevBtn = pagination.querySelector('.pagination-prev');
            const nextBtn = pagination.querySelector('.pagination-next');
            
            prevBtn.classList.toggle('uk-disabled', currentPage <= 1);
            nextBtn.classList.toggle('uk-disabled', currentPage >= totalPages);
        }

        function updatePageInfo() {
            const currentPageSpan = container.querySelector('.current-page');
            if (currentPageSpan) {
                currentPageSpan.textContent = currentPage;
            }
        }

        function showPageItems(page) {
            // Get items per page from container data attribute
            const itemsPerPage = parseInt(container.getAttribute('data-items-per-page')) || 6;
            
            gridItems.forEach((item, index) => {
                // Calculate which page this item should be on based on its index
                const itemPage = Math.floor(index / itemsPerPage) + 1;
                const shouldShow = itemPage === page;
                
                if (shouldShow) {
                    item.style.display = '';
                    item.classList.add('uk-animation-slide-top-small');
                } else {
                    item.style.display = 'none';
                    item.classList.remove('uk-animation-slide-top-small');
                }
            });

            // Remove animation class after animation completes
            setTimeout(() => {
                gridItems.forEach(item => {
                    item.classList.remove('uk-animation-slide-top-small');
                });
            }, 300);
        }

        // Reset to page 1 when filters change
        document.addEventListener('multiselectFilterApplied', function() {
            if (currentPage !== 1) {
                goToPage(1);
            }
        });
    }

    function initLoadMorePagination(container, gridItems, loadMoreBtn) {
        let currentPage = 1;
        const totalPages = parseInt(loadMoreBtn.getAttribute('data-total-pages'));
        const itemsPerPage = parseInt(container.getAttribute('data-items-per-page')) || 6;

        loadMoreBtn.addEventListener('click', function() {
            currentPage++;
            showItemsUpToPage(currentPage);
            
            // Hide button if all pages are shown
            if (currentPage >= totalPages) {
                loadMoreBtn.style.display = 'none';
            }
            
            // Update button text
            const remainingPages = totalPages - currentPage;
            if (remainingPages > 0) {
                loadMoreBtn.textContent = `Load More`;
            }
        });

        function showItemsUpToPage(page) {
            gridItems.forEach((item, index) => {
                const itemPage = Math.floor(index / itemsPerPage) + 1;
                const shouldShow = itemPage <= page;
                
                if (shouldShow && item.style.display === 'none') {
                    item.style.display = '';
                    item.classList.add('uk-animation-slide-top-small');
                    
                    setTimeout(() => {
                        item.classList.remove('uk-animation-slide-top-small');
                    }, 300);
                }
            });
        }

        // Reset load more when filters change
        document.addEventListener('multiselectFilterApplied', function() {
            currentPage = 1;
            loadMoreBtn.style.display = 'block';
            loadMoreBtn.textContent = 'Load More';
            
            // Hide items beyond first page
            gridItems.forEach((item, index) => {
                const itemPage = Math.floor(index / itemsPerPage) + 1;
                if (itemPage > 1) {
                    item.style.display = 'none';
                }
            });
        });
    }

    function updatePaginationAfterFilter(visibleItems) {
        const containers = document.querySelectorAll('[data-pagination="true"]');
        
        containers.forEach(container => {
            const paginationContainer = container.querySelector('.pagination-container');
            if (!paginationContainer) return;
            
            const itemsPerPage = parseInt(container.getAttribute('data-items-per-page')) || 6;
            
            if (visibleItems.length === 0) {
                paginationContainer.style.display = 'none';
            } else if (visibleItems.length <= itemsPerPage) {
                paginationContainer.style.display = 'none';
            } else {
                paginationContainer.style.display = 'block';
                switchToLoadMoreStyle(container, visibleItems);
            }
        });
    }

    function switchToLoadMoreStyle(container, visibleItems) {
        const paginationContainer = container.querySelector('.pagination-container');
        const originalPagination = paginationContainer.querySelector('.uk-pagination');
        const existingLoadMore = paginationContainer.querySelector('.load-more-btn');
        
        // Hide original pagination
        if (originalPagination) {
            originalPagination.style.display = 'none';
        }
        
        // Create or show load more button
        let loadMoreBtn = existingLoadMore;
        if (!loadMoreBtn) {
            loadMoreBtn = createLoadMoreButton(container, visibleItems);
            paginationContainer.appendChild(loadMoreBtn);
        } else {
            loadMoreBtn.style.display = 'block';
            updateLoadMoreButton(loadMoreBtn, container, visibleItems);
        }
        
        // Initialize load more functionality for filtered items
        initLoadMoreForFiltered(container, visibleItems, loadMoreBtn);
    }

    function createLoadMoreButton(container, visibleItems) {
        const itemsPerPage = parseInt(container.getAttribute('data-items-per-page')) || 6;
        const totalPages = Math.ceil(visibleItems.length / itemsPerPage);
        const remainingPages = totalPages - 1;
        
        const loadMoreBtn = document.createElement('button');
        loadMoreBtn.className = 'load-more-btn uk-button uk-button-default uk-align-center';
        loadMoreBtn.textContent = remainingPages > 0 ? `Load More` : 'Load More';
        loadMoreBtn.setAttribute('data-total-pages', totalPages);
        loadMoreBtn.setAttribute('data-current-page', 1);
        
        return loadMoreBtn;
    }

    function updateLoadMoreButton(loadMoreBtn, container, visibleItems) {
        const itemsPerPage = parseInt(container.getAttribute('data-items-per-page')) || 6;
        const totalPages = Math.ceil(visibleItems.length / itemsPerPage);
        const remainingPages = totalPages - 1;
        
        loadMoreBtn.textContent = remainingPages > 0 ? `Load More ` : 'Load More';
        loadMoreBtn.setAttribute('data-total-pages', totalPages);
        loadMoreBtn.setAttribute('data-current-page', 1);
    }

    function initLoadMoreForFiltered(container, visibleItems, loadMoreBtn) {
        const itemsPerPage = parseInt(container.getAttribute('data-items-per-page')) || 6;
        let currentPage = 1;
        
        // Remove existing event listeners
        const newLoadMoreBtn = loadMoreBtn.cloneNode(true);
        loadMoreBtn.parentNode.replaceChild(newLoadMoreBtn, loadMoreBtn);
        
        // Show only first page of filtered items
        showFilteredItemsUpToPage(visibleItems, currentPage, itemsPerPage);
        
        newLoadMoreBtn.addEventListener('click', function() {
            currentPage++;
            showFilteredItemsUpToPage(visibleItems, currentPage, itemsPerPage);
            
            const totalPages = parseInt(newLoadMoreBtn.getAttribute('data-total-pages'));
            
            // Hide button if all pages are shown
            if (currentPage >= totalPages) {
                newLoadMoreBtn.style.display = 'none';
            } else {
                // Update button text
                const remainingPages = totalPages - currentPage;
                newLoadMoreBtn.textContent = `Load More  `;
            }
        });
    }

    function showFilteredItemsUpToPage(visibleItems, page, itemsPerPage) {
        const itemsToShow = page * itemsPerPage;
        
        visibleItems.forEach((item, index) => {
            if (index < itemsToShow) {
                if (item.style.display === 'none') {
                    item.style.display = '';
                    item.classList.add('uk-animation-slide-top-small');
                    
                    setTimeout(() => {
                        item.classList.remove('uk-animation-slide-top-small');
                    }, 300);
                }
            } else {
                item.style.display = 'none';
            }
        });
    }

    function restoreOriginalPagination(container) {
        const paginationContainer = container.querySelector('.pagination-container');
        const originalPagination = paginationContainer.querySelector('.uk-pagination');
        const loadMoreBtn = paginationContainer.querySelector('.load-more-btn');
        
        // Show original pagination
        if (originalPagination) {
            originalPagination.style.display = '';
        }
        
        // Hide load more button
        if (loadMoreBtn) {
            loadMoreBtn.style.display = 'none';
        }
    }

    document.addEventListener('multiselectFilterApplied', function(e) {
        const visibleItems = Array.from(document.querySelectorAll('.js-filter > div'))
            .filter(item => item.style.display !== 'none');
        
        updatePaginationAfterFilter(visibleItems);
    });

    document.addEventListener('multiselectFilterCleared', function(e) {
        const containers = document.querySelectorAll('[data-pagination="true"]');
        containers.forEach(container => {
            restoreOriginalPagination(container);
        });
    });

    window.YOOthemePagination = {
        init: initPagination
    };

})();
