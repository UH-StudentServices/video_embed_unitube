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
      '#url' => sprintf('https://unitube.it.helsinki.fi/unitube/embed.html?id=%s&play=false', $this->getVideoId()),
      '#attributes' => [
        'width' => $width,
        'height' => $height,
        'scrolling' => 'no',
        'marginheight' => '0px',
        'marginwidth' => '0px',
        'frameborder' => '0',
        'allowfullscreen' => 'true',
      ],
    ];
  }

  /**
   * Gets videos metadata.
   * @return array|mixed
   */
  protected function getMetadata() {

    // Perform an request to metadata and ensure we get valid response code
    $response = $this->httpClient->request('GET', 'https://webcast.helsinki.fi/search/episode.json?id=' . $this->getVideoId());
    if ($response->getStatusCode() != 200) {
      return array();
    }

    // Parse JSON and get attachments
    $parsed_response = drupal_json_decode((string) $response->getBody());
    if (json_last_error()) {
      return array();
    }

    return $parsed_response;
  }

  /**
   * {@inheritdoc}
   */
  public function getRemoteThumbnailUrl() {
    $metadata = $this->getMetadata();
    if (empty($metadata['result']['mediapackage']['attachments']['attachment'])) {
      return '';
    }

    // We are looking types in certain order
    $types = array(
      'presentation/player+preview',
      'presenter/player+preview',
      'presentation/search+preview',
      'presenter/search+preview',
      'presentation/feed+preview',
      'presenter/feed+preview',
    );
    foreach ($types as $type) {
      foreach ($metadata['result']['mediapackage']['attachments']['attachment'] as $attachment) {
        if (isset($attachment['mimetype']) && isset($attachment['type']) && isset($attachment['url'])) {
          if ($attachment['type'] == $type && $attachment['mimetype'] == 'image/jpeg') {
            return $attachment['url'];
          }
        }
      }
    }
    return '';
  }

  /**
   * {@inheritdoc}
   */
  public static function getIdFromInput($input) {
    preg_match('/^https?:\/\/((www2?)?\.helsinki\.fi\/(en|fi|sv)|hy\.fi)\/unitube\/video\/(?<id>[a-zA-Z0-9\-]*)\/?/', $input, $matches);
    return isset($matches['id']) ? $matches['id'] : FALSE;
  }

}
