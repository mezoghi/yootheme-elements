<?php

// Resets
if ($props['panel_link']) {
    $props['title_link'] = '';
    $props['image_link'] = '';
}

// Override default settings
if (!$props['grid_parallax'] && $props['grid_parallax_justify']) {
    $props['grid_parallax'] = '0';
}

$el = $this->el('div', [

    'uk-filter' => $tags && !($props['filter_multiselect'] && $props['filter_style'] === 'checkbox') ? [
        'target: .js-filter;',
        'animation: {filter_animation};',
    ] : false,
    
    'class' => [
        'multiselect-filter-container' => $tags && $props['filter_multiselect'] && $props['filter_style'] === 'checkbox',
    ],
    
    'data-filter-logic' => $props['filter_logic'],
    'data-category-logic' => $props['filter_category_logic'] ?: 'or',
    'data-filter-animation' => $props['filter_multiselect_animation'] ?: 'fade',
    'data-pagination' => $props['pagination'] ? 'true' : 'false',
    'data-items-per-page' => $props['pagination'] ? (int)($props['pagination_items_per_page'] ?: 6) : 0,

]);

// Grid
$grid = $this->el('div', [

    'class' => [
        'uk-grid',
        'js-filter' => $tags,
        'uk-child-width-[1-{@!grid_default: auto}]{grid_default}',
        'uk-child-width-[1-{@!grid_small: auto}]{grid_small}@s',
        'uk-child-width-[1-{@!grid_medium: auto}]{grid_medium}@m',
        'uk-child-width-[1-{@!grid_large: auto}]{grid_large}@l',
        'uk-child-width-[1-{@!grid_xlarge: auto}]{grid_xlarge}@xl',
        'uk-flex-center {@grid_column_align} {@!grid_masonry}',
        'uk-flex-middle {@grid_row_align} {@!grid_masonry}',
        $props['grid_column_gap'] == $props['grid_row_gap'] ? 'uk-grid-{grid_column_gap}' : '[uk-grid-column-{grid_column_gap}] [uk-grid-row-{grid_row_gap}]',
        'uk-grid-divider {@grid_divider} {@!grid_column_gap: collapse} {@!grid_row_gap: collapse}' => count($children) > 1,
        'uk-grid-match {@!grid_masonry}',
    ],

    'uk-grid' => $this->expr([
        'masonry: {grid_masonry};',
        'parallax: {grid_parallax};',
        'parallax-justify: true; {@grid_parallax_justify}',
        'parallax-start: {grid_parallax_start};' => $props['grid_parallax'] || $props['grid_parallax_justify'],
        'parallax-end: {grid_parallax_end};' => $props['grid_parallax'] || $props['grid_parallax_justify'],
    ], $props) ?: count($children) > 1,

    'uk-grid-checked' => $props['panel_style'] === 'tile-checked'
        ? 'uk-tile-default,uk-tile-muted'
        : false,

    'uk-lightbox' => [
        'toggle: a[data-type];' => $props['lightbox'],
        'animation: {lightbox_animation};',
        'nav: {lightbox_nav}; slidenav: false;',
        'delay-controls: 0;' => $props['lightbox_controls'],
        'counter: true;' => $props['lightbox_counter'],
        'bg-close: false;' => !$props['lightbox_bg_close'],
        'video-autoplay: {lightbox_video_autoplay};',
    ],

]);

$cell = $this->el('div');

// Filter
$filter_grid = $this->el('div', [

    'class' => [
        'uk-grid',
        'uk-child-width-expand',
        $props['filter_grid_column_gap'] == $props['filter_grid_row_gap'] ? 'uk-grid-{filter_grid_column_gap}' : '[uk-grid-column-{filter_grid_column_gap}] [uk-grid-row-{filter_grid_row_gap}]',
    ],

    'uk-grid' => count($children) > 1,
]);

$filter_cell = $this->el('div', [

    'class' => [
        'uk-width-{filter_grid_width}@{filter_grid_breakpoint}',
        'uk-flex-last@{filter_grid_breakpoint} {@filter_position: right}',
    ],

]);

?>

<?= $el($props, $attrs) ?>

    <?php if ($tags) : ?>

        <?php if ($filter_horizontal = in_array($props['filter_position'], ['left', 'right'])) : ?>
        <?= $filter_grid($props) ?>
            <?= $filter_cell($props) ?>
        <?php endif ?>

            <?= $this->render("{$__dir}/template-nav", compact('props', 'tags', 'categorized_tags')) ?>

        <?php if ($filter_horizontal) : ?>
            </div>
            <div>
        <?php endif ?>

            <?= $grid($props) ?>
            <?php 
            // Pagination logic - Debug output
            $itemsPerPage = $props['pagination'] ? (int)($props['pagination_items_per_page'] ?: 6) : count($children);
            $totalItems = count($children);
            $totalPages = $props['pagination'] ? ceil($totalItems / $itemsPerPage) : 1;
            
            
            foreach ($children as $index => $child) : 
                $pageNumber = floor($index / $itemsPerPage) + 1;
                $isVisible = !$props['pagination'] || $pageNumber === 1;
            ?>
            <?= $cell($props, [
                'data-tag' => $child->tags,
                'data-page' => $pageNumber,
                'data-item-index' => $index,
                'style' => $isVisible ? '' : 'display: none;'
            ], $builder->render($child, ['element' => $props])) ?>
            <?php endforeach ?>
            <?= $grid->end() ?>
            
            <?php if ($props['filter_multiselect'] && $props['filter_style'] === 'checkbox') : ?>
            <div class="no-results-message" style="display: none;">
                <div class="uk-text-center uk-text-muted uk-margin-large-top">
                    <div class="uk-text-large">
                        <?= $props['filter_no_results_message'] ?: 'No products found' ?>
                    </div>
                </div>
            </div>
            <?php endif ?>
            
            <?php if ($props['pagination']) : ?>
            <div class="pagination-container uk-margin-large-top uk-text-<?= $props['pagination_align'] ?: 'center' ?>" style="<?= $totalPages <= 1 ? 'display: none;' : '' ?>">
                <?php if ($props['pagination_style'] === 'load-more') : ?>
                    <button class="uk-button uk-button-default load-more-btn" data-current-page="1" data-total-pages="<?= $totalPages ?>">
                        Load More
                    </button>
                <?php else : ?>
                    <ul class="uk-pagination" data-total-pages="<?= $totalPages ?>" data-current-page="1">
                        <li class="pagination-prev uk-disabled">
                            <a href="#"><span uk-pagination-previous></span></a>
                        </li>
                        
                        <?php if ($props['pagination_style'] === 'default') : ?>
                            <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                            <li class="pagination-page <?= $i === 1 ? 'uk-active' : '' ?>" data-page="<?= $i ?>">
                                <a href="#"><?= $i ?></a>
                            </li>
                            <?php endfor ?>
                        <?php endif ?>
                        
                        <li class="pagination-next <?= $totalPages <= 1 ? 'uk-disabled' : '' ?>">
                            <a href="#"><span uk-pagination-next></span></a>
                        </li>
                    </ul>
                    
                    <?php if ($props['pagination_style'] === 'previous-next') : ?>
                    <div class="pagination-info uk-text-meta uk-margin-small-top">
                        Page <span class="current-page">1</span> of <span class="total-pages"><?= $totalPages ?></span>
                    </div>
                    <?php endif ?>
                <?php endif ?>
            </div>
            <?php endif ?>

        <?php if ($filter_horizontal) : ?>
            </div>
        </div>
        <?php endif ?>

    <?php else : ?>

        <?= $grid($props) ?>
        <?php foreach ($children as $child) : ?>
        <div><?= $builder->render($child, ['element' => $props]) ?></div>
        <?php endforeach ?>
        <?= $grid->end() ?>

    <?php endif ?>

<?= $el->end() ?>
