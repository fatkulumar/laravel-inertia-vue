<?php

namespace App\Http\Controllers\Committee;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\User;
use App\Traits\EntityValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Traits\FileUpload;

class ParticipantController extends Controller
{
    use EntityValidator;
    use FileUpload;

    protected function fileSettings()
    {
        $this->settings = [
            'attributes'  => ['jpeg', 'jpg', 'png'],
            'path'        => 'file/proposal/',
            'softdelete'  => false
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $idSubmission)
    {
        try {
            $participants = Submission::orderBy('created_at', 'desc')
                ->when($request['search'], function($query, $request) {
                    $query->whereHas('participant', function ($query) use ($request) {
                        $query->where('name', 'like', '%' . $request . '%');
                    });
                })
                ->whereHas('participant.roles')
                ->whereHas('participant.profile.regional')
                ->with('participant.roles', 'participant.profile.regional')
                ->where('committee_id', Auth()->user()->id)
                ->paginate(5)
                ->withQueryString()
                ->appends(['search' => $request['search']]);

        $users = User::with('roles', 'profile.regional')
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'peserta'); // Gantilah 'name' dengan kolom yang sesuai dalam tabel roles
                })
                ->doesntHave('submissions') // Pastikan User tidak memiliki relasi dengan tabel submissions
                ->get();
            // return $users;
            return Inertia::render('Committee/Schedule/Participant', [
                'participants' => $participants,
                'users' => $users,
            ]);
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function index in Committee/ScheduleController', $errors);
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
        // return $request;
        try {
            $validasiData = $this->storeValidator($request);
            if ($validasiData) return redirect()->back()->withErrors($validasiData)->withInput();

            $id = $request->post('id');
            if ($id) {

                $submissions = Submission::where('id', $id)->first();
                $saveData = [
                    'participant_id' => $request->post('participant_id'),
                    'committee_id' => $request->post('committee_id'),
                    'category_id' => $request->post('category_id'),
                    'class_room_id' => $request->post('class_room_id'),
                    'start_date_class' => $request->post('start_date_class'),
                    'end_date_class' => $request->post('end_date_class'),
                    'location' => $request->post('location'),
                    'goggle_maps' => $request->post('goggle_maps'),
                    'status' => $request->post('status'),
                    'address' => $request->post('address'),
                    'periode' => $request->post('periode'),
                    'file' => $request->post('file'),
                ];
                $result = $submissions->update($saveData);
                if (!$result) return redirect()->back()->withErrors($result)->withInput();
            } else {

                $saveData = [
                    'participant_id' => $request->post('participant_id'),
                    'committee_id' => $request->post('committee_id'),
                    'category_id' => $request->post('category_id'),
                    'class_room_id' => $request->post('class_room_id'),
                    'start_date_class' => $request->post('start_date_class'),
                    'end_date_class' => $request->post('end_date_class'),
                    'location' => $request->post('location'),
                    'google_maps' => $request->post('google_maps'),
                    'status' => $request->post('status'),
                    'address' => $request->post('address'),
                    'periode' => $request->post('periode'),
                    'file' => $request->post('file'),
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
                'committee_id' => 'required|string|max:36',
                'class_room_id' => 'required|string|max:36',
                'category_id' => 'required|string|max:36',
                'start_date_class' => 'required|string|max:15',
                'end_date_class' => 'required|string|max:15',
                'location' => 'required|string|max:255',
                'google_maps' => 'required|string|max:20000',
                'status' => 'required|string|max:10',
                'address' => 'required|string|max:20000',
                'periode' => 'required|integer|max:10000',
                'file' => 'required|string|max:20000',
            ];
            $Validatedata = [
                'participant_id' => $request->post('participant_id'),
                'committee_id' => $request->post('committee_id'),
                'category_id' => $request->post('category_id'),
                'class_room_id' => $request->post('class_room_id'),
                'start_date_class' => $request->post('start_date_class'),
                'end_date_class' => $request->post('end_date_class'),
                'location' => $request->post('location'),
                'google_maps' => $request->post('google_maps'),
                'status' => $request->post('status'),
                'address' => $request->post('address'),
                'periode' => $request->post('periode'),
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
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $schedule = Submission::with('participant', 'committee', 'committee.profile.regional')
                                ->where('id',$id)
                                ->get();
        $schedule->map(function ($schedule) {
            $this->fileSettings();
            if (isset($schedule['file'])) {
                $schedule['linkFile'] = $this->getFileAttribute($schedule['file']);
            } else {
                $schedule['linkFile'] = null;
            }
            return $schedule;
        });
        // return $schedule;
        return Inertia::render('Committee/Schedule/DetailSchedule', [
            'schedule' => $schedule,
        ]);
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
            Submission::findOrFail($id)->delete();
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
                Submission::findOrFail($id)->delete();
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
            Submission::where('id', $id)->update([
                'hp' => 'Ditolak',
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

    public function approvalSubmission(Request $request)
    {
        try {
            $id = $request->post('id');
            Submission::where('id', $id)->update([
                'hp' => 'Diterima',
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

    public function graduationSubmission(Request $request)
    {
        try {
            $id = $request->post('id');
            Submission::where('id', $id)->update([
                'hp' => 'Lulus',
                'graduation_date' => Carbon::now(),
            ]);
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function rejectSubmission in SubmissionController', $errors);
        }
    }

    public function optionSubmission(Request $request)
    {
        try {
            $ids = $request->post('id');
            $hp = $request->post('hp');
            foreach($ids as $id) {
                Submission::where('id', $id)->update([
                    'hp' => $hp,
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
}
