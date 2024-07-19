<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Submission;
use App\Traits\EntityValidator;
use App\Traits\FileUpload;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

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
            $submissions = Schedule::orderBy('created_at', 'desc')
                ->when($request['search'], function($query, $request) {
                    $query->whereHas('committee', function ($query) use ($request) {
                        $query->where('name', 'like', '%' . $request . '%');
                    });
                })
                ->with('committee', 'classRoom', 'category', 'regional')
                ->paginate(5)
                ->withQueryString()
                ->appends(['search' => $request['search']]);

            $submissions->map(function ($submission) {
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
            // return $submissions;
            return Inertia::render('Dashboard/Schedule', [
                'submissions' => $submissions,
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

    public function deleteSchedule(Request $request, $id)
    {
        try {
            // return $id;
            // Schedule::findOrFail($id)->delete();
            return Schedule::where('id', $id)->delete();
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
}
