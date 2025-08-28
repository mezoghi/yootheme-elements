<?php

namespace YOOtheme;

return [
    '2.5.0-beta.1.3' => function ($node) {
        if (!empty($node->props['tags'])) {
            $node->props['tags'] = ucwords($node->props['tags']);
        }
    },
];
