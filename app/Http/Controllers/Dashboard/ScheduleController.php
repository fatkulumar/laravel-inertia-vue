<?php

namespace App\Http\Controllers\Dashboard;

use App\Exports\TotalFemaleGraduatedParticipantByScheduleClassExport;
use App\Exports\TotalFemaleNotGraduatedParticipantByScheduleClassExport;
use App\Exports\TotalFemaleParticipantByScheduleClassExport;
use App\Exports\TotalGraduatedParticipantByScheduleClassExport;
use App\Exports\TotalMaleGraduatedParticipantByScheduleClassExport;
use App\Exports\TotalMaleNotGraduatedParticipantByScheduleClassExport;
use App\Exports\TotalMaleParticipantByScheduleClassExport;
use App\Exports\TotalNotGraduatedParticipantByScheduleClassExport;
use App\Exports\TotalParticipantByScheduleClassExport;
use App\Http\Controllers\Controller;
use App\Models\AppointmentFile;
use App\Models\Schedule;
use App\Models\Submission;
use App\Models\User;
use App\Traits\EntityValidator;
use App\Traits\FileUpload;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class ScheduleController extends Controller
{
    use EntityValidator;
    use FileUpload;

    protected function fileSettings()
    {
        $this->settings = [
            'attributes'  => ['jpeg', 'jpg', 'png'],
            'path'        => 'file/schedule/',
            'softdelete'  => false
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $schedules = Schedule::orderBy('created_at', 'desc')
                ->when($request['search'], function($query, $request) {
                    $query->whereHas('committee', function ($query) use ($request) {
                        $query->where('name', 'like', '%' . $request . '%');
                    });
                })
                ->with('committee', 'classRoom', 'category', 'regional')
                ->paginate(5)
                ->withQueryString()
                ->appends(['search' => $request['search']]);

            $schedules->map(function ($submission) {
                $this->fileSettings();
                if (isset($submission['poster'])) {
                    $submission['poster'] = $this->getFileAttribute($submission['poster']);
                } else {
                    $submission['link_poster'] = null;
                }
                if (isset($submission['proposal'])) {
                    $submission['link_proposal'] = $this->getFileAttribute($submission['proposal']);
                } else {
                    $submission['link_proposal'] = null;
                }
                $submission->formatted_end_date_class = Carbon::parse($submission->end_date_class)->format('Y-m-d');
                $submission->formatted_start_date_class = Carbon::parse($submission->start_date_class)->format('Y-m-d');
                return $submission;
            });
            // return $schedules;
            return Inertia::render('Dashboard/Schedule/Schedule', [
                'schedules' => $schedules,
            ]);
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function index in Dashboard/SubmissionController', $errors);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validasiData = $this->storeValidator($request);
            if ($validasiData) return redirect()->back()->withErrors($validasiData)->withInput();

            $id = $request->post('id');
            if ($id) {

                $submissions = Schedule::where('id', $id)->first();
                $saveData = [
                    'participant_id' => $request->post('participant_id'),
                    'committeee_id' => $request->post('committeee_id'),
                    'status' => $request->post('status'),
                    'file' => $request->post('file'),
                ];
                $result = $submissions->update($saveData);
                if (!$result) return redirect()->back()->withErrors($result)->withInput();
            } else {

                $saveData = [
                    'participant_id' => $request->post('participant_id'),
                    'committeee_id' => $request->post('committeee_id'),
                    'status' => $request->post('status'),
                    'file' => $request->post('file'),
                ];

                $result = Schedule::create($saveData);
                if (!isset($result->id)) return redirect()->back()->withErrors($result)->withInput();
            }
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function store in SubmissionController', $errors);
        }
    }

    private function storeValidator(Request $request)
    {
        try {
            $rules = [
                'participant_id' => 'required|string|max:36',
                'committeee_id' => 'required|string|max:36',
                'status' => 'required|string|max:15',
                'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ];
            $Validatedata = [
                'participant_id' => $request->post('participant_id'),
                'committeee_id' => $request->post('committeee_id'),
                'status' => $request->post('status'),
                'file' => $request->post('file'),
            ];
            $validator = EntityValidator::validate($Validatedata, $rules);
            if ($validator->fails()) return $validator->errors();
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function storeValidator in SubmissionController', $errors);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function delete(string $id)
    {
        try {
            Schedule::findOrFail($id)->delete();
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function delete in SubmissionController', $errors);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try {
            $ids = $request->post('id');
            foreach ($ids as $id) {
                Schedule::findOrFail($id)->delete();
            }
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function delete in SubmissionController', $errors);
        }
    }

    public function rejectSchedule(Request $request)
    {
        try {
            $id = $request->post('id');
            Schedule::where('id', $id)->update([
                'status' => 'rejected',
                'approval_date' => Carbon::now(),
            ]);
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function rejectSubmission in SubmissionController', $errors);
        }
    }

    public function approvalSchedule(Request $request)
    {
        try {
            $id = $request->post('id');
            Schedule::where('id', $id)->update([
                'status' => 'approval',
                'approval_date' => Carbon::now(),
            ]);
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function rejectSubmission in SubmissionController', $errors);
        }
    }

    public function overviewSchedule(Request $request)
    {
        try {
            $id = $request->post('id');
            Schedule::where('id', $id)->update([
                'status' => 'overview',
                'date_overview' => Carbon::now(),
            ]);
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function rejectSubmission in SubmissionController', $errors);
        }
    }

    public function receivedSchedule(Request $request)
    {
        try {
            $id = $request->post('id');
            Schedule::where('id', $id)->update([
                'status' => 'received',
                'date_received' => Carbon::now(),
            ]);
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function rejectSubmission in SubmissionController', $errors);
        }
    }

    public function deleteSchedule(Request $request, $id)
    {
        try {
            Schedule::where('id', $id)->delete();
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function rejectSubmission in SubmissionController', $errors);
        }
    }

    public function optionSchedule(Request $request)
    {
        try {
            $ids = $request->post('id');
            $status = $request->post('status');
            foreach($ids as $id) {
                Schedule::where('id', $id)->update([
                    'status' => $status,
                    'graduation_date' => Carbon::now(),
                ]);
            }
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function rejectSubmission in SubmissionController', $errors);
        }
    }

    public function report(Request $request, $scheduleId)
    {
        try {
            // Mengambil pengguna dengan role 'peserta'
            $participants = User::with(['profile', 'submissions'])
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'peserta');
                })
                ->whereHas('submissions', function ($query) use ($scheduleId) {
                    $query->where('schedule_id', $scheduleId);
                })
                ->get();

            $totalParticipants = $participants->count();

            $totalMaleParticipants = $participants->filter(function ($user) {
                return $user->profile && $user->profile->gender == 'laki-laki';
            })->count();

            $totalFemaleParticipants = $participants->filter(function ($user) {
                return $user->profile && $user->profile->gender == 'perempuan';
            })->count();

            $totalGraduatedParticipants = $participants->filter(function ($user) {
                return $user->submissions->contains('status', 'graduated');
            })->count();

            $totalNotGraduatedParticipants = $participants->filter(function ($user) {
                return !$user->submissions->contains('status', 'graduated');
            })->count();

            $totalGraduatedMaleParticipants = $participants->filter(function ($user) {
                return $user->submissions->contains('status', 'graduated') && $user->profile && $user->profile->gender == 'laki-laki';
            })->count();

            $totalGraduatedFemaleParticipants = $participants->filter(function ($user) {
                return $user->submissions->contains('status', 'graduated') && $user->profile && $user->profile->gender == 'perempuan';
            })->count();

            $totalNotGraduatedMaleParticipants = $participants->filter(function ($user) {
                return !$user->submissions->contains('status', 'graduated') && $user->profile && $user->profile->gender == 'laki-laki';
            })->count();

            $totalNotGraduatedFemaleParticipants = $participants->filter(function ($user) {
                return !$user->submissions->contains('status', 'graduated') && $user->profile && $user->profile->gender == 'perempuan';
            })->count();

            $reports = [
                'participants' => [
                    'total' => $totalParticipants,
                    'male' => $totalMaleParticipants,
                    'female' => $totalFemaleParticipants,
                ],
                'participant_graduated' => [
                    'total' => $totalGraduatedParticipants,
                    'male' => $totalGraduatedMaleParticipants,
                    'female' => $totalGraduatedFemaleParticipants,
                ],
                'participant_not_graduated' => [
                    'total' => $totalNotGraduatedParticipants,
                    'male' => $totalNotGraduatedMaleParticipants,
                    'female' => $totalNotGraduatedFemaleParticipants,
                ],
            ];

            $appointmentFiles = AppointmentFile::where('schedule_id', $scheduleId)->get();

            $appointmentFiles->map(function ($appointmentFile) {
                $this->fileSettings();
                if (isset($appointmentFile['file'])) {
                    $appointmentFile['file'] = $this->getFileAttribute($appointmentFile['file']);
                }
                return $appointmentFile;
            });
            // return $appointmentFiles;

            return Inertia::render('Dashboard/Schedule/Report', [
                'reports' => $reports,
                'appointmentFiles' => $appointmentFiles,
            ]);
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function report in SubmissionController', $errors);
        }
    }

    function downloadReportTotalParticipantByScheduleClass(Request $request, $scheduleId)
    {
        try {
            return Excel::download(new TotalParticipantByScheduleClassExport($scheduleId), "Total Peserta.xlsx");
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function downloadReportParticipantByScheduleClass in Committee/ScheduleController', $errors);
        }
    }

    function downloadReportTotalMaleParticipantByScheduleClass(Request $request, $scheduleId)
    {
        try {
            return Excel::download(new TotalMaleParticipantByScheduleClassExport($scheduleId), "Total Peserta Laki Laki.xlsx");
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function downloadReportTotalMaleParticipantByScheduleClass in Committee/ScheduleController', $errors);
        }
    }

    function downloadReportTotalFemaleParticipantByScheduleClass(Request $request, $scheduleId)
    {
        try {
            return Excel::download(new TotalFemaleParticipantByScheduleClassExport($scheduleId), "Total Peserta Perempuan.xlsx");
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function downloadReportTotalMaleParticipantByScheduleClass in Committee/ScheduleController', $errors);
        }
    }

    function downloadReportTotalGraduatedParticipantByScheduleClass(Request $request, $scheduleId)
    {
        try {
            return Excel::download(new TotalGraduatedParticipantByScheduleClassExport($scheduleId), "Total Peserta Lulus.xlsx");
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function downloadReportTotalGraduatedParticipantByScheduleClass in Committee/ScheduleController', $errors);
        }
    }

    function downloadReportTotalMaleGraduatedParticipantByScheduleClass(Request $request, $scheduleId)
    {
        try {
            return Excel::download(new TotalMaleGraduatedParticipantByScheduleClassExport($scheduleId), "Total Peserta Lulus Laki Laki.xlsx");
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function downloadReportTotalMaleGraduatedParticipantByScheduleClass in Committee/ScheduleController', $errors);
        }
    }

    function downloadReportTotalFemaleGraduatedParticipantByScheduleClass(Request $request, $scheduleId)
    {
        try {
            return Excel::download(new TotalFemaleGraduatedParticipantByScheduleClassExport($scheduleId), "Total Peserta Lulus Perempuab.xlsx");
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function downloadReportTotalFemaleGraduatedParticipantByScheduleClass in Committee/ScheduleController', $errors);
        }
    }

    function downloadReportTotalNotGraduatedParticipantByScheduleClass(Request $request, $scheduleId)
    {
        try {
            return Excel::download(new TotalNotGraduatedParticipantByScheduleClassExport($scheduleId), "Total Peserta Tidak Lulus.xlsx");
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function downloadReportTotalNotGraduatedParticipantByScheduleClass in Committee/ScheduleController', $errors);
        }
    }

    function downloadReportTotalMaleNotGraduatedParticipantByScheduleClass(Request $request, $scheduleId)
    {
        try {
            return Excel::download(new TotalMaleNotGraduatedParticipantByScheduleClassExport($scheduleId), "Total Peserta Laki Laki Tidak Lulus.xlsx");
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function downloadReportTotalMaleNotGraduatedParticipantByScheduleClass in Committee/ScheduleController', $errors);
        }
    }

    function downloadReportTotalFemaleNotGraduatedParticipantByScheduleClass(Request $request, $scheduleId)
    {
        try {
            return Excel::download(new TotalFemaleNotGraduatedParticipantByScheduleClassExport($scheduleId), "Total Peserta Perempuan Tidak Lulus.xlsx");
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function downloadReportTotalFemaleNotGraduatedParticipantByScheduleClass in Committee/ScheduleController', $errors);
        }
    }

    public function uploadAppointmentFile(Request $request)
    {
        try {
            $id = $request->post('id');
            if ($id) {
                $validasiData = $this->updateAppointmentFileValidator($request);
                if ($validasiData) return redirect()->back()->withErrors($validasiData)->withInput();
                // return $validasiData;

                // return $validasiData;
                $appointmentFile = AppointmentFile::where('id', $id)->first();

                $file = $request->file('file');
                if ($file) {
                    $this->fileSettings();
                    $uploadFile = $this->uploadFile($file);
                } else {
                    $uploadFile = $appointmentFile->file;
                }


                $saveData = [
                    'schedule_id' => $request->post('schedule_id'),
                    'name' => $request->post('name'),
                    'file' => $uploadFile,
                ];

                $result = $appointmentFile->update($saveData);

                // return $schedule;
                if (!$result) return redirect()->back()->withErrors($result)->withInput();
            } else {
                // return $request->all();
                $validasiData = $this->storeAppointmentFileValidator($request);
                if ($validasiData) return redirect()->back()->withErrors($validasiData)->withInput();
                $file = $request->file('file');
                if ($file) {
                    $this->fileSettings();
                    $uploadFile = $this->uploadFile($file);
                } else {
                    $uploadFile = "File Tidak Ada";
                }

                $saveData = [
                    'schedule_id' => $request->post('schedule_id'),
                    'name' => $request->post('name'),
                    'file' => $uploadFile,
                ];
                $result = AppointmentFile::create($saveData);

                if (!isset($result->id)) return redirect()->back()->withErrors($result)->withInput();
            }
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function uploadAppointmentFile in Committee/ScheduleController', $errors);
        }
    }

    private function updateAppointmentFileValidator(Request $request)
    {
        try {
            $rules = [
                'schedule_id' => 'required|string|max:36',
                'name' => 'required|string|max:5000',
                'file' => 'nullable|mimes:pdf|max:2048',

            ];
            $Validatedata = [
                'schedule_id' => $request->post('schedule_id'),
                'name' => $request->post('name'),
                'file' => $request->file('file'),
            ];
            $validator = EntityValidator::validate($Validatedata, $rules);
            if ($validator->fails()) return $validator->errors();
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function updateAppointmentFileValidator in Dashboard/ScheduleController', $errors);
        }
    }

    private function storeAppointmentFileValidator(Request $request)
    {
        try {
            $rules = [
                'schedule_id' => 'required|string|max:36',
                'name' => 'required|string|max:5000',
                'file' => 'required|mimes:pdf|max:2048',

            ];
            $Validatedata = [
                'schedule_id' => $request->post('schedule_id'),
                'name' => $request->post('name'),
                'file' => $request->file('file'),
            ];
            $validator = EntityValidator::validate($Validatedata, $rules);
            if ($validator->fails()) return $validator->errors();
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function storeAppointmentFileValidator in Dashboard/ScheduleController', $errors);
        }
    }
}
