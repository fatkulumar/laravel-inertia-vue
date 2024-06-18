<?php

namespace App\Http\Controllers\Committee;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ClassRoom;
use App\Models\Submission;
use App\Models\User;
use App\Traits\EntityValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Traits\FileUpload;

class ScheduleController extends Controller
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
    public function index(Request $request)
    {
        try {
            $schedules = Submission::orderBy('created_at', 'desc')
                ->when($request['search'], function($query, $request) {
                    $query->whereHas('participant', function ($query) use ($request) {
                        $query->where('name', 'like', '%' . $request . '%');
                    });
                })
                ->with('participant', 'committee')
                ->paginate(5)
                ->withQueryString()
                ->appends(['search' => $request['search']]);

            $classRooms = ClassRoom::all(['id', 'name']);
            $categories = Category::all(['id', 'name']);

            $committee = User::with('profile.regional')->where('id', Auth()->user()->id)->first();
            // return $classRooms;
            return Inertia::render('Committee/Schedule/Schedule', [
                'schedules' => $schedules,
                'committee' => $committee,
                'classRooms' => $classRooms,
                'categories' => $categories,
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
        // return $request->post('committee_id');
        try {

            $id = $request->post('id');
            if ($id) {
                $validasiData = $this->updateValidator($request);
                if ($validasiData) return redirect()->back()->withErrors($validasiData)->withInput();

                $submissions = Submission::where('id', $id)->first();
                $file = $request->file('file');
                if($file) {
                    $this->fileSettings();
                    $upload = $this->uploadFile($file);
                }else{
                    $upload = $submissions->file;
                }

                $saveData = [
                    'committee_id' => $request->post('committee_id'),
                    'class_room_id' => $request->post('class_room_id'),
                    'category_id' => $request->post('category_id'),
                    'status' => 'pending',
                    'file' => $upload,
                    'start_date_class' => $request->post('start_date_class'),
                    'end_date_class' => $request->post('end_date_class'),
                    'periode' => $request->post('periode'),
                    'location' => $request->post('location'),
                    'google_maps' => $request->post('google_maps'),
                    'address' => $request->post('address'),
                ];
                $result = $submissions->update($saveData);
                if (!$result) return redirect()->back()->withErrors($result)->withInput();
            } else {
                // return "umar";
                // return $request->all();
                $validasiData = $this->storeValidator($request);
                if ($validasiData) return redirect()->back()->withErrors($validasiData)->withInput();

                $file = $request->file('file');
                if($file) {
                    $this->fileSettings();
                    $upload = $this->uploadFile($file);
                }else{
                    $upload = null;
                }
                $saveData = [
                    'committee_id' => $request->post('committee_id'),
                    'class_room_id' => $request->post('class_room_id'),
                    'category_id' => $request->post('category_id'),
                    'status' => 'pending',
                    'file' => $upload,
                    'start_date_class' => $request->post('start_date_class'),
                    'end_date_class' => $request->post('end_date_class'),
                    'periode' => $request->post('periode'),
                    'location' => $request->post('location'),
                    'google_maps' => $request->post('google_maps'),
                    'address' => $request->post('address'),
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
                'committee_id' => 'required|string|max:36',
                'class_room_id' => 'required|string|max:36',
                'category_id' => 'required|string|max:36',
                'hp' => 'required|string|min:8|max:13',
                'start_date_class' => 'required|string|max:15',
                'end_date_class' => 'required|string|max:15',
                'location' => 'required|string|max:255',
                'google_maps' => 'required|string|max:20000',
                'address' => 'required|string|max:20000',
                'periode' => 'required|string|max:10',
                'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ];
            $Validatedata = [
                'committee_id' => $request->post('committee_id'),
                'class_room_id' => $request->post('class_room_id'),
                'category_id' => $request->post('category_id'),
                'hp' => $request->post('hp'),
                'start_date_class' => $request->post('start_date_class'),
                'end_date_class' => $request->post('end_date_class'),
                'location' => $request->post('location'),
                'google_maps' => $request->post('google_maps'),
                'address' => $request->post('address'),
                'periode' => $request->post('periode'),
                'file' => $request->file('file'),
            ];
            $validator = EntityValidator::validate($Validatedata, $rules);
            if ($validator->fails()) return $validator->errors();
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function storeValidator in ArticleController', $errors);
        }
    }

    private function updateValidator(Request $request)
    {
        try {
            $rules = [
                'committee_id' => 'required|string|max:36',
                'class_room_id' => 'required|string|max:36',
                'category_id' => 'required|string|max:36',
                'hp' => 'required|string|min:8|max:13',
                'start_date_class' => 'required|string|max:15',
                'end_date_class' => 'required|string|max:15',
                'location' => 'required|string|max:255',
                'google_maps' => 'required|string|max:20000',
                'address' => 'required|string|max:20000',
                'periode' => 'required|string|max:10',
                'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ];
            $Validatedata = [
                'committeee_id' => $request->post('committee_id'),
                'class_room_id' => $request->post('class_room_id'),
                'category_id' => $request->post('category_id'),
                'hp' => $request->post('hp'),
                'start_date_class' => $request->post('start_date_class'),
                'end_date_class' => $request->post('end_date_class'),
                'location' => $request->post('location'),
                'goggle_maps' => $request->post('goggle_maps'),
                'address' => $request->post('address'),
                'periode' => $request->post('periode'),
                'file' => $request->file('file'),
            ];
            $validator = EntityValidator::validate($Validatedata, $rules);
            if ($validator->fails()) return $validator->errors();
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function updateValidator in ArticleController', $errors);
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

        $classRooms = ClassRoom::all(['id', 'name']);
        $categories = Category::all(['id', 'name']);
        // return $schedule;
        return Inertia::render('Committee/Schedule/DetailSchedule', [
            'schedule' => $schedule,
            'classRooms' => $classRooms,
            'categories' => $categories,
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

    // public function rejectSubmission(Request $request)
    // {
    //     try {
    //         $id = $request->post('id');
    //         Submission::where('id', $id)->update([
    //             'status' => 'Ditolak',
    //             'approval_date' => Carbon::now(),
    //         ]);
    //     } catch (\Exception $exception) {
    //         $errors['message'] = $exception->getMessage();
    //         $errors['file'] = $exception->getFile();
    //         $errors['line'] = $exception->getLine();
    //         $errors['trace'] = $exception->getTrace();
    //         Log::channel('daily')->info('function rejectSubmission in SubmissionController', $errors);
    //     }
    // }

    // public function approvalSubmission(Request $request)
    // {
    //     try {
    //         $id = $request->post('id');
    //         Submission::where('id', $id)->update([
    //             'status' => 'Diterima',
    //             'approval_date' => Carbon::now(),
    //         ]);
    //     } catch (\Exception $exception) {
    //         $errors['message'] = $exception->getMessage();
    //         $errors['file'] = $exception->getFile();
    //         $errors['line'] = $exception->getLine();
    //         $errors['trace'] = $exception->getTrace();
    //         Log::channel('daily')->info('function rejectSubmission in SubmissionController', $errors);
    //     }
    // }

    // public function graduationSubmission(Request $request)
    // {
    //     try {
    //         $id = $request->post('id');
    //         Submission::where('id', $id)->update([
    //             'status' => 'Lulus',
    //             'graduation_date' => Carbon::now(),
    //         ]);
    //     } catch (\Exception $exception) {
    //         $errors['message'] = $exception->getMessage();
    //         $errors['file'] = $exception->getFile();
    //         $errors['line'] = $exception->getLine();
    //         $errors['trace'] = $exception->getTrace();
    //         Log::channel('daily')->info('function rejectSubmission in SubmissionController', $errors);
    //     }
    // }

    // public function optionSubmission(Request $request)
    // {
    //     try {
    //         $ids = $request->post('id');
    //         $status = $request->post('status');
    //         foreach($ids as $id) {
    //             Submission::where('id', $id)->update([
    //                 'status' => $status,
    //                 'graduation_date' => Carbon::now(),
    //             ]);
    //         }
    //     } catch (\Exception $exception) {
    //         $errors['message'] = $exception->getMessage();
    //         $errors['file'] = $exception->getFile();
    //         $errors['line'] = $exception->getLine();
    //         $errors['trace'] = $exception->getTrace();
    //         Log::channel('daily')->info('function rejectSubmission in SubmissionController', $errors);
    //     }
    // }
}
