<?php

$image = $this->el('image', [

    'src' => $src,
    'alt' => $props['image_alt'],
    'loading' => $element['image_loading'] ? false : null,
    'width' => $element['image_width'],
    'height' => $element['image_height'],
    'focal_point' => $focal,
    'uk-svg' => $element['image_svg_inline'],
    'thumbnail' => true,

    'class' => [
        'uk-text-{image_svg_color} {@image_svg_inline}' => $this->isImage($src) == 'svg',
    ],

]);

return $image;
