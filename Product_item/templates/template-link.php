<?php

namespace YOOtheme;

use YOOtheme\Http\Uri;

/** @var ImageProvider $imageProvider */
$imageProvider = app(ImageProvider::class);

$link = $props['link'] ? $this->el('a', [
    'href' => $props['link'],
    'aria-label' => $props['link_aria_label'] ?: $element['link_aria_label'],
]) : null;

// Lightbox
if ($link && $element['lightbox']) {

    if ($type = $this->isImage($props['link'])) {

        if ($type !== 'svg' && ($element['lightbox_image_width'] || $element['lightbox_image_height'])) {

            $thumbnail = [$element['lightbox_image_width'], $element['lightbox_image_height'], $element['lightbox_image_orientation']];
            if (!empty($props['lightbox_image_focal_point'])) {
                [$y, $x] = explode('-', $props['lightbox_image_focal_point']);
                $thumbnail += [3 => $x, 4 => $y];
            }

            $props['link'] = (string) (new Uri($props['link']))->withFragment('thumbnail=' . implode(',', $thumbnail));
        }

        $link->attr([
            'href' => $imageProvider->getUrl($props['link']),
            'data-alt' => $props['image_alt'],
            'data-type' => 'image',
        ]);

    } elseif ($this->isVideo($props['link'])) {
        $link->attr('data-type', 'video');
    } elseif (!$this->iframeVideo($props['link'])) {
        $link->attr('data-type', 'iframe');
    } else {
        $link->attr('data-type', true);
    }

    // Caption
    $caption = '';

    if ($props['title'] != '' && $element['title_display'] != 'item') {

        $caption .= "<h4 class='uk-margin-remove'>{$props['title']}</h4>";

        if ($element['title_display'] == 'lightbox') {
            $props['title'] = '';
        }
    }

    if ($props['content'] != '' && $element['content_display'] != 'item') {

        $caption .= $props['content'];

        if ($element['content_display'] == 'lightbox') {
            $props['content'] = '';
        }
    }

    if ($caption) {
        $link->attr('data-caption', $caption);
    }

    // Text Color
    $element['lightbox_text_color'] = $props['lightbox_text_color'] ?: $element['lightbox_text_color'];
    if ($element['lightbox_text_color']) {
        $link->attr('data-attrs', "class: uk-inverse-{$element['lightbox_text_color']}");
    }


} elseif ($link) {

    $link->attr([
        'target' => ['_blank {@link_target}'],
        'uk-scroll' => str_contains((string) $props['link'], '#'),
    ]);

}

if ($link && $element['panel_link']) {

    $link_container->attr($link->attrs + [

        'class' => [
            'uk-link-toggle',
        ],

    ]);

    $props['title'] = $this->striptags($props['title']);
    $props['meta'] = $this->striptags($props['meta']);
    $props['content'] = $this->striptags($props['content']);

    if ($props['title'] != '' && $element['title_hover_style'] != 'reset') {
        $props['title'] = $this->el('span', [
            'class' => [
                'uk-link-{title_hover_style: heading}',
                'uk-link {!title_hover_style}',
            ],
        ], $props['title'])->render($element);
    }

}

if ($link && $props['title'] != '' && $element['title_link']) {

    $props['title'] = $link($element, [], $this->striptags($props['title'])); // title_hover_style is set on title

}

if ($link && $props['image'] && $element['image_link']) {

    $props['image'] = $link($element, [
        'class' => [
            'uk-flex-1 uk-flex uk-flex-column {@panel_expand: image|both}',
        ],
    ], $props['image']);

}

if ($link && ($props['link_text'] || $element['link_text'])) {

    if ($element['panel_link']) {
        $link = $this->el('div');
    }

    $link->attr('class', [
        'el-link',
        'uk-{link_style: link-(muted|text)}',
        'uk-button uk-button-{!link_style: |link-muted|link-text} [uk-button-{link_size}] [uk-width-1-1 {@link_fullwidth}]',
        // Keep link style if panel link
        'uk-link {@link_style:} {@panel_link}',
        'uk-text-muted {@link_style: link-muted} {@panel_link}',
    ]);

}

return $link;
