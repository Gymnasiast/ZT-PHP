#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

if ($argc < 2)
{
    echo "Usage: zooreader.php <inputfile>\n";
    exit(1);
}

$inputFilename = $argv[1];
$reader = \Cyndaron\BinaryHandler\BinaryReader::fromFile($inputFilename);

$importer = new \ZTPHP\Zoofile\Importer($reader);
$importer->read();
$importer->print();
