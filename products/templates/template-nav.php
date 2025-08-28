<?php

// => gallery
$nav = $this->el('ul', [

    'class' => [
        'el-nav',
        'uk-{filter_style} {@filter_style: tab}',
        'uk-margin[-{filter_margin}] {@filter_position: top}',
    ],

    'uk-scrollspy-class' => in_array($props['animation'], ['none', 'parallax']) || !$props['item_animation'] ? false : (!empty($props['animation'])
                            ? ['uk-animation-{0}' => $props['animation']]
                            : true),

]);

$nav_horizontal = [
    'uk-subnav {@filter_style: subnav.*}',
    'uk-{filter_style}  {@filter_style: subnav-.*}',
    'uk-flex-{filter_align: right|center}',
    'uk-child-width-expand {@filter_align: justify}',
];

$nav_vertical = [
    'uk-nav uk-nav-{0} [uk-text-left {@text_align}] {@filter_style: subnav.*}' => $props['filter_style_primary'] ? 'primary' : 'default',
    'uk-tab-{filter_position} {@filter_style: tab}',
];

$nav_attrs = $props['filter_position'] === 'top'
    ? [
        'class' => $nav_horizontal,
        'uk-margin' => (bool) preg_match('/^subnav/', $props['filter_style']),
        ]
    : [
        'class' => $nav_vertical,
        'uk-toggle' => [
            "cls: {$this->expr(array_merge($nav_vertical, $nav_horizontal), $props)};",
            'mode: media;',
            'media: @{filter_grid_breakpoint};',
        ],
    ];

?>

<?php if ($props['filter_multiselect'] && $props['filter_style'] === 'checkbox') : ?>
    <!-- Multi-select Checkbox Filters -->
    <div class="uk-margin">
        <?php if ($props['filter_all']) : ?>
        <div class="uk-margin-small">
            <label class="uk-text-bold">
                <input class="uk-checkbox uk-margin-small-right filter-all-checkbox" type="checkbox" checked>
                <?= $this->trans($props['filter_all_label'] ?: 'All') ?>
            </label>
        </div>
        <hr class="uk-margin-small">
        <?php endif ?>
        
        <?php if (!empty($categorized_tags)) : ?>
            <!-- Categorized Filters -->
            <?php foreach ($categorized_tags as $categoryKey => $categoryData) : ?>
            <div class="uk-margin-medium filter-category" data-category="<?= $categoryKey ?>">
                <h5 class="uk-text-bold uk-margin-small-bottom"><?= $categoryData['title'] ?></h5>
                <?php foreach ($categoryData['tags'] as $tagKey => $tagName) : ?>
                <div class="uk-margin-small">
                    <label>
                        <input class="uk-checkbox uk-margin-small-right filter-tag-checkbox" 
                               type="checkbox" 
                               data-tag="<?= str_replace('"', '\"', $categoryKey . '-' . $tagKey) ?>"
                               data-category="<?= $categoryKey ?>"
                               data-filter-logic="<?= $props['filter_logic'] ?: 'or' ?>">
                        <?= $tagName ?>
                    </label>
                </div>
                <?php endforeach ?>
            </div>
            <?php endforeach ?>
        <?php endif ?>
        
        <?php if (!empty($tags)) : ?>
            <!-- General Tags -->
            <?php 
            $generalTags = array_filter($tags, function($key) use ($categorized_tags) {
                foreach ($categorized_tags as $categoryKey => $categoryData) {
                    if (strpos($key, $categoryKey . '-') === 0) {
                        return false;
                    }
                }
                return true;
            }, ARRAY_FILTER_USE_KEY);
            ?>
            
            <?php if (!empty($generalTags)) : ?>
            <div class="uk-margin-medium filter-category" data-category="general">
                <h5 class="uk-text-bold uk-margin-small-bottom">General</h5>
                <?php foreach ($generalTags as $tag => $name) : ?>
                <div class="uk-margin-small">
                    <label>
                        <input class="uk-checkbox uk-margin-small-right filter-tag-checkbox" 
                               type="checkbox" 
                               data-tag="<?= str_replace('"', '\"', $tag) ?>"
                               data-category="general"
                               data-filter-logic="<?= $props['filter_logic'] ?: 'or' ?>">
                        <?= $name ?>
                    </label>
                </div>
                <?php endforeach ?>
            </div>
            <?php endif ?>
        <?php endif ?>
    </div>
<?php else : ?>
    <!-- Standard Single-select Filters -->
    <?= $nav($props, $nav_attrs) ?>

        <?php if ($props['filter_all']) : ?>
        <li class="uk-active" uk-filter-control><a href><?= $this->trans($props['filter_all_label'] ?: 'All') ?></a></li>
        <?php endif ?>

        <?php foreach ($tags as $tag => $name) : ?>
        <li <?= $this->attrs([
            'class' => ['uk-active' => $tag === array_key_first($tags) && !$props['filter_all']],
            'uk-filter-control' => json_encode(['filter' => '[data-tag~="' . str_replace('"', '\"', $tag) . '"]']),
        ]) ?>>
            <a href><?= $name ?></a>
        </li>
        <?php endforeach ?>

    <?= $nav->end() ?>
<?php endif ?>
