<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Collection;

class CommunityOwnersExport implements FromCollection, WithHeadings, ShouldAutoSize
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
                $owner->fullname,
                $owner->email,
                $owner->mobile,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Full Name',
            'Email',
            'Phone', 
        ];
    }
}


