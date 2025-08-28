<?php

$icon = $this->el('span', [

    'class' => [
        'uk-text-{icon_color}',
    ],

    'uk-icon' => [
        'icon: {0};' => $props['icon'],
        'width: {icon_width};',
        'height: {icon_width};',
    ],

]);

return $icon;
