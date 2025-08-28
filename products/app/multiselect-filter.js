/**
 * Multi-select Tag Filtering for YOOtheme Products Element
 * Supports AND/OR logic for multiple tag selection
 */
(function() {
    'use strict';

    // Initialize multi-select filtering when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initMultiSelectFilters();
    });

    function initMultiSelectFilters() {
        const containers = document.querySelectorAll('.multiselect-filter-container');
        
        containers.forEach(container => {
            const filterLogic = container.getAttribute('data-filter-logic') || 'or';
            const checkboxes = container.querySelectorAll('.filter-tag-checkbox');
            const allCheckbox = container.querySelector('.filter-all-checkbox');
            const gridItems = container.querySelectorAll('.js-filter > div');
            
            if (!checkboxes.length || !gridItems.length) return;

            // Handle "All" checkbox
            if (allCheckbox) {
                allCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        // Uncheck all tag checkboxes
                        checkboxes.forEach(cb => cb.checked = false);
                        showAllItems(gridItems);
                    }
                });
            }

            // Handle individual tag checkboxes
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    // If any tag is selected, uncheck "All"
                    if (this.checked && allCheckbox) {
                        allCheckbox.checked = false;
                    }
                    
                    // If no tags are selected, check "All"
                    const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
                    if (!anyChecked && allCheckbox) {
                        allCheckbox.checked = true;
                        showAllItems(gridItems);
                        return;
                    }
                    
                    // Apply filtering
                    applyMultiSelectFilter(checkboxes, gridItems, filterLogic);
                });
            });

            // Initial state - show all items
            showAllItems(gridItems);
        });
    }

    function applyMultiSelectFilter(checkboxes, gridItems, logic) {
        const container = document.querySelector('.multiselect-filter-container');
        const categoryLogic = container ? container.getAttribute('data-category-logic') || 'or' : 'or';
        const betweenCategoriesLogic = container ? container.getAttribute('data-filter-logic') || 'and' : 'and';
        
        // Group selected tags by category
        const selectedTagsByCategory = {};
        
        Array.from(checkboxes)
            .filter(cb => cb.checked)
            .forEach(cb => {
                const tag = cb.getAttribute('data-tag');
                const category = cb.getAttribute('data-category') || 'general';
                
                if (!selectedTagsByCategory[category]) {
                    selectedTagsByCategory[category] = [];
                }
                selectedTagsByCategory[category].push(tag);
            });

        const categories = Object.keys(selectedTagsByCategory);
        
        if (categories.length === 0) {
            showAllItems(gridItems);
            hideNoResultsMessage();
            return;
        }

        let visibleItemsCount = 0;

        gridItems.forEach(item => {
            const itemTags = getItemTags(item);
            let shouldShow = false;
            
            if (betweenCategoriesLogic === 'and') {
                // AND logic between categories: item must match ALL categories
                shouldShow = categories.every(category => {
                    const categoryTags = selectedTagsByCategory[category];
                    
                    if (categoryLogic === 'or') {
                        // OR within category: match ANY tag in this category
                        return categoryTags.some(tag => itemTags.includes(tag));
                    } else {
                        // AND within category: match ALL tags in this category
                        return categoryTags.every(tag => itemTags.includes(tag));
                    }
                });
            } else {
                // OR logic between categories: item must match ANY category
                shouldShow = categories.some(category => {
                    const categoryTags = selectedTagsByCategory[category];
                    
                    if (categoryLogic === 'or') {
                        // OR within category: match ANY tag in this category
                        return categoryTags.some(tag => itemTags.includes(tag));
                    } else {
                        // AND within category: match ALL tags in this category
                        return categoryTags.every(tag => itemTags.includes(tag));
                    }
                });
            }

            if (shouldShow) {
                showItem(item);
                visibleItemsCount++;
            } else {
                hideItem(item);
            }
        });

        // Show/hide no results message and pagination
        if (visibleItemsCount === 0) {
            showNoResultsMessage();
            hidePagination();
        } else {
            hideNoResultsMessage();
            // Only show pagination if there are enough items to paginate
            const container = document.querySelector('.multiselect-filter-container');
            const itemsPerPage = parseInt(container.getAttribute('data-items-per-page')) || 6;
            if (visibleItemsCount > itemsPerPage) {
                showPagination();
                // Apply pagination to visible items only
                applyPaginationToVisibleItems(gridItems, itemsPerPage);
            } else {
                hidePagination();
            }
        }

        // Trigger custom event for other components
        const event = new CustomEvent('multiselectFilterApplied', {
            detail: { selectedTagsByCategory, visibleItemsCount }
        });
        document.dispatchEvent(event);
    }

    function getItemTags(item) {
        const tagAttr = item.getAttribute('data-tag');
        if (!tagAttr) return [];
        
        // Handle both space-separated and comma-separated tags
        return tagAttr.split(/[\s,]+/).filter(tag => tag.trim());
    }

    function showItem(item) {
        // Get animation type from container data attribute
        const container = document.querySelector('.multiselect-filter-container');
        const animationType = container?.getAttribute('data-filter-animation') || 'fade';
        
        item.style.display = '';
        
        // Apply animation based on filter_animation setting
        if (animationType.includes('fade')) {
            item.style.opacity = '0';
            item.style.transform = 'scale(0.8)';
            item.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            
            setTimeout(() => {
                item.style.opacity = '1';
                item.style.transform = 'scale(1)';
            }, 10);
        } else if (animationType.includes('slide')) {
            item.style.opacity = '0';
            item.style.transform = 'translateY(20px)';
            item.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            
            setTimeout(() => {
                item.style.opacity = '1';
                item.style.transform = 'translateY(0)';
            }, 10);
        } else {
            // Default fade animation
            item.style.opacity = '0';
            item.style.transition = 'opacity 0.3s ease';
            
            setTimeout(() => {
                item.style.opacity = '1';
            }, 10);
        }
    }

    function hideItem(item) {
        // Get animation type from container data attribute
        const container = document.querySelector('.multiselect-filter-container');
        const animationType = container?.getAttribute('data-filter-animation') || 'fade';
        
        // Apply hide animation based on filter_animation setting
        if (animationType.includes('fade')) {
            item.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            item.style.opacity = '0';
            item.style.transform = 'scale(0.8)';
            
            setTimeout(() => {
                item.style.display = 'none';
                item.style.transform = 'scale(1)';
            }, 300);
        } else if (animationType.includes('slide')) {
            item.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            item.style.opacity = '0';
            item.style.transform = 'translateY(-20px)';
            
            setTimeout(() => {
                item.style.display = 'none';
                item.style.transform = 'translateY(0)';
            }, 300);
        } else {
            // Default fade animation
            item.style.transition = 'opacity 0.2s ease';
            item.style.opacity = '0';
            
            setTimeout(() => {
                item.style.display = 'none';
            }, 200);
        }
    }

    function showAllItems(gridItems) {
        gridItems.forEach(item => {
            showItem(item);
        });
        hideNoResultsMessage();
        
        // Apply pagination to all items when showing all
        const container = document.querySelector('.multiselect-filter-container');
        const itemsPerPage = parseInt(container.getAttribute('data-items-per-page')) || 6;
        
        if (gridItems.length > itemsPerPage) {
            showPagination();
            applyPaginationToAllItems(gridItems, itemsPerPage);
        } else {
            hidePagination();
        }
        
        // Restore original pagination style when filters are cleared
        document.dispatchEvent(new CustomEvent('multiselectFilterCleared'));
    }

    function showNoResultsMessage() {
        const noResultsMessage = document.querySelector('.no-results-message');
        if (noResultsMessage) {
            noResultsMessage.style.display = 'block';
            noResultsMessage.classList.add('uk-animation-fade');
        }
    }

    function hideNoResultsMessage() {
        const noResultsMessage = document.querySelector('.no-results-message');
        if (noResultsMessage) {
            noResultsMessage.style.display = 'none';
            noResultsMessage.classList.remove('uk-animation-fade');
        }
    }

    function showPagination() {
        const paginationContainer = document.querySelector('.pagination-container');
        if (paginationContainer) {
            paginationContainer.style.display = 'block';
            paginationContainer.style.visibility = 'visible';
        }
    }

    function hidePagination() {
        const paginationContainer = document.querySelector('.pagination-container');
        if (paginationContainer) {
            paginationContainer.style.display = 'none';
            paginationContainer.style.visibility = 'hidden';
        }
    }

    function applyPaginationToVisibleItems(gridItems, itemsPerPage) {
        let visibleIndex = 0;
        
        gridItems.forEach(item => {
            // Only count visible items for pagination
            if (item.style.display !== 'none' && item.style.opacity !== '0') {
                const pageNumber = Math.floor(visibleIndex / itemsPerPage) + 1;
                
                // Show only first page items, hide the rest
                if (pageNumber === 1) {
                    // Item is already visible from filtering, keep it visible
                } else {
                    // Hide items beyond first page
                    item.style.display = 'none';
                }
                
                visibleIndex++;
            }
        });
        
        resetPaginationToFirstPage();
    }

    function applyPaginationToAllItems(gridItems, itemsPerPage) {
        gridItems.forEach((item, index) => {
            const pageNumber = Math.floor(index / itemsPerPage) + 1;
            
            // Show only first page items, hide the rest
            if (pageNumber === 1) {
                item.style.display = '';
                item.style.opacity = '1';
            } else {
                item.style.display = 'none';
            }
        });
        
        resetPaginationToFirstPage();
    }

    function resetPaginationToFirstPage() {
        const paginationContainer = document.querySelector('.pagination-container');
        if (paginationContainer) {
            const currentPageSpan = paginationContainer.querySelector('.current-page');
            if (currentPageSpan) {
                currentPageSpan.textContent = '1';
            }
            
            // Update active page in pagination
            const pageLinks = paginationContainer.querySelectorAll('.pagination-page');
            pageLinks.forEach((link, index) => {
                if (index === 0) {
                    link.classList.add('uk-active');
                } else {
                    link.classList.remove('uk-active');
                }
            });
        }
    }

    // Export for potential external use
    window.YOOthemeMultiSelectFilter = {
        init: initMultiSelectFilters,
        applyFilter: applyMultiSelectFilter
    };

})();
