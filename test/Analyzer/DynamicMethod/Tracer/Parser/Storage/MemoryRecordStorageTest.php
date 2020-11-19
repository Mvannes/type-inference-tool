<?php
/**
 * @copyright 2017-2018 Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Component\TypeInference\Analyzer\DynamicMethod\Tracer\Parser\Storage;

use Hostnet\Component\TypeInference\Analyzer\Data\Type\ScalarPhpType;
use Hostnet\Component\TypeInference\Analyzer\DynamicMethod\Tracer\Parser\Record\EntryRecord;
use Hostnet\Component\TypeInference\Analyzer\DynamicMethod\Tracer\Parser\Record\ReturnRecord;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hostnet\Component\TypeInference\Analyzer\DynamicMethod\Tracer\Parser\Storage\MemoryRecordStorage
 */
class MemoryRecordStorageTest extends TestCase
{
    /**
     * @var MemoryRecordStorage
     */
    private $storage;

    private $parameters = ['arg0'];

    /**
     * @var EntryRecord
     */
    private $entry_record;

    /**
     * @var ReturnRecord
     */
    private $return_record;

    protected function setUp(): void
    {
        $this->storage       = new MemoryRecordStorage();
        $function_number     = 123;
        $this->entry_record  = new EntryRecord($function_number, 'SomeFunction', true, 'file.php', $this->parameters);
        $this->return_record = new ReturnRecord($function_number, ScalarPhpType::TYPE_STRING);
    }

    public function testAppendEntryRecordShouldAddAnEntryRecord(): void
    {
        $this->storage->appendEntryRecord($this->entry_record);
        $this->storage->finishInsertion();
        $this->storage->loopEntryRecords(function (EntryRecord $record, array $parameters, $return_type): void {
            self::assertSame($this->entry_record, $record);
            self::assertSame($this->parameters, $parameters);
            self::assertNull($return_type);
        });
    }

    public function testAppendReturnRecordShouldAddAReturnRecord(): void
    {
        $this->storage->appendEntryRecord($this->entry_record);
        $this->storage->appendReturnRecord($this->return_record);
        $this->storage->loopEntryRecords(function (EntryRecord $record, array $parameters, $return_type): void {
            self::assertSame($this->entry_record, $record);
            self::assertSame($this->parameters, $parameters);
            self::assertSame($this->return_record->getReturnType(), $return_type);
        });
    }

    public function testWhenEntryRecordHasNoMatchingReturnRecordThenReturnTypeIsNull(): void
    {
        $this->storage->appendEntryRecord($this->entry_record);

        $this->return_record = new ReturnRecord(999, 'SomethingElse');
        $this->storage->appendReturnRecord($this->return_record);

        $this->storage->loopEntryRecords(function (EntryRecord $record, array $parameters, $return_type): void {
            self::assertNull($return_type);
        });
    }

    public function testClearRecordsShouldRemoveAllAppendedRecords(): void
    {
        $this->storage->appendEntryRecord($this->entry_record);
        $this->storage->appendReturnRecord($this->return_record);
        $this->storage->clearRecords();

        $count = 0;
        $this->storage->loopEntryRecords(function () use (&$count): void {
            $count++;
        });

        self::assertSame(0, $count);
    }
}
