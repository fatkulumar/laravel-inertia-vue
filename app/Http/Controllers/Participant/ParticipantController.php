<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Regional;
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
                'participant_id' => Auth::user()->id,
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
            $userId = Auth::user()->id;
            $histories = User::with([
                                    'profile',
                                    'submissions.schedule.classRoom',
                                    'submissions.schedule.category',
                                    'submissions.schedule.regencyRegional',
                                    ])
                                ->where('id', $userId)
                                ->get();

            $histories->map(function ($history) {
                $this->fileSettings();
                $history->formatted_updated_at = isset($history->updated_at) ? Carbon::parse($history->updated_at)->format('d-m-Y') : null;
                foreach ($history->submissions as $submission) {
                    if (isset($submission->schedule->poster)) {
                        $submission->schedule->poster = $this->getFileAttribute($submission->schedule->poster);
                    } else {
                        $submission->schedule->poster = null;
                    }
                }

                foreach ($history->submissions as $submission) {
                    if (isset($submission->schedule->end_date_class)) {
                        $submission->schedule->formatted_start_date_class = Carbon::parse($submission->schedule->start_date_class)->format('Y-m-d');
                        $submission->schedule->formatted_end_date_class = Carbon::parse($submission->schedule->end_date_class)->format('Y-m-d');
                    } else {
                        $submission->schedule->formatted_start_date_class = null;
                        $submission->schedule->formatted_end_date_class = null;
                    }

                }

                return $history;
            });
            // return $histories;

            return Inertia::render('Participant/HistoryClass', [
                'histories' => $histories,
            ]);
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function historyClass in Participant/ParticipantController', $errors);
        }
    }

    public function certificate(Request $request, $credentialId)
    {
        // return $userId;

        try {
            $userId = Auth::user()->id;
            $users = User::with(['profile', 'submissions.schedule', 'certificate'])
                        ->where('id', $userId)
                        ->whereHas('certificate', function ($query) use ($credentialId) {
                            $query->where('credential_id', $credentialId);
                        })
                        ->get();
            // return $users;
            return Inertia::render('Participant/Certificate', [
                'certificate' => $users,
            ]);
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function certificate in Participant/ParticipantController', $errors);
        }
    }

    public function show()
    {
        try {
            $id = Auth::user()->id;
            $participant = User::with('profile.regional', 'submissions.schedule', 'submissions.schedule.classRoom', 'submissions.schedule.category')
            ->where('id', $id)->get();
        $participant->each(function ($user) {
            $this->fileSettings();

            // Proses gambar profil pengguna jika ada
            if (isset($user->profile->image)) {
                $user->profile->image = $this->getFileAttribute($user->profile->image);
            } else {
                $user->profile->image = null;
            }

            // Proses setiap submission pengguna
            $user->submissions->each(function ($submission) {
                if (isset($submission->schedule->poster)) {
                    $submission->schedule->poster = $this->getFileAttribute($submission->schedule->poster);
                } else {
                    $submission->schedule->poster = null;
                }

                return $submission;
            });

            return $user;
        });
        $regionals = Regional::all();
        // return $participant;
        return Inertia::render('Participant/DetailParticipant', [
            'participant' => $participant,
            'regionals' => $regionals,
        ]);
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function show in Participant/ParticipantController', $errors);
        }
    }
}
