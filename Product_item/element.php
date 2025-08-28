<?php

namespace YOOtheme;

return [
    'transforms' => [
        'render' => function ($node, $params) {
            // Display
            foreach (['title', 'meta', 'content', 'link', 'image', 'video', 'hover_image', 'hover_video'] as $key) {
                if (!$params['parent']->props["show_{$key}"]) {
                    $node->props[$key] = '';
                    if ($key === 'image') {
                        $node->props['icon'] = '';
                    }
                }
            }

            /**
             * Auto-correct media rendering for dynamic content
             *
             * @var View $view
             */
            $view = app(View::class);

            foreach (['', 'hover_'] as $prefix) {
                if ($node->props["{$prefix}image"] && $view->isVideo($node->props["{$prefix}image"])) {
                    $node->props["{$prefix}video"] = $node->props["{$prefix}image"];
                    $node->props["{$prefix}image"] = null;
                } elseif ($node->props["{$prefix}video"] && $view->isImage($node->props["{$prefix}video"])) {
                    $node->props["{$prefix}image"] = $node->props["{$prefix}video"];
                    $node->props["{$prefix}video"] = null;
                }
            }

            // Don't render element if content fields are empty
            return $node->props['title'] != '' ||
                $node->props['meta'] != '' ||
                $node->props['content'] != '' ||
                $node->props['image'] ||
                $node->props['video'] ||
                $node->props['icon'];
        },
    ],
];
