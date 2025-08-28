<?php

if ($iframe = $this->iframeVideo($src)) {

    $video = $this->el('iframe', [

        'src' => $iframe,
        'uk-responsive' => true,
        'loading' => ['lazy {@image_loading}'],

        'class' => [
            'uk-disabled',
        ],

        'uk-video' => [
            'automute: true;',
        ],

    ]);

} else {

    $video = $this->el('video', [

        'src' => $src,
        'controls' => false,
        'loop' => true,
        'autoplay' => true,
        'muted' => true,
        'playsinline' => true,
        'preload' => ['none {@image_loading}'],

        'uk-video' => true,

    ]);

}

$video->attr([

    'width' => $element['image_width'],
    'height' => $element['image_height'],

]);

return $video;
