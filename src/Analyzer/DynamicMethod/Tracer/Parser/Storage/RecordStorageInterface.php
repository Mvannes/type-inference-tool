<?php
/**
 * @copyright 2017-2018 Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Component\TypeInference\Analyzer\DynamicMethod\Tracer\Parser\Storage;

use Hostnet\Component\TypeInference\Analyzer\DynamicMethod\Tracer\Parser\Record\EntryRecord;
use Hostnet\Component\TypeInference\Analyzer\DynamicMethod\Tracer\Parser\Record\ReturnRecord;

/**
 * Used by trace parser to store parsed trace data.
 */
interface RecordStorageInterface
{
    /**
     * Appends an entry record to a collection of entry records.
     *
     * @param EntryRecord $entry_record
     */
    public function appendEntryRecord(EntryRecord $entry_record): void;

    /**
     * Appends an entry record to a collection of entry records.
     *
     * @param ReturnRecord $return_record
     */
    public function appendReturnRecord(ReturnRecord $return_record): void;

    /**
     * Mark insertions as finished.
     */
    public function finishInsertion(): void;

    /**
     * Loops all trace records and executes the given callback for each found
     * record.
     *
     * @param callable $callback
     */
    public function loopEntryRecords(callable $callback): void;

    /**
     * Deletes all records
     */
    public function clearRecords(): void;
}
