<?php
/**
 * This file is part of PHP-FFmpeg-Extensions library.
 *
 * (c) Alexander Sharapov <alexander@sharapov.biz>
 * http://sharapov.biz/
 *
 */

date_default_timezone_set('UTC');
require_once dirname(__FILE__) . '/../vendor/autoload.php';

// Init FFMpeg library
$ffmpeg = \FFMpeg\FFMpeg::create(array(
    'ffmpeg.binaries'  => '/usr/local/bin/ffmpeg', // Path to FFMpeg
    'ffprobe.binaries' => '/usr/local/bin/ffprobe', // Path to FFProbe
    'timeout'          => 3600, // The timeout for the underlying process
    'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
));
$video = $ffmpeg->open(dirname(__FILE__).'/source/demo_video_720p_HD.mp4');

// Create draw overlay filter
$drawText = new Sharapov\FFMpegExtensions\Filters\Video\OverlayFilter();

// Create text overlay
$overlayText = new Sharapov\FFMpegExtensions\Filters\Video\Overlay\Text();
$overlayText
    ->setFontFile(dirname(__FILE__).'/source/calibri.ttf') // Set path to font file
    ->setFontColor('#ffffff') // Set font color
    ->setFontSize(33) // Set font size
    ->setOverlayText('This is the default text with bounding box') // Set overlay text
    ->setCoordinates(new \Sharapov\FFMpegExtensions\Coordinate\Point(400, 550)) // Set coordinates
    ->setTimeLine(new \Sharapov\FFMpegExtensions\Coordinate\TimeLine(1, 15)) // Set timings (start, stop) in seconds
    ->setBoundingBox(new \Sharapov\FFMpegExtensions\Filters\Video\Overlay\Box()); // Apply bounding box

// Pass text overlay to filter
$drawText
    ->setOverlay($overlayText);

// Apply overlay filter to video
$video
    ->addFilter($drawText);

// Choose output format
$format = new \FFMpeg\Format\Video\X264();
$format->on('progress', function ($video, $format, $percentage) {
  echo "$percentage %<br />";
});

// And render it
$video
    ->save($format, dirname(__FILE__).'/output/export-sample-text-overlay-bounding-box.mp4');