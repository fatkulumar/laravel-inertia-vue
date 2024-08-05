<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TotalFemaleGraduatedParticipantByScheduleClassExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;
    protected $scheduleId;

    public function __construct($scheduleId)
    {
        $this->scheduleId = $scheduleId;
    }


    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'Email',
            'Jenis Kelamin',
            'Kelas',
            'Mulai',
            'Selesai',
            'Status',
            'TTD',
        ];
    }

    public function map($user): array
    {
        $result = [];
        static $number = 1;
        foreach ($user->submissions as $submission) {
            $result[] = [
                $number++, // assuming this is the "No"
                $user->name,
                $user->email,
                optional($user->profile)->gender,
                optional($submission->schedule->classRoom)->name ." ". optional($submission->schedule->category)->name,
                $submission->schedule->start_date_class,
                $submission->schedule->end_date_class,
                $submission->status == 'graduated' ? 'Lulus' : 'Tidak Lulus',
            ];
        }

        return $result;
    }

    public function query()
    {
        return User::with(['profile', 'submissions.schedule.classRoom', 'submissions.schedule.category'])
        ->whereHas('roles', function ($query) {
            $query->where('name', 'peserta');
        })
        ->whereHas('submissions', function ($query) {
            $query->where('schedule_id', $this->scheduleId)
                 ->where('status', 'graduated');
        })
        ->whereHas('profile', function ($query) {
            $query->where('gender', 'perempuan');
        });
    }
}
