# ImageResizer

<img src="https://travis-ci.org/teodorpopa/ImageResizer.svg?branch=master" /> <a href="https://codeclimate.com/github/teodorpopa/ImageResizer"><img src="https://codeclimate.com/github/teodorpopa/ImageResizer/badges/gpa.svg" /></a> <a href='https://coveralls.io/github/teodorpopa/ImageResizer?branch=master'><img src='https://coveralls.io/repos/teodorpopa/ImageResizer/badge.svg?branch=master&service=github' alt='Coverage Status' /></a> <img src="https://scrutinizer-ci.com/g/teodorpopa/ImageResizer/badges/quality-score.png?b=master" />


##### Example

```
composer require teodorpopa/image-resizer
```

```
$image = new ImageResizer::load('image.jpg')->resize(200, 200, [
  'resizeType'      => ImageResizer::RESIZE_AUTO
  'background'      => '#ffffff',
  'resizePositionX'  => ImageResizer::RESIZE_POSITION_CENTER,
  'resizePositionY'  => ImageResizer::RESIZE_POSITION_MIDDLE
]);
```
