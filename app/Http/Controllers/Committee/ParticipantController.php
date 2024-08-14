<?php

namespace App\Http\Controllers\Committee;

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
use Illuminate\Support\Facades\DB;

class ParticipantController extends Controller
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

    protected $directoryCertificate = '/file/certificate/';
    private function updateValidator(Request $request)
    {
        try {
            $rules = [
                'participant_id' => 'required|string|max:36',
                'name' => 'required|string|max:100',
                'address' => 'required|string|2000',
                'hp' => 'required|string|max:15',
                'regional_id' => 'required|string|max:36',
            ];
            $Validatedata = [
                'participant_id' => $request->post('participant_id'),
                'name' => $request->post('name'),
                'address' => $request->post('address'),
                'hp' => $request->post('hp'),
                'regional_id' => $request->post('regional_id'),
            ];
            $validator = EntityValidator::validate($Validatedata, $rules);
            if ($validator->fails()) return $validator->errors();
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function updateValidator in SubmissionController', $errors);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
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
        return Inertia::render('Committee/DetailParticipant', [
            'participant' => $participant,
            'regionals' => $regionals,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request)
    {
        // return $request;
        DB::beginTransaction();
        try {
            $validasiData = $this->updateValidator($request);
            if ($validasiData) return redirect()->back()->withErrors($validasiData)->withInput();

            $id = $request->post('participant_id');

            $updateDataUser = [
                'name' => $request->post('name'),
            ];
            User::where('id', $id)->update($updateDataUser);

            $updateDataProfile = [
                'address' => $request->post('address'),
                'hp' => $request->post('hp'),
                'regional_id' => $request->post('regional_id'),
            ];
            $result = Profile::where('profileable_id', $id)->update($updateDataProfile);
            DB::commit();
            if (!$result) return redirect()->back()->withErrors($result)->withInput();
        } catch (\Exception $exception) {
            DB::rollBack();
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function store in SubmissionController', $errors);
        }
    }

    public function participantClassRoom(Request $request)
    {
        try {
            $submissions = Submission::orderBy('created_at', 'desc')
                ->when($request['search'], function ($query, $request) {
                    $query->whereHas('participant', function ($query) use ($request) {
                        $query->where('name', 'like', '%' . $request . '%');
                    });
                })
                ->whereHas('participant.roles')
                ->whereHas('participant.profile.regional')
                ->with('schedule.classRoom', 'schedule.category', 'participant.roles', 'participant.profile.regional')
                ->paginate(5)
                ->withQueryString()
                ->appends(['search' => $request['search']]);

            $submissions->map(function ($submission) {
                $this->fileSettings();
                if (isset($submission->participant->image)) {
                    $submission->participant->image = $this->getFileAttribute($submission->participant->image);
                } else {
                    $submission->participant->image = null;
                }

                if (isset($submission->schedule->poster)) {
                    $submission->schedule->poster = $this->getFileAttribute($submission->schedule->poster);
                } else {
                    $submission->schedule->poster = null;
                }
                return $submission;
            });
            // return $submissions;
            return Inertia::render('Committee/ParticipantClassRoom', [
                'submissions' => $submissions,
            ]);
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function participantClassRoom in SubmissionController', $errors);
        }
    }

    public function historyClass(Request $request, $userId)
    {
        // return $userId;

        try {
            $users = Auth::user();
            $profile = Profile::with('regional')->where('profileable_id', $users->id)->first();
            $schedules = Schedule::with([
                'classRoom',
                'category',
                'submissions.certificate',
                'submissions' => function ($query) use ($userId) {
                    $query->whereHas('participant', function ($query) use ($userId) {
                        $query->where('id', $userId);
                    });
                },
                'regencyRegional'
            ])
            ->where('regional_id', $profile->regional_id)
            ->whereHas('submissions', function ($query) use ($userId) {
                $query->whereHas('participant', function ($query) use ($userId) {
                    $query->where('id', $userId);
                });
            })
            ->get();

            $schedules->map(function ($schedule) {
                $this->fileSettings();
                foreach ($schedule->submissions as $submission) {
                    if (isset($submission->schedule->updated_at)) {
                        $schedule->formatted_updated_at = isset($schedule->updated_at) ? Carbon::parse($schedule->updated_at)->format('d-m-Y') : null;;
                    } else {
                        $schedule->formatted_updated_at = null;
                    }
                }

                if (isset($schedule['poster'])) {
                    $schedule['poster'] = $this->getFileAttribute($schedule['poster']);
                } else {
                    $schedule['poster'] = null;
                }
                $schedule->formatted_end_date_class = Carbon::parse($schedule->end_date_class)->format('Y-m-d');
                $schedule->formatted_start_date_class = Carbon::parse($schedule->start_date_class)->format('Y-m-d');
                return $schedule;
            });
            // return $schedules;
            return Inertia::render('Committee/Participant/HistoryClass', [
                'schedules' => $schedules,
            ]);
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function historyClass in Participant/ParticipantController', $errors);
        }
    }

    public function certificate(Request $request, $credentialId, $userId)
    {
        try {

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
                $certif->image = $certif->image ? asset($this->directoryCertificate . $certif->image) : '/user.png';
                return $certif;
            });

            // return $certificate;
            return Inertia::render('Committee/Participant/Certificate', [
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
            Log::channel('daily')->info('function changeImage in Commmittee/ParticipantController', $errors);
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
