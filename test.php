<?php
require 'vendor/autoload.php';

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;

$qrText = 'Hello World';
$result = Builder::create()
    ->writer(new PngWriter())
    ->data($qrText)
    ->encoding(new Encoding('UTF-8'))
    ->size(300)
    ->margin(10)
    ->build();

$result->saveToFile(__DIR__ . '/qr_test.png');

echo "QR generated successfully!";
