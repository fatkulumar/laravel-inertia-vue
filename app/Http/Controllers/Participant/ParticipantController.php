<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
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

    protected $directoryCertificate = "/file/certificate/";

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
            if ($proof) {
                $this->fileSettings();
                $uploadProof = $this->uploadFile($proof);
            } else {
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
            $profile = Profile::where('profileable_id', $users->id)->first('regional_id');
            $regency_regional_ids = Schedule::where('regional_id', $profile->regional_id)
                ->pluck('regency_regional_ids')
                ->toArray(); // Ambil array dari kolom regency_regional_ids
            // return $regency_regional_ids;
            $schedules = Schedule::with('classRoom:id,name', 'category:id,name')
                ->where('regional_id', $profile->regional_id)
                ->whereIn('regency_regional_ids', $regency_regional_ids)
                ->where('status', 'approval')
                ->whereDate('end_date_class', '>=', Carbon::now())
                ->whereDoesntHave('submissions')
                ->get(
                    [
                        'id',
                        'category_id',
                        'class_room_id',
                        'poster',
                        'start_date_class',
                        'end_date_class',
                        'benefit',
                        'facility',
                    ]
                );

            $schedules->map(function ($schedule) {
                $directorySchedule = '/file/schedule/';
                if (isset($schedule['poster'])) {
                    $schedule['poster'] = asset($directorySchedule . $schedule['poster']);
                } else {
                    $schedule['poster'] = null;
                }
                $schedule->formatted_end_date_class = Carbon::parse($schedule->end_date_class)->format('d-m-Y');
                $schedule->formatted_start_date_class = Carbon::parse($schedule->start_date_class)->format('d-m-Y');
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
                $schedules = Schedule::with(['classRoom:id,name', 'category:id,name', 'submissions:id,schedule_id,participant_id,status'])
                    ->where('regional_id', $profile->regional_id)
                    ->whereHas('submissions', function ($query) {
                        $query->where('status', 'approved'); // Sesuaikan dengan kondisi yang Anda inginkan
                    })
                    ->whereDate('end_date_class', '>=', Carbon::now())
                    ->get(
                        [
                            'id',
                            'category_id',
                            'class_room_id',
                            'poster',
                            'start_date_class',
                            'end_date_class',
                            'benefit',
                            'facility',
                        ]
                    );

                $schedules->map(function ($schedule) {
                    $directorySchedule = '/file/schedule/';
                    if (isset($schedule['poster'])) {
                        $schedule['poster'] = asset($directorySchedule . $schedule['poster']);
                    } else {
                        $schedule['link_poster'] = null;
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
                Log::channel('daily')->info('function eventActive in Participant/ParticipantController', $errors);
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
            $schedules = Schedule::with(['classRoom:id,name', 'category:id,name', 'submissions:id,schedule_id,participant_id,status'])
                ->where('regional_id', $profile->regional_id)
                ->whereHas('submissions', function ($query) {
                    $query->where('status', 'pending'); // Sesuaikan dengan kondisi yang Anda inginkan
                })
                ->whereDate('end_date_class', '>=', Carbon::now())
                ->get(
                    [
                        'id',
                        'category_id',
                        'class_room_id',
                        'poster',
                        'start_date_class',
                        'end_date_class',
                        'benefit',
                        'facility',
                    ]
                );

            $schedules->map(function ($schedule) {
                $directorySchedule = '/file/schedule/';
                if (isset($schedule['poster'])) {
                    $schedule['poster'] = asset($directorySchedule . $schedule['poster']);
                } else {
                    $schedule['poster'] = null;
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
                'profile:id,profileable_id,regional_id,address,hp,image,gender',
                'submissions:id,schedule_id,participant_id,status,created_at,updated_at',
                'submissions.schedule:id,regional_id,category_id,class_room_id,poster,start_date_class,end_date_class,benefit,facility,periode',
                'submissions.schedule.classRoom:id,name',
                'submissions.schedule.category:id,name',
                'submissions.schedule.regencyRegional:id,regency,regional_id',
                'submissions.certificate:id,credential_id',
            ])
                ->where('id', $userId)
                ->get('id', 'name', 'email', 'updated_at');

            $histories->map(function ($history) {
                $this->fileSettings();
                $history->formatted_updated_at = isset($history->updated_at) ? Carbon::parse($history->updated_at)->format('d-m-Y') : null;
                foreach ($history->submissions as $submission) {
                    $directorySchedule = '/file/schedule/';
                    if (isset($submission->schedule->poster)) {
                        $submission->schedule['link_poster'] = asset($directorySchedule . $submission->schedule->poster);
                    } else {
                        $submission->schedule['link_poster'] = null;
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
            $certificate = Certificate::with([
                'headOrganization',
                'user.submissions.schedule.category',
                'user.submissions.schedule.classRoom',
            ])
            ->where('user_id', $userId)
            ->where('credential_id', $credentialId)
            ->get();

            $certificate->map(function ($certif) {
                $certif->formatted_created_at = \Carbon\Carbon::now()->translatedFormat('l, d F Y');
                $certif->formatted_expired_at = \Carbon\Carbon::now()->translatedFormat('l, d F Y');
                $certif->image = isset($certif->image) ?  asset($this->directoryCertificate . $certif->image) : // get path public
                    '/user.png';
                return $certif;
            });
            // return $certificate;
            return Inertia::render('Participant/Certificate', [
                'certificate' => $certificate,
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
            $participant = User::with(
                [
                    'profile:id,profileable_id,regional_id,address,hp,image,gender',
                    'profile.regional:id,name',
                    'submissions:id,schedule_id,participant_id,status,created_at,updated_at',
                    'submissions.schedule:id,regional_id,category_id,class_room_id,poster',
                    'submissions.schedule.classRoom:id,name',
                    'submissions.schedule.category:id,name',
                ]
            )
            ->where('id', $id)
            ->get(['id', 'name', 'email', 'image']);
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
                    $directorySchedule = '/file/schedule/';
                    if (isset($submission->schedule->poster)) {
                        $submission->schedule->poster = asset($directorySchedule . $submission->schedule->poster);
                    } else {
                        $submission->schedule->poster = null;
                    }

                    return $submission;
                });

                return $user;
            });
            $regionals = Regional::all(['id', 'name']);
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

    public function changeImage(Request $request)
    {
        try {
            $validasiData = $this->changeImageValidator($request);
            if ($validasiData) return redirect()->back()->withErrors($validasiData)->withInput();

            $data = Certificate::where('credential_id', $request->post('credential_id'))->first();
            $filePath = public_path($this->directoryCertificate . $data->image);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $image = $request->file('image');
            if ($image) {
                $fileName = "{$image->hashName()}-{$image->getClientOriginalName()}";
                $image->move(public_path($this->directoryCertificate), $fileName);
                $uploadImage = $fileName;
            } else {
                $uploadImage = "Foto Tidak Ada";
            }

            $saveData = [
                'image' => $uploadImage,
            ];

            $result = Certificate::where('credential_id', $request->post('credential_id'))->update($saveData);

            if (!isset($result->id)) return redirect()->back()->withErrors($result)->withInput();
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function changeImage in Participant/ParticipantController', $errors);
        }
    }

    private function changeImageValidator(Request $request)
    {
        try {
            $rules = [
                'credential_id' => 'required|string|max:36',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

            ];
            $Validatedata = [
                'credential_id' => $request->post('credential_id'),
                'image' => $request->file('poster'),
            ];
            $validator = EntityValidator::validate($Validatedata, $rules);
            if ($validator->fails()) return $validator->errors();
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function changeImageValidator in Committee/ScheduleController', $errors);
        }
    }
}
