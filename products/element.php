<?php

namespace YOOtheme;

return [
    'transforms' => [
        'render' => function ($node) {
            $node->tags = [];

            // Filter tags
            if (!empty($node->props['filter'])) {
                foreach ($node->children as $child) {
                    $child->tags = [];
                    $child->categorized_tags = [];

                    // Process general tags
                    foreach (explode(',', $child->props['tags'] ?? '') as $tag) {
                        // Strip tags as precaution if tags are mapped dynamically
                        $tag = strip_tags($tag);

                        if ($key = str_replace(' ', '-', trim($tag))) {
                            $child->tags[$key] = trim($tag);
                        }
                    }

                    // Process tag1 and tag2 with custom labels
                    $tagCategories = [
                        'tag1' => $child->props['tag1_label'] ?: 'Tag 1',
                        'tag2' => $child->props['tag2_label'] ?: 'Tag 2'
                    ];

                    foreach ($tagCategories as $tagField => $categoryTitle) {
                        if (!empty($child->props[$tagField])) {
                            $categoryKey = $tagField;
                            $child->categorized_tags[$categoryKey] = [
                                'title' => $categoryTitle,
                                'tags' => []
                            ];

                            foreach (explode(',', $child->props[$tagField]) as $tag) {
                                $tag = strip_tags($tag);
                                if ($tagKey = str_replace(' ', '-', trim($tag))) {
                                    $child->tags[$categoryKey . '-' . $tagKey] = trim($tag);
                                    $child->categorized_tags[$categoryKey]['tags'][$tagKey] = trim($tag);
                                }
                            }
                        }
                    }

                    $node->tags += $child->tags;
                }

                // Build categorized structure for the parent node
                $node->categorized_tags = [];
                foreach ($node->children as $child) {
                    if (!empty($child->categorized_tags)) {
                        foreach ($child->categorized_tags as $categoryKey => $categoryData) {
                            if (!isset($node->categorized_tags[$categoryKey])) {
                                $node->categorized_tags[$categoryKey] = [
                                    'title' => $categoryData['title'],
                                    'tags' => []
                                ];
                            }
                            $node->categorized_tags[$categoryKey]['tags'] += $categoryData['tags'];
                        }
                    }
                }

                if (
                    $node->props['filter_order'] === 'manual' &&
                    $node->props['filter_order_manual']
                ) {
                    $order = array_map(
                        'strtolower',
                        array_map('trim', explode(',', $node->props['filter_order_manual'])),
                    );
                    uasort($node->tags, function ($a, $b) use ($order) {
                        $iA = array_search(strtolower($a), $order);
                        $iB = array_search(strtolower($b), $order);
                        return $iA !== false && $iB !== false
                            ? $iA - $iB
                            : ($iA !== false
                                ? -1
                                : ($iB !== false
                                    ? 1
                                    : strnatcmp($a, $b)));
                    });
                } else {
                    natsort($node->tags);
                }

                if ($node->props['filter_reverse']) {
                    $node->tags = array_reverse($node->tags, true);
                }
            }

            if ($node->props['panel_style'] === 'tile-checked') {
                app(Metadata::class)->set('script:builder-grid', [
                    'src' => Path::get('./app/grid.min.js', __DIR__),
                    'defer' => true,
                ]);
            }

            // Load multi-select filter JavaScript and CSS when needed
            if (!empty($node->props['filter']) && 
                !empty($node->props['filter_multiselect']) && 
                $node->props['filter_style'] === 'checkbox') {
                app(Metadata::class)->set('script:multiselect-filter', [
                    'src' => Path::get('./app/multiselect-filter.js', __DIR__),
                    'defer' => true,
                ]);
                app(Metadata::class)->set('style:multiselect-filter', [
                    'src' => Path::get('./app/multiselect-filter.css', __DIR__),
                ]);
            }

            // Load pagination JavaScript when needed
            if (!empty($node->props['pagination'])) {
                app(Metadata::class)->set('script:pagination', [
                    'src' => Path::get('./app/pagination.js', __DIR__),
                    'defer' => true,
                ]);
            }
        },
    ],
];
