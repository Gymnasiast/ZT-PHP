<?php

declare(strict_types=1);

namespace ZTPHP\Zoofile;

use Cyndaron\BinaryHandler\BinaryReader;
use ZTPHP\Terrain\Type;

use function count;
use function dechex;

final class Importer
{
    private readonly BinaryReader $reader;

    private Header $header;
    /** @var Exhibit[] */
    private array $exhibits = [];
    /** @var TerrainTile[] */
    private array $terrainTiles = [];
    /** @var MapObject[] */
    private array $objects;
    private string $scenarioFile;

    public function __construct(BinaryReader $reader)
    {
        $this->reader = $reader;
    }

    private function readZtString(): string
    {
        $length = $this->reader->readUint32();
        $string = $this->reader->readBytes($length);

        return $string;
    }

    public function read()
    {
        $this->readHeader();
        $this->readExhibits();
        $this->reader->seek(4 * 4);
        $this->readTerrain();
        $this->readObjects();
        $this->reader->seek(6 * 4);
        $this->scenarioFile = $this->readZtString();
    }

    private function readHeader(): void
    {
        $magic = $this->reader->readBytes(4);
        $version = $this->reader->readSint32();
        $language = $this->reader->readSint32();
        $campaign = $this->reader->readUint32();
        $mapSizeX = $this->reader->readUint32();
        $mapSizeY = $this->reader->readUint32();
        $unk18 = $this->reader->readUint32();
        $unk1C = $this->reader->readUint32();

        $this->header = new Header($mapSizeX, $mapSizeY);
    }

    private function readExhibits(): void
    {
        $numExhibits = $this->reader->readUint32();
        for ($i = 0; $i < $numExhibits; $i++)
        {
            $this->readExhibit();
        }
    }

    private function readExhibit(): void
    {
        $this->reader->seek(8);
        $name = $this->readZtString();
        $this->reader->seek(66);

        $tankHeight = 0;
        $tankFilled = false;
        $extension = $this->reader->readUint32();
        if ($extension === 0x10000)
        {
            [$tankHeight, $tankFilled] = $this->readTankFields();
        }
        elseif ($extension === 0x1010000)
        {
            throw new \Exception('Cannot import zoos with show tanks yet!');
        }

        $this->exhibits[] = new Exhibit($name, $tankHeight, $tankFilled);
    }

    private function readTankFields(): array
    {
        $tankHeight = $this->reader->readSint32();
        $unk8 = $this->reader->readSint32();
        $tankFilled = (bool)$this->reader->readUint8();
        $unk8 = $this->reader->readSint32();
        $unk9 = $this->reader->readSint32();
        $unk10 = $this->reader->readSint32();

        return [$tankHeight, $tankFilled];
    }

    private function readTerrain(): void
    {
        for ($y = 0; $y < $this->header->mapSizeY; $y++)
        {
            for ($x = 0; $x < $this->header->mapSizeX; $x++)
            {
                $height = $this->reader->readSint32();
                $shape = $this->reader->readUint8();
                $terrainType = $this->reader->readUint8();
                $unk = $this->reader->readSint32();

                $this->terrainTiles[] = new TerrainTile($height, $shape, Type::from($terrainType), $unk);
            }
        }
    }

    private function readObjects(): void
    {
        $numObjects = $this->reader->readUint32();
        for ($i = 0; $i < $numObjects; $i++)
        {
            $this->readObject();
        }
    }

    private function readObject(): void
    {
        $id0 = $this->readZtString();
        $id1 = $this->readZtString();
        $id2 = $this->readZtString();
        $restLength = $this->reader->readUint32();
        $this->reader->seek($restLength);

        $this->objects[] = new MapObject($id0, $id1, $id2);
    }

    public function print(): void
    {
        $printLn = static function ($line) {
            echo "$line\n";
        };

        $numExhibits = count($this->exhibits);
        $endPosition = dechex($this->reader->getPosition());
        $printLn("Map size: {$this->header->mapSizeX} × {$this->header->mapSizeY}");
        $printLn("Number of exhibits: {$numExhibits}");
        $printLn("");

        for ($i = 0; $i < $numExhibits; $i++)
        {
            $tankFilled = $this->exhibits[$i]->tankFilled ? 'Y' : 'N';
            $printLn("Exhibit {$i}:");
            $printLn("  Name: {$this->exhibits[$i]->name}");
            $printLn("  Tank height: {$this->exhibits[$i]->tankHeight}");
            $printLn("  Tank filled: {$tankFilled}");
        }

        //$this->printObjects();

        $printLn("");
        $printLn("Scenario file: {$this->scenarioFile}");
        $printLn("");
        $printLn("End position: 0x{$endPosition}");
    }

    private function printObjects()
    {
        $printLn = static function ($line) {
            echo "$line\n";
        };

        $numObjects = count($this->objects);
        for ($i = 0; $i < $numObjects; $i++)
        {
            $object = &$this->objects[$i];
            $printLn("Object {$i}: {$object->id0}/{$object->id1}/{$object->id2}");
        }
    }
}
