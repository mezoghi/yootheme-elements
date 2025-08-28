<?php

// Override default settings
$element['panel_style'] = $props['panel_style'] ?: $element['panel_style'];

// Resets
if ($props['icon'] && !($props['image'] || $props['video'])) { $element['panel_image_no_padding'] = ''; }
if ($element['panel_style'] || !($props['image'] || $props['video'])) {
    $element['image_box_decoration'] = '';
    $element['image_transition_border'] = '';
}
if (!($element['panel_style'] || (!$element['panel_style'] && ($props['image'] || $props['video']) && $element['image_align'] != 'between'))) {
    $element['panel_padding'] = '';
}
if (!(($props['image'] || $props['video'])
    && $element['image_align'] == 'top' && !$element['panel_style'] && !$element['panel_padding'] && !$element['item_maxwidth']
    && (!$element['grid_default'] || $element['grid_default'] == '1')
    && (!$element['grid_small'] || $element['grid_small'] == '1')
    && (!$element['grid_medium'] || $element['grid_medium'] == '1')
    && (!$element['grid_large'] || $element['grid_large'] == '1')
    && (!$element['grid_xlarge'] || $element['grid_xlarge'] == '1'))) {
    $element['panel_content_width'] = '';
}

// Override default settings
if ($element['grid_masonry'] && !(($props['image'] || $props['video']) && in_array($element['image_align'], ['left', 'right']))) {
    $element['panel_expand'] = '';
}
$element['image_text_color'] = $props['image_text_color'] ?: $element['image_text_color'];

// If link is not set use image/video field for the lightbox
if (!$props['link'] && $element['lightbox']) {
    $props['link'] = $props['video'] ?: $props['image'];
}

// New logic shortcuts
$element['flex_column_align'] = '';
$element['flex_column_align_fallback'] = '';
if ($element['panel_expand']) {
    $horizontal = ['left', 'center', 'right'];
    $vertical = ['top', 'middle', 'bottom'];
    $element['flex_column_align'] = str_replace($horizontal, $vertical, $element['text_align'] ?? '');
    $element['flex_column_align_fallback'] = str_replace($horizontal, $vertical, $element['text_align_fallback'] ?? '');

    // `uk-flex-top` prevents child inline elements from taking the whole width, needed for floating shadow + hover image
    if (!$element['panel_style']) {
        if ($element['text_align'] && $element['text_align_breakpoint'] && !$element['text_align_fallback']) {
            $element['flex_column_align_fallback'] = 'top';
        } elseif (!$element['text_align']) {
            $element['flex_column_align'] = 'top';
        }
    }
}

// Image
$props['image'] = $this->render("{$__dir}/template-media", compact('props', 'element'));

// Panel/Card/Tile
$el = $this->el($props['item_element'] ?: 'div', [

    'class' => [
        'el-item',
        'uk-margin-auto uk-width-{item_maxwidth}',
        'uk-flex uk-flex-column {@panel_link}' => $props['link'],
    ],

]);

// Link Container
$link_container = $props['link'] && $element['panel_link'] ? $this->el('a', [

    'class' => [
        'uk-flex-1',
    ],

]) : null;

($link_container ?: $el)->attr([

    'class' => [
        'uk-panel [uk-{panel_style: tile-.*}] {@panel_style: |tile-.*}',
        'uk-card uk-{panel_style: card-.*} [uk-card-{!panel_padding: |default}]',
        'uk-tile-hover {@panel_style: tile-.*} {@panel_link} {@panel_link_hover}' => $props['link'],
        'uk-card-hover {@panel_style: card-(default|primary|secondary)} {@panel_link} {@panel_link_hover}' => $props['link'],
        'uk-transition-toggle {@panel_link}' => $props['image'] && ($element['image_transition'] || $element['image_transition_border'] || $props['hover_image'] || $props['hover_video']),
        'uk-flex uk-flex-column {@panel_expand}',
        // Image not wrapped in card media container or grid
        'uk-flex-{flex_column_align}[@{text_align_breakpoint} [uk-flex-{flex_column_align_fallback}]] {@panel_expand}' => !($element['panel_style'] && str_starts_with($element['panel_style'], 'card-') && $props['image'] && $element['panel_image_no_padding'] && $element['image_align'] != 'between') && !in_array($element['image_align'], ['left', 'right']),
    ],

]);

// Padding || Content Width
$content_container = '';
if (($element['panel_padding'] && $props['image'] && (!$element['panel_style'] || $element['panel_image_no_padding']) && $element['image_align'] != 'between')
    || $element['panel_content_width']) {

    $content_container = $this->el('div', [
        'class' => [
            'uk-flex-1 uk-flex uk-flex-column {@panel_expand: content|both}',
            // 1 Column Content Width
            'uk-container uk-container-{panel_content_width}',
            // Image not wrapped in card media container or grid
            'uk-width-1-1 {@flex_column_align} {@panel_expand} {!panel_content_width}' => !($element['panel_style'] && str_starts_with($element['panel_style'], 'card-') && $props['image'] && $element['panel_image_no_padding'] && $element['image_align'] != 'between') && !in_array($element['image_align'], ['left', 'right']),
        ],
    ]);

}

($content_container ?: $link_container ?: $el)->attr('class', [
    'uk-padding[-{!panel_padding: default}] {@panel_style: |tile-.*} {@panel_padding}',
    'uk-card-body {@panel_style: card-.*} {@panel_padding}',
]);

// Image align
$grid = '';
$cell_image = '';
$cell_content = '';
if ($props['image'] && in_array($element['image_align'], ['left', 'right'])) {

    $grid = $this->el('div', [

        'class' => [
            $element['panel_style'] && $element['panel_image_no_padding']
                ? 'uk-grid-collapse'
                : ($element['image_grid_column_gap'] == $element['image_grid_row_gap']
                    ? 'uk-grid-{image_grid_column_gap}'
                    : '[uk-grid-column-{image_grid_column_gap}] [uk-grid-row-{image_grid_row_gap}]'),
            // Center vertically
            'uk-flex-middle {@image_vertical_align} {@!panel_expand}',
            // Expand
            'uk-flex-1 {@panel_expand}',
            'uk-flex-column {@panel_expand} uk-flex-row@{image_grid_breakpoint}',
        ],

        'uk-grid' => true,
    ]);

    $cell_image = $this->el('div', [

        'class' => [
            'uk-width-{image_grid_width}[@{image_grid_breakpoint}]',
            // Order
            'uk-flex-last[@{image_grid_breakpoint}] {@image_align: right}',
            // Center vertically and keep text align
            'uk-flex uk-flex-middle[@{image_grid_breakpoint}] uk-flex-{text_align} {@image_vertical_align} {@panel_expand: content}',
            'uk-flex-{text_align}[@{text_align_breakpoint} [uk-flex-{text_align_fallback}]] {@panel_expand: content}',
            // Expand, also when stacking
            'uk-flex uk-flex-column {@panel_expand: image|both} [uk-flex-1 uk-flex-initial@{image_grid_breakpoint}]',
            'uk-flex-{flex_column_align}[@{text_align_breakpoint} [uk-flex-{flex_column_align_fallback}]] {@panel_expand: image|both}',
        ],

    ]);

    $cell_content = $this->el('div', [

        'class' => [
            'uk-width-expand',
            // Center vertically
            'uk-flex uk-flex-column uk-flex-center[@{image_grid_breakpoint}] {@image_vertical_align} {@panel_expand: image}',
            // Expand, also when stacking but only for `content`
            'uk-flex uk-flex-column {@panel_expand: content|both}',
            'uk-flex-1 {@image_grid_breakpoint} {@panel_expand: content}',
            'uk-flex-1[@{image_grid_breakpoint} uk-flex-none] {@panel_expand: both}',
            'uk-flex-none uk-flex-1@{image_grid_breakpoint} {@panel_expand: image}',
        ],

    ]);

}

($content_container ?: $cell_content ?: $link_container ?: $el)->attr('class', [
    'uk-margin-remove-first-child {@!panel_content_width}',
]);

// Link
$link = include "{$__dir}/template-link.php";

// Card Media (Needs to be after link)
if ($element['panel_style'] && str_starts_with($element['panel_style'], 'card-') && $props['image'] && $element['panel_image_no_padding'] && $element['image_align'] != 'between') {

    $props['image'] = $this->el('div', [
        'class' => [
            'uk-card-media-{image_align}',
            'uk-flex-1 uk-flex uk-flex-column {@panel_expand: image|both}',
            'uk-flex-{flex_column_align}[@{text_align_breakpoint} [uk-flex-{flex_column_align_fallback}]] {@panel_expand: image|both} {@!image_align: left|right}',
        ],

        'uk-toggle' => [
            'cls: uk-card-media-{image_align} uk-card-media-top; mode: media; media: @{image_grid_breakpoint} {@image_align: left|right}',
        ]
    ], $props['image'])->render($element);

}

?>

<?= $el($element, $attrs) ?>

    <?php if ($link_container) : ?>
    <?= $link_container($element) ?>
    <?php endif ?>

        <?php if ($grid) : ?>
        <?= $grid($element) ?>
        <?php endif ?>

            <?php if ($cell_image) : ?>
            <?= $cell_image($element) ?>
            <?php endif ?>

                <?php if (in_array($element['image_align'], ['left', 'right'])) : ?>
                <?= $props['image'] ?>
                <?php endif ?>

            <?php if ($cell_image) : ?>
            <?= $cell_image->end() ?>
            <?php endif ?>

            <?php if ($cell_content) : ?>
            <?= $cell_content($element) ?>
            <?php endif ?>

                <?php if ($element['image_align'] == 'top') : ?>
                <?= $props['image'] ?>
                <?php endif ?>

                <?php if ($content_container) : ?>
                <?= $content_container($element) ?>
                <?php endif ?>

                    <?= $this->render("{$__dir}/template-content", compact('props', 'link')) ?>

                <?php if ($content_container) : ?>
                <?= $content_container->end() ?>
                <?php endif ?>

                <?php if ($element['image_align'] == 'bottom') : ?>
                <?= $props['image'] ?>
                <?php endif ?>

            <?php if ($cell_content) : ?>
            <?= $cell_content->end() ?>
            <?php endif ?>

        <?php if ($grid) : ?>
        <?= $grid->end() ?>
        <?php endif ?>

    <?php if ($link_container) : ?>
    <?= $link_container->end() ?>
    <?php endif ?>

<?= $el->end() ?>
