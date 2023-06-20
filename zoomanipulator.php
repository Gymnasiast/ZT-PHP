<?php

use Cyndaron\BinaryHandler\BinaryWriter;
use RCTPHP\Util;

require __DIR__ . '/vendor/autoload.php';

const TERRAIN_TYPE_MAP = [
    'Grass',
    'Savannah grass',
    'Sand',
    'Dirt',
    'Rainforest floor',
    'Brown stone',
    'Gray stone',
    'Gravel',
    'Snow',
    'Fresh water',
    'Salt water',
    'Decideous floor',
    'Waterfall',
    'Coniferous floor',
    'Concrete',
    'Asphalt',
    'Trampled terrain',
    'Gunite',
];

const RCT_HEIGHT_OFFSET = 12;


if ($argc < 2)
{
    echo "Usage: zooreader.php <inputfile> <terrain offset>\n";
    exit(1);
}

function readZtString(\Cyndaron\BinaryHandler\BinaryReader $reader): string
{
    $length = $reader->readUint32();
    $string = $reader->readBytes($length);

    return $string;
}

$inputFilename = $argv[1];
//$terrainOffset = hexdec($argv[2]);

$reader = \Cyndaron\BinaryHandler\BinaryReader::fromFile($inputFilename);
$magic = $reader->readBytes(4);
$version = $reader->readSint32();
$language = $reader->readSint32();
$campaign = $reader->readUint32();
$mapSizeX = $reader->readUint32();
$mapSizeY = $reader->readUint32();
$unk18 = $reader->readUint32();
$unk1C = $reader->readUint32();
$numExhibits = $reader->readUint32();

echo "Reading map with size {$mapSizeX} × {$mapSizeY}, with {$numExhibits} exhibits.\n\n";



for ($i = 0; $i < $numExhibits; $i++)
{
    $reader->seek(8);
    $name = readZtString($reader);
    $reader->seek(66);
    $extension = $reader->readUint32();
    if ($extension === 0x10000)
    {
        $reader->seek(21);
    }
    else if ($extension === 0x1010000)
    {
        throw new \Exception('Cannot import zoos with show tanks yet!');
    }

    \RCTPHP\Util::printLn("Exhibit {$i}: {$name}");
}

$reader->seek(4 * 4);

//for ($y = 0; $y < $mapSizeY; $y++)
//{
//    for ($x = 0; $x < $mapSizeX; $x++)
//    {
//        $height = $reader->readSint32();
//        $shape = $reader->readUint8();
//        $terrainType = $reader->readUint8();
//        $unk = $reader->readSint32();
//    }
//}

//$im = imagecreate($mapSizeX, $mapSizeY);
//for ($i = 0; $i < 255; $i++)
//{
//    imagecolorallocate($im, $i, $i, $i);
//}

Util::printLn($reader->getPosition());

$newsize = 233;
$newsizeX = $newsize;
$newsizeY = $newsize;
$outputFile = 'enlarged6.zoo';

function fillTile(BinaryWriter $writer): int
{
    $writer->writeSint32(0);
    $writer->writeUint8(0);
    $writer->writeUint8(0);
    $writer->writeSint32(0);

    return 10;
}

$secondReader = \Cyndaron\BinaryHandler\BinaryReader::fromFile($inputFilename);
$writer = BinaryWriter::fromFile($outputFile);
$writer->writeBytes($secondReader->readBytes($reader->getPosition()));

//die();
//$reader->moveTo($terrainOffset);
for ($y = 0; $y < $mapSizeY; $y++)
{
    for ($x = 0; $x < $mapSizeX; $x++)
    {
        $height = $reader->readSint32();
        $shape = $reader->readUint8();
        $type = $reader->readUint8();
        $unk = $reader->readSint32();

        $name = TERRAIN_TYPE_MAP[$type];
        $shapeF = dechex($shape);
        echo "<{$x}, {$y}>: {$name}, height {$height}, shape {$shape} / 0x{$shapeF}, unk {$unk}\n";
//        $pixelColor = $height + RCT_HEIGHT_OFFSET;
//        imagesetpixel($im, $x, $y, $pixelColor);
    }

    $writer->writeBytes($secondReader->readBytes($reader->getPosition() - $secondReader->getPosition()));

    for ($x = $mapSizeX; $x < $newsizeX; $x++)
    {
        fillTile($writer);
    }
}
$writer->writeBytes($secondReader->readBytes($reader->getPosition() - $secondReader->getPosition()));
for ($y = $mapSizeY; $y < $newsizeY; $y++)
{
    for ($x = 0; $x < $newsizeX; $x++)
    {
        fillTile($writer);
    }

}


//$residualSize = ($newsize * $newsize) - ($mapSizeX * $mapSizeY);
//for ($i = 0; $i < $residualSize; $i++)
//{
//    fillTile($writer);
//    $writer->writeSint32(0);
//    $writer->writeUint8(0);
//    $writer->writeUint8(0);
//    $writer->writeSint32(0);
//}

$totalSize = $secondReader->getSize();
$writer->writeBytes($secondReader->readBytes($totalSize - $secondReader->getPosition()));

$writer->moveTo(0x10);
$writer->writeUint32($newsizeX);
$writer->writeUint32($newsizeY);

//imagepng($im, 'zoo-heightmap.png');
