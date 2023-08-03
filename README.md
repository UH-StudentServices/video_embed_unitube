# Video Embed UniTube
This Drupal 9/10 module allows you to use UniTube video URLs while embedding your videos
with [Video Embed Field](https://www.drupal.org/project/video_embed_field)
module.

Read more about UniTube service itself from their [Wiki page](https://wiki.helsinki.fi/display/unitube/UniTube+Service).

## How to Use?
* Configure your Video Embed Field
* Paste UniTube URLs from your browser

Both URL formats are supported: `http://hy.fi/unitube/video/{UUID}` and
`https://www.helsinki.fi/{LANG}/unitube/video/{UUID}`.

## Installation
Download this release and place it to your modules directory.

OR via composer:
```
$ composer config repositories.video_embed_unitube vcs https://github.com/UH-StudentServices/video_embed_unitube
$ composer require UH-StudentServices/video_embed_unitube:^VERSION
```

## Questions
Please post your question to doo-projekti@helsinki.fi

## License
This software is developed under [GPL v3](LICENSE.txt).
