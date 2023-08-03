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
    $name = $this->getName();
    if (!$name) {
      return [];
    }
    return [
      '#prefix' => '<div>',
      '#suffix' => '</div>',
      '#type' => 'video_embed_iframe',
      '#provider' => 'unitube',
      '#url' => sprintf('https://unitube.it.helsinki.fi/unitube/embed.html?id=%s&play=false', $this->getVideoId()),
      '#attributes' => [
        'title' => $name,
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
    try {
      // Perform an request to metadata and ensure we get valid response code
      // $response = $this->httpClient->request('GET', 'https://webcast.helsinki.fi/search/episode.json?id=' . $this->getVideoId());
      $response = $this->httpClient->request('https://httpstat.us/503');
      // $response = $this->httpClient->request('GET', 'https://httpstat.us/503');
      if ($response->getStatusCode() != 200) {
        return [];
      }

      // Parse JSON and get attachments
      $parsed_response = json_decode((string) $response->getBody(), TRUE);
      if (json_last_error()) {
        return [];
      }

      return $parsed_response;
    }
    catch (\Exception $e) {
      \Drupal::logger('video_embed_unitube')->error('There was an error downloading metadata. Message: @message.', ['@message' => $e->getMessage()]);
    }
    return NULL;
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
    $types = [
      'presentation/player+preview',
      'presenter/player+preview',
      'presentation/search+preview',
      'presenter/search+preview',
      'presentation/feed+preview',
      'presenter/feed+preview',
    ];
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

  /**
   * {@inheritdoc}
   */
  public function getName() {
    $metadata = $this->getMetadata();
    if (!$metadata) {
      // In case of maintenance break.
      return NULL;
    }
    if (isset($metadata['search-results']['result']['dcTitle'])) {
      $title = $metadata['search-results']['result']['dcTitle'];
      return $this->t('@provider Video (@id)', ['@provider' => $this->getPluginDefinition()['title'], '@id' => $title]);
    }
    return $this->t('UniTube Video');
  }

}
