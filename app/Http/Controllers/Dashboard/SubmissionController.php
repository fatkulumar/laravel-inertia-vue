<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\HeadOrganization;
use App\Models\Profile;
use App\Models\Schedule;
use App\Models\Submission;
use App\Models\User;
use App\Traits\EntityValidator;
use App\Traits\FileUpload;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class SubmissionController extends Controller
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
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $submissions = Submission::orderBy('created_at', 'desc')
                ->when($request['search'], function ($query, $request) {
                    $query->whereHas('participant', function ($query) use ($request) {
                        $query->where('name', 'like', '%' . $request . '%');
                    });
                })
                ->with(['participant:id,name,email', 'schedule:id,category_id,class_room_id,committee_id', 'schedule.regional:id,name', 'schedule.classRoom:id,name', 'schedule.category:id,name', 'certificate:id,submission_id,user_id'])
                ->paginate(5)
                ->withQueryString()
                ->appends(['search' => $request['search']]);

            $submissions->map(function ($submission) {
                $this->fileSettings();
                if (isset($submission['proof'])) {
                    $submission['link_proof'] = $this->getFileAttribute($submission['proof']);
                } else {
                    $submission['link_proof'] = null;
                }
                $submission->formatted_end_date_class = Carbon::parse($submission->end_date_class)->format('d-m-Y');
                $submission->formatted_start_date_class = Carbon::parse($submission->start_date_class)->format('d-m-Y');
                return $submission;
            });

            $committeeIds = Schedule::distinct()->get(['committee_id']);
            $committees = User::whereIn('id', $committeeIds)->get(['id', 'name']);
            $participants = User::whereHas('roles', function ($query) {
                $query->where('name', 'peserta');
            })
            ->with('roles:name')
            ->select('id', 'name')
            ->get()
            ->map(function ($user) {
                $user->roles->transform(function ($role) {
                    // Hapus data pivot dari setiap role
                    unset($role->pivot);
                    return $role;
                });
                unset($user->roles);
                return $user;
            });

            // return $submissions;
            return Inertia::render('Dashboard/Submission', [
                'submissions' => $submissions,
                'committees' => $committees,
                'participants' => $participants,
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
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
            $id = $request->post('id');
            if ($id) {

                $submissions = Submission::where('id', $id)->first();
                $saveData = [
                    'schedule_id' => $request->post('schedule_id'),
                    'participant_id' => $request->post('participant_id'),
                    'status' => $request->post('status'),
                    'proof' => $uploadProof,
                ];
                $result = $submissions->update($saveData);
                if (!$result) return redirect()->back()->withErrors($result)->withInput();
            } else {

                $saveData = [
                    'schedule_id' => $request->post('schedule_id'),
                    'participant_id' => $request->post('participant_id'),
                    'status' => $request->post('status'),
                    'proof' => $uploadProof,
                ];

                $result = Submission::create($saveData);
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
                'schedule_id' => 'required|string|max:36',
                'status' => 'required|string|max:10',
                'proof' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ];
            $Validatedata = [
                'participant_id' => $request->post('participant_id'),
                'schedule_id' => $request->post('schedule_id'),
                'status' => $request->post('status'),
                'proof' => $request->file('proof'),
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
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
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

    public function rejectSubmission(Request $request)
    {
        try {
            $id = $request->post('id');
            // return $id;
            Submission::where('id', $id)->update([
                'status' => 'rejected',
            ]);
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function rejectSubmission in SubmissionController', $errors);
        }
    }

    public function approvalSubmission(Request $request)
    {
        try {
            $id = $request->post('id');
            Submission::where('id', $id)->update([
                'status' => 'approved',
            ]);
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function rejectSubmission in SubmissionController', $errors);
        }
    }

    public function deleteSubmission(Request $request, $id)
    {
        try {
            Submission::where('id', $id)->delete();
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function rejectSubmission in SubmissionController', $errors);
        }
    }

    public function graduatedSubmission(Request $request)
    {
        try {
            $id = $request->post('id');
            Submission::where('id', $id)->update([
                'status' => 'graduated',
            ]);
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function graduatedSubmission in SubmissionController', $errors);
        }
    }

    public function optionSubmission(Request $request)
    {
        try {
            $ids = $request->post('id');
            $status = $request->post('status');
            foreach ($ids as $id) {
                Submission::where('id', $id)->update([
                    'status' => $status,
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

    public function certificateSubmission(Request $request)
    {
        try {
            $validasiData = $this->certificateValidator($request);
            if ($validasiData) return redirect()->back()->withErrors($validasiData)->withInput();
            $headOrgnization = HeadOrganization::where('status', 'active')->first('id');
            $profile = Profile::where('profileable_id', $request->post('participant_id'))->first();
            $submission_id = $request->post('submission_id');
            $credential_id = $request->post('credential_id');
            $participant_id = $request->post('participant_id');
            $saveData = [
                'submission_id' => $submission_id,
                'credential_id' => $credential_id,
                'user_id' => $participant_id,
                'head_organization_id' => $headOrgnization->id,
                'expired_at' => Carbon::now()->addYears(2),
                'image' => $profile->image,
            ];
            Certificate::create($saveData);
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function certificateSubmission in SubmissionController', $errors);
        }
    }

    private function certificateValidator(Request $request)
    {
        try {
            $rules = [
                'submission_id' => 'required|string|max:36',
                'credential_id' => 'required|string|max:36|unique:certificates,credential_id',
                'certificateable_id' => 'required|string|max:36',
            ];
            $Validatedata = [
                'submission_id' => $request->post('submission_id'),
                'credential_id' => $request->post('credential_id'),
                'certificateable_id' => $request->post('participant_id'),
            ];
            $validator = EntityValidator::validate($Validatedata, $rules);
            if ($validator->fails()) return $validator->errors();
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function certificateValidator in SubmissionController', $errors);
        }
    }

    public function schedule(Request $request, $committeeId)
    {
        try {
            $profile = Profile::where('profileable_id', $committeeId)->first();
            $schedule = Schedule::with(['classRoom:id,name', 'category:id,name'])
                ->where('committee_id', $committeeId)
                ->where('regional_id', $profile->regional_id)
                ->select('class_room_id', 'category_id', 'id')
                ->get();
            return response()->json($schedule, 200);
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function schedule in SubmissionController', $errors);
        }
    }
}
