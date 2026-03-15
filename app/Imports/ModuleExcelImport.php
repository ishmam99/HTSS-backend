<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\{
    ToCollection,
    WithHeadingRow,
    SkipsEmptyRows,
    WithChunkReading,
    WithBatchInserts
};
use Modules\CRM\Models\{
    Module,
    ModuleField,
    Record,
    RecordRelation,
    RecordValue
};
   use \Illuminate\Bus\Queueable;
    use \Illuminate\Queue\InteractsWithQueue;
    use \Illuminate\Queue\SerializesModels;
    use \Maatwebsite\Excel\Concerns\Importable; // Also add this if missing
class ModuleExcelImport implements
    ToCollection,
    WithHeadingRow,
    SkipsEmptyRows,
    WithChunkReading,
    WithBatchInserts,
    ShouldQueue
{
    protected Module $module;
    protected int $userId;
    protected Collection $fields;

    protected bool $strictParent = true;

    protected array $relationMap = [
        5 => [ // Deal
            'parent_module_id' => 2,
            'excel_column'     => 'account_nameid',
            'relation_type'    => 'Accounts-Deals',
        ],
        3 => [ // Contact
            'parent_module_id' => 2,
            'excel_column'     => 'account_nameid',
            'relation_type'    => 'Accounts-Contacts',
        ],
        9 => [ // Proposal
            'parent_module_id' => 5,
            'excel_column'     => 'deal_id',
            'relation_type'    => 'Deals-Proposals',
        ],
    ];

    public function __construct(string $moduleId, int $userId, bool $strictParent = true)
    {
        $module = Module::findOrFail($moduleId);
        $this->module = $module;
        $this->userId = $userId;
        $this->strictParent = $strictParent;
        $modID = $module->id == 2 ? 1 : $module->id ;
        $this->fields = ModuleField::where('module_id',  $modID )
            ->get()
             ->keyBy(fn ($f) => $this->normalizeKey($f->label));
            // dd($this->fields);
            // \Log::info(  $this->fields);
    }

    public function collection(Collection $rows)
    {
        // dd($rows);
        foreach ($rows as $row) {
            try {
                // dd($row);
                $this->importRow($row);
            } catch (\Throwable $e) {
                $this->logError($row, $e->getMessage());
            }
        }
    }

  protected function importRow($row)
{

    // Convert row to array (if it's a Collection)
    if ($row instanceof \Illuminate\Support\Collection) {
        $row = $row->toArray();
    }

    $accountRequiredModules = [3, 5, 9];

    if (in_array($this->module->id, $accountRequiredModules) && !array_key_exists('account_nameid', $row)) {
        throw new \Exception("Required column 'account_nameid' is missing in the Excel file.");
    }

    if (empty($row['record_id'])) {
        $this->logError($row, 'Missing record_id');
        return;
    }

        // if (($this->module->id == 3 || $this->module->id  == 5 || $this->module->id  == 9 )&&empty($row['account_nameid'])) {
        //     $this->logError($row, 'Missing account_nameid');
        //     return;
        // }
        // dd($row);
        /** 🔎 Duplicate Detection */
        $record = $this->findDuplicate($row);
        // dd($record);

        if (!$record) {
            $record = Record::updateOrCreate(
                [
                    'module_id'   => $this->module->id,
                    'external_id' => $row['record_id'],
                ],
                [
                    'created_by' => $this->userId,
                ]
            );

        }

        /** 🧾 Record Values */

        $this->syncRelation($record, $row);
        // \Log::info(json_encode($row));
        // \Log::info(json_encode($this->fields));
        // dd($record);
        foreach ($row as $header => $value) {

        // \Log::info("header value: {$header} = " . json_encode($value));

         if ($header === 'record_id' || $header === 'account_nameid' || $header === 'deal_id') {
                    $key = $header; // do NOT normalize
                } else {
                    $key = $this->normalizeKey($header); // normalize human-friendly headers
                }

                if ($key === 'record_id' || !isset($this->fields[$key])) {
                    continue;
                }
            // $key = strtolower(trim($header));

            // if ($key === 'record_id' || !isset($this->fields[$key])) {

            //     continue;
            // }
            // dd($value,$header);
               $value = $this->castValue($value, $this->fields[$key]->type);

                if ($value !== null && $value !== '') {
                    // dd($value);
                  $rec =  RecordValue::updateOrCreate(
                        [
                            'record_id' => $record->id,
                            'field_id'  => $this->fields[$key]->id,
                        ],
                        [
                            'value' => $value,
                        ]
                    );
                    // dd($rec,$value);
                }
        }


    }
    protected function normalizeKey(string $key): string
{
    // Lowercase
    $key = strtolower(trim($key));

    // Replace dots, spaces, and other non-word characters with underscore
    $key = preg_replace('/[^\w]+/', '_', $key);

    // Remove starting/trailing underscores
    $key = trim($key, '_');

    return $key;
}

    protected function syncRelation(Record $child, $row): void
    {
        // dd($this->module);

        if (!isset($this->relationMap[$this->module->id])) {
            return;
        }
        // dd($row);
        //  \Log::info($row);
        $config = $this->relationMap[$this->module->id];
        $parentExternalId = $row[$config['excel_column']] ?? null;
        // dd($config);
        //   \Log::info($config);
        if (!$parentExternalId) {
            $this->logError($row, 'Missing parent reference');
            return;
        }

        $parent = Record::where('module_id', $config['parent_module_id'])
            ->where('external_id', $parentExternalId)
            ->first();
        //    \Log::info($parent);
        if (!$parent && $this->strictParent) {
            $this->logError($row, 'Parent record not found');
            return;
        }

        if (!$parent) {
            $parent = Record::create([
                'module_id'   => $config['parent_module_id'],
                'external_id' => $parentExternalId,
                'created_by' => $this->userId,
            ]);
        }

        RecordRelation::updateOrCreate([
            'parent_record_id' => $parent->id,
            'child_record_id'  => $child->id,
            'relation_type'    => $config['relation_type'],
        ]);
    }

    protected function findDuplicate($row): ?Record
    {
        foreach ($this->fields as $field) {
            if (!$field->is_duplicate_key) continue;

            $key = strtolower($field->name);
            $value = $row[$key] ?? null;
            if (!$value) continue;

            $rv = RecordValue::where('field_id', $field->id)
                ->where('value', $value)
                ->first();

            if ($rv) {
                return $rv->record;
            }
        }

        return null;
    }

protected function castValue($value, string $type)
{
    return match ($type) {
        'number'  => is_numeric($value) ? $value : '',
        'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
        'date'    => $value ? Carbon::parse($value)->toDateString() : '',
        default   => is_array($value) || is_object($value)
                        ? json_encode($value)
                        : trim((string) $value),
    };
}


    protected function logError($row, string $message): void
    {
        DB::table('import_errors')->insert([
            'module_id'  => $this->module->id,
            'row_data'   => json_encode($row),
            'error'      => $message,
            'created_at'=> now(),
        ]);
    }

    public function chunkSize(): int { return 500; }
    public function batchSize(): int { return 500; }
}
