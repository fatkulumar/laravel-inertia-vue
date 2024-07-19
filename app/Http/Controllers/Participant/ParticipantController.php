<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Schedule;
use App\Models\Submission;
use App\Models\User;
use App\Traits\EntityValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Traits\FileUpload;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ParticipantController extends Controller
{
    use EntityValidator;
    use FileUpload;

    protected function fileSettings()
    {
        $this->settings = [
            'attributes'  => ['jpeg', 'jpg', 'png'],
            'path'        => 'file/proof/',
            'softdelete'  => false
        ];
    }

    public function RegisterClass(Request $request)
    {
        try {
            $validasiData = $this->storeValidator($request);
            if ($validasiData) return redirect()->back()->withErrors($validasiData)->withInput();

            $proof = $request->file('proof');
            if($proof) {
                $this->fileSettings();
                $uploadProof = $this->uploadFile($proof);
            }else{
                $uploadProof = "Proof Tidak Ada";
            }

            $saveData = [
                'schedule_id' => $request->post('schedule_id'),
                'user_id' => Auth::user()->id,
                'proof' => $uploadProof,
            ];

            $result = Submission::create($saveData);
            if (!isset($result->id)) return redirect()->back()->withErrors($result)->withInput();

        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function RegisterClass in Participant/ParticipantController', $errors);
        }
    }

    private function storeValidator(Request $request)
    {
        try {
            $rules = [
                'schedule_id' => 'required|string|max:36',
                'proof' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

            ];
            $Validatedata = [
                'schedule_id' => $request->post('schedule_id'),
                'proof' => $request->file('poster'),
            ];
            $validator = EntityValidator::validate($Validatedata, $rules);
            if ($validator->fails()) return $validator->errors();
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function storeValidator in Committee/ScheduleController', $errors);
        }
    }

    public function eventAvailable()
    {
        try {
            $users = Auth::user();
            $profile = Profile::where('profileable_id', $users->id)->first();
            $schedules = Schedule::with('classRoom', 'category')->where('regional_id', $profile->regional_id)
                ->where('status', 'approval')
                ->whereDate('end_date_class', '>=', Carbon::now())
                ->whereDoesntHave('submissions')
                ->get();

            $schedules->map(function ($schedule) {
                $this->fileSettings();
                if (isset($schedule['poster'])) {
                    $schedule['poster'] = $this->getFileAttribute($schedule['poster']);
                } else {
                    $schedule['link_poster'] = null;
                }
                if (isset($schedule['proposal'])) {
                    $schedule['proposal'] = $this->getFileAttribute($schedule['proposal']);
                } else {
                    $schedule['link_proposal'] = null;
                }
                $schedule->formatted_end_date_class = Carbon::parse($schedule->end_date_class)->format('Y-m-d');
                $schedule->formatted_start_date_class = Carbon::parse($schedule->start_date_class)->format('Y-m-d');
                return $schedule;
            });

            // return $schedules;
            return Inertia::render('Participant/ClassAvailable', [
                'schedule' => $schedules,
            ]);
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function index in Participant/ParticipantController', $errors);
        }
    }

    public function eventActive()
    {
        try {
            try {
                $users = Auth::user();
                $profile = Profile::where('profileable_id', $users->id)->first();
                $schedules = Schedule::with('classRoom', 'category', 'submissions')
                    ->where('regional_id', $profile->regional_id)
                    ->whereHas('submissions', function ($query) {
                        $query->where('status', 'approved'); // Sesuaikan dengan kondisi yang Anda inginkan
                    })
                    ->whereDate('end_date_class', '>=', Carbon::now())
                    ->get();

                $schedules->map(function ($schedule) {
                    $this->fileSettings();
                    if (isset($schedule['poster'])) {
                        $schedule['poster'] = $this->getFileAttribute($schedule['poster']);
                    } else {
                        $schedule['link_poster'] = null;
                    }
                    if (isset($schedule['proposal'])) {
                        $schedule['proposal'] = $this->getFileAttribute($schedule['proposal']);
                    } else {
                        $schedule['link_proposal'] = null;
                    }
                    $schedule->formatted_end_date_class = Carbon::parse($schedule->end_date_class)->format('Y-m-d');
                    $schedule->formatted_start_date_class = Carbon::parse($schedule->start_date_class)->format('Y-m-d');
                    return $schedule;
                });
                // return $schedules;
                return Inertia::render('Participant/ClassActive', [
                    'schedule' => $schedules,
                ]);
            } catch (\Exception $exception) {
                $errors['message'] = $exception->getMessage();
                $errors['file'] = $exception->getFile();
                $errors['line'] = $exception->getLine();
                $errors['trace'] = $exception->getTrace();
                Log::channel('daily')->info('function waitingApproval in Participant/ParticipantController', $errors);
            }
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function eventActive in Participant/ParticipantController', $errors);
        }
    }

    public function waitingApproval()
    {
        try {
            $users = Auth::user();
            $profile = Profile::where('profileable_id', $users->id)->first();
            $schedules = Schedule::with('classRoom', 'category', 'submissions')
                ->where('regional_id', $profile->regional_id)
                ->whereHas('submissions', function ($query) {
                    $query->where('status', 'pending'); // Sesuaikan dengan kondisi yang Anda inginkan
                })
                ->whereDate('end_date_class', '>=', Carbon::now())
                ->get();

            $schedules->map(function ($schedule) {
                $this->fileSettings();
                if (isset($schedule['poster'])) {
                    $schedule['poster'] = $this->getFileAttribute($schedule['poster']);
                } else {
                    $schedule['link_poster'] = null;
                }
                if (isset($schedule['proposal'])) {
                    $schedule['proposal'] = $this->getFileAttribute($schedule['proposal']);
                } else {
                    $schedule['link_proposal'] = null;
                }
                $schedule->formatted_end_date_class = Carbon::parse($schedule->end_date_class)->format('Y-m-d');
                $schedule->formatted_start_date_class = Carbon::parse($schedule->start_date_class)->format('Y-m-d');
                return $schedule;
            });
            // return $schedules;
            return Inertia::render('Participant/WaitingApproval', [
                'schedule' => $schedules,
            ]);
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function waitingApproval in Participant/ParticipantController', $errors);
        }
    }

    public function historyClass()
    {
        try {
            $users = Auth::user();
            $profile = Profile::where('profileable_id', $users->id)->first();
            $schedules = Schedule::with('classRoom', 'category', 'submissions')
                ->where('regional_id', $profile->regional_id)
                ->whereHas('submissions', function ($query) {
                    $query->where('status', 'graduated'); // Sesuaikan dengan kondisi yang Anda inginkan
                })
                ->whereDate('end_date_class', '>=', Carbon::now())
                ->get();

            $schedules->map(function ($schedule) {
                $this->fileSettings();
                if (isset($schedule['poster'])) {
                    $schedule['poster'] = $this->getFileAttribute($schedule['poster']);
                } else {
                    $schedule['link_poster'] = null;
                }
                if (isset($schedule['proposal'])) {
                    $schedule['proposal'] = $this->getFileAttribute($schedule['proposal']);
                } else {
                    $schedule['link_proposal'] = null;
                }
                $schedule->formatted_end_date_class = Carbon::parse($schedule->end_date_class)->format('Y-m-d');
                $schedule->formatted_start_date_class = Carbon::parse($schedule->start_date_class)->format('Y-m-d');
                return $schedule;
            });
            // return $schedules;
            return Inertia::render('Participant/HistoryClass', [
                'schedule' => $schedules,
            ]);
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function waitingApproval in Participant/ParticipantController', $errors);
        }
    }
}
