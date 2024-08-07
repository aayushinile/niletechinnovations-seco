<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Collection;

class CommunityOwnerExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $owners;

    public function __construct(Collection $owners)
    {
        $this->owners = $owners;
    }

    public function collection()
    {
        return $this->owners->map(function ($owner) {
            return [
                $owner->id,
                $owner->business_name,
                $owner->email,
                $owner->mobile,
                $owner->status,
                $owner->created_at,
                'in' // Static value for the 'Data' column
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Business Name',
            'Email',
            'Mobile',
            'Status',
            'Created At',
            'Data' // Adding a heading for the 'Data' column
        ];
    }
}


