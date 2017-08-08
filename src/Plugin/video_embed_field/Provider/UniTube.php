<?php

/**
 * @file
 * Contains \Drupal\video_embed_unitube\Plugin\video_embed_field\Provider\UniTube.
 */

namespace Drupal\video_embed_unitube\Plugin\video_embed_field\Provider;

use Drupal\video_embed_field\ProviderPluginBase;

/**
 * @VideoEmbedProvider(
 *   id = "unitube",
 *   title = @Translation("UniTube")
 * )
 */
class UniTube extends ProviderPluginBase {

  /**
   * {@inheritdoc}
   */
  public function renderEmbedCode($width, $height, $autoplay) {
    return [
      '#prefix' => '<div>',
      '#suffix' => '</div>',
      '#type' => 'video_embed_iframe',
      '#provider' => 'unitube',
      '#url' => sprintf('https://webcast.helsinki.fi/unitube/embed.html?id=%s&play=false', $this->getVideoId()),
      '#attributes' => [
        'width' => $width,
        'height' => $height,
        'scrolling' => 'no',
        'marginheight' => '0px',
        'marginwidth' => '0px',
        'frameborder' => '0',
        'allowfullscreen' => 'allowfullscreen',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getRemoteThumbnailUrl() {
    // UniTube does not support remote thumbnails
    return '';
  }

  /**
   * {@inheritdoc}
   */
  public function downloadThumbnail() {
    // UniTube does not support remote thumbnails
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getLocalThumbnailUri() {
    return drupal_get_path('module', 'video_embed_unitube') . '/unitube_generic_thumbnail.jpg';
  }

  /**
   * {@inheritdoc}
   */
  public static function getIdFromInput($input) {
    preg_match('/^https?:\/\/((www)?\.helsinki\.fi\/(en|fi|sv)|hy\.fi)\/unitube\/video\/(?<id>[a-zA-Z0-9\-]*)\/?/', $input, $matches);
    return isset($matches['id']) ? $matches['id'] : FALSE;
  }

}
