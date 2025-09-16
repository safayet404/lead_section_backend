<?php

namespace App\Imports;

use App\Models\Lead;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class LeadsImport implements ToModel, WithHeadingRow
{
    protected $extraData;

    public function __construct($extraData = [])
    {
        $this->extraData = $extraData;
    }

    public function model(array $row)
    {
        // Check for null or empty values to prevent database errors.
        if (empty($row['lead_date']) || empty($row['name']) || empty($row['email'])) {
            return null; // Skip this row if required data is missing.
        }

        return new Lead([
            // Standardizes date format from Excel to a Carbon instance.
            'lead_date' => Carbon::instance(Date::excelToDateTimeObject($row['lead_date'])),
            'name' => $row['name'],
            'email' => $row['email'],
            'phone' => $row['phone'] ?? null, // Use null coalesce for optional fields
            'interested_course' => $row['interested_course'] ?? null,
            'interested_country' => $row['interested_country'] ?? null,
            'current_qualification' => $row['current_qualification'] ?? null,
            'ielts_or_english_test' => $row['ielts_or_english_test'] ?? null,
            'source' => $row['source'] ?? null,

            // Assigns extra data passed from the controller.
            'lead_type' => $this->extraData['lead_type'] ?? null,
            'event_id' => $this->extraData['event_id'] ?? null,
            'lead_country' => $this->extraData['lead_country'],
            'lead_branch' => $this->extraData['lead_branch'],
            'status_id' => 1, // Sets default status to 1
            'created_by' => $this->extraData['created_by'],
        ]);
    }
}
