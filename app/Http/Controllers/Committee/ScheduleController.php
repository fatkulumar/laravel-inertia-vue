<?php

namespace App\Http\Controllers\Committee;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ClassRoom;
use App\Models\Letter;
use App\Models\Profile;
use App\Models\Schedule;
use App\Models\Speaker;
use App\Models\TypeActivity;
use App\Models\User;
use App\Traits\EntityValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Traits\FileUpload;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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
            $users = Auth::user();
            $profile = Profile::where('profileable_id', $users->id)->first();
            $schedules = Schedule::orderBy('created_at', 'desc')
                ->when($request['search'], function($query, $request) {
                    $query->whereHas('participant', function ($query) use ($request) {
                        $query->where('name', 'like', '%' . $request . '%');
                    });
                })
                ->with('committee.profile.regional')
                ->where('regional_id', $profile->regional_id)
                ->paginate(5)
                ->withQueryString()
                ->appends(['search' => $request['search']]);

            $classRooms = ClassRoom::all(['id', 'name']);
            $categories = Category::all(['id', 'name']);

            $committee = User::with('profile.regional')->where('id', Auth()->user()->id)->first();
            $committees = User::with('profile.regional', 'chief')->role('panitia')->get();
            $typeActivities = TypeActivity::all(['id', 'name']);
            // return $schedules;

            return Inertia::render('Committee/Schedule/Schedule', [
                'schedules' => $schedules,
                'committee' => $committee,
                'committees' => $committees,
                'classRooms' => $classRooms,
                'categories' => $categories,
                'typeActivities' => $typeActivities,
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
        try {

            $id = $request->post('id');
            if ($id) {
                $validasiData = $this->updateValidator($request);
                if ($validasiData) return redirect()->back()->withErrors($validasiData)->withInput();
                // return $validasiData;

                // return $validasiData;
                $schedule = Schedule::where('id', $id)->first();

                $poster = $request->file('poster');
                if($poster) {
                    $this->fileSettings();
                    $uploadPoster = $this->uploadFile($poster);
                }else{
                    $uploadPoster = $schedule->poster;
                }

                $proposal = $request->file('proposal');
                if($proposal) {
                    $this->fileSettings();
                    $uploadProposal = $this->uploadFile($proposal);
                }else{
                    $uploadProposal = $schedule->proposal;
                }


                $saveData = [
                    'regional_id' => $request->post('regional_id'),
                    'committee_id' => $request->post('committee_id'),
                    'class_room_id' => $request->post('class_room_id'),
                    'category_id' => $request->post('category_id'),
                    'status' => $request->post('status'),
                    'start_date_class' => $request->post('start_date_class'),
                    'end_date_class' => $request->post('end_date_class'),
                    'periode' => $request->post('periode'),
                    'location' => $request->post('location'),
                    'google_maps' => $request->post('google_maps'),
                    'address' => $request->post('address'),
                    'chief_id' => $request->post('chief_id'),
                    'type_activity_id' => $request->post('type_activity_id'), //jenis kegiatan
                    'poster' => $uploadPoster,
                    'concept' => $request->post('concept'), //konsep kegiatan
                    'committee_layout' => $request->post('committee_layout'), //susunan panitia
                    'target_participant' => $request->post('target_participant'), //target peserta
                    'speaker' => $request->post('speaker'), //pemateri
                    'total_activity' => $request->post('total_activity'), // total kegiatan yang sudah dikerjakan
                    'price' => $request->post('price'), // harga
                    'facility' => $request->post('facility'), // fasiliitas
                    'total_rooms_stay' => $request->post('total_rooms_stay'), // jumlah ruang menginap
                    'benefit' => $request->post('benefit'), // jumlah ruang menginap
                    'proposal' => $uploadProposal,
                ];

                $result = $schedule->update($saveData);

                // return $schedule;
                // return $request->post('periode');
                if (!$result) return redirect()->back()->withErrors($result)->withInput();
            } else {
                // return "umar";
                // return $request->all();
                $validasiData = $this->storeValidator($request);
                if ($validasiData) return redirect()->back()->withErrors($validasiData)->withInput();

                $poster = $request->file('poster');
                if($poster) {
                    $this->fileSettings();
                    $uploadPoster = $this->uploadFile($poster);
                }else{
                    $uploadPoster = "Poster Tidak Ada";
                }

                $proposal = $request->file('proposal');
                if($proposal) {
                    $this->fileSettings();
                    $uploadProposal = $this->uploadFile($proposal);
                }else{
                    $uploadProposal = "Proposal Tidak Ada";
                }

                $saveData = [
                    'regional_id' => $request->post('regional_id'),
                    'committee_id' => $request->post('committee_id'),
                    'class_room_id' => $request->post('class_room_id'),
                    'category_id' => $request->post('category_id'),
                    'status' => $request->post('status'),
                    'start_date_class' => $request->post('start_date_class'),
                    'end_date_class' => $request->post('end_date_class'),
                    'periode' => $request->post('periode'),
                    'location' => $request->post('location'),
                    'google_maps' => $request->post('google_maps'),
                    'address' => $request->post('address'),
                    'chief_id' => $request->post('chief_id'),
                    'type_activity_id' => $request->post('type_activity_id'), //jenis kegiatan
                    'poster' => $uploadPoster,
                    'concept' => $request->post('concept'), //konsep kegiatan
                    'committee_layout' => $request->post('committee_layout'), //susunan panitia
                    'target_participant' => $request->post('target_participant'), //target peserta
                    'speaker' => $request->post('speaker'), //pemateri
                    'total_activity' => $request->post('total_activity'), // total kegiatan yang sudah dikerjakan
                    'price' => $request->post('price'), // harga
                    'facility' => $request->post('facility'), // fasiliitas
                    'total_rooms_stay' => $request->post('total_rooms_stay'), // jumlah ruang menginap
                    'benefit' => $request->post('benefit'), // jumlah ruang menginap
                    'proposal' => $uploadProposal,
                ];

                $result = Schedule::create($saveData);

                Letter::create([
                   'schedule_id' => $result->id
                ]);

                if (!isset($result->id)) return redirect()->back()->withErrors($result)->withInput();
            }
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function store in Committee/ScheduleController', $errors);
        }
    }

    private function storeValidator(Request $request)
    {
        try {
            $rules = [
                'regional_id' => 'required|string|max:36',
                'committee_id' => 'required|string|max:36',
                'class_room_id' => 'required|string|max:36',
                'category_id' => 'required|string|max:36',
                'start_date_class' => 'required|string|max:15',
                'end_date_class' => 'required|string|max:15',
                'location' => 'required|string|max:255',
                'google_maps' => 'required|string|max:10000',
                'address' => 'required|string|max:2000',
                'periode' => 'required|integer',
                'poster' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'status' => 'required|string|max:10',
                'chief_id' => 'required|string|max:36',
                'type_activity_id' => 'required|string|max:36',
                'concept' => 'required|string',
                'committee_layout' => 'required|string',
                'target_participant' => 'required|string',
                'speaker' => 'required|string|max:36',
                'total_activity' => 'required|integer',
                'price' => 'required|integer',
                'facility' => 'required|string|max:20000',
                'total_rooms_stay' => 'required|integer',
                'benefit' => 'required|string|max:20000',
                'proposal' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

            ];
            $Validatedata = [
                'regional_id' => $request->post('regional_id'),
                'committee_id' => $request->post('committee_id'),
                'class_room_id' => $request->post('class_room_id'),
                'category_id' => $request->post('category_id'),
                'status' => $request->post('status'),
                'start_date_class' => $request->post('start_date_class'),
                'end_date_class' => $request->post('end_date_class'),
                'periode' => $request->post('periode'),
                'location' => $request->post('location'),
                'google_maps' => $request->post('google_maps'),
                'address' => $request->post('address'),
                'chief_id' => $request->post('chief_id'),
                'type_activity_id' => $request->post('type_activity_id'),
                'poster' => $request->file('poster'),
                'concept' => $request->post('concept'),
                'committee_layout' => $request->post('committee_layout'),
                'target_participant' => $request->post('target_participant'),
                'speaker' => $request->post('speaker'),
                'total_activity' => $request->post('total_activity'),
                'price' => $request->post('price'),
                'facility' => $request->post('facility'),
                'total_rooms_stay' => $request->post('total_rooms_stay'),
                'benefit' => $request->post('benefit'),
                'proposal' => $request->file('proposal'),
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

    private function updateValidator(Request $request)
    {
        try {
            $rules = [
                'regional_id' => 'required|string|max:36',
                'committee_id' => 'required|string|max:36',
                'class_room_id' => 'required|string|max:36',
                'category_id' => 'required|string|max:36',
                'start_date_class' => 'required|string|max:15',
                'end_date_class' => 'required|string|max:15',
                'location' => 'required|string|max:255',
                'google_maps' => 'required|string|max:10000',
                'address' => 'required|string|max:2000',
                'periode' => 'required|integer',
                'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'status' => 'required|string|max:10',
                'chief_id' => 'required|string|max:36',
                'type_activity_id' => 'required|string|max:36',
                'concept' => 'required|string',
                'committee_layout' => 'required|string',
                'target_participant' => 'required|string',
                'speaker' => 'required|string|max:36',
                'total_activity' => 'required|integer',
                'price' => 'required|integer',
                'facility' => 'required|string|max:20000',
                'total_rooms_stay' => 'required|integer',
                'benefit' => 'required|string|max:20000',
                'proposal' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

            ];
            $Validatedata = [
                'regional_id' => $request->post('regional_id'),
                'committee_id' => $request->post('committee_id'),
                'class_room_id' => $request->post('class_room_id'),
                'category_id' => $request->post('category_id'),
                'status' => $request->post('status'),
                'start_date_class' => $request->post('start_date_class'),
                'end_date_class' => $request->post('end_date_class'),
                'periode' => $request->post('periode'),
                'location' => $request->post('location'),
                'google_maps' => $request->post('google_maps'),
                'address' => $request->post('address'),
                'chief_id' => $request->post('chief_id'),
                'type_activity_id' => $request->post('type_activity_id'),
                'poster' => $request->file('poster'),
                'concept' => $request->post('concept'),
                'committee_layout' => $request->post('committee_layout'),
                'target_participant' => $request->post('target_participant'),
                'speaker' => $request->post('speaker'),
                'total_activity' => $request->post('total_activity'),
                'price' => $request->post('price'),
                'facility' => $request->post('facility'),
                'total_rooms_stay' => $request->post('total_rooms_stay'),
                'benefit' => $request->post('benefit'),
                'proposal' => $request->file('proposal'),
            ];
            $validator = EntityValidator::validate($Validatedata, $rules);
            if ($validator->fails()) return $validator->errors();
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function updateValidator in Committee/ScheduleController', $errors);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $schedule = Schedule::with('participant', 'committee', 'committee.profile.regional', 'chief.profile', 'speaker')
                                ->where('id',$id)
                                ->get();

        $schedule->map(function ($schedule) {
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
            $schedule->formatted_created_at = Carbon::parse($schedule->created_at)->format('d-m-Y');
            $schedule->formatted_date_overview = $schedule->date_overview ? Carbon::parse($schedule->date_overview)->format('d-m-Y') : '';
            $schedule->formatted_date_received = $schedule->date_received ? Carbon::parse($schedule->date_received)->format('d-m-Y') : '';
            $schedule->formatted_date_done = $schedule->date_done ? Carbon::parse($schedule->date_done)->format('d-m-Y') : '';
            return $schedule;
        });

        // return $schedule;

        $classRooms = ClassRoom::all(['id', 'name']);
        $categories = Category::all(['id', 'name']);
        $chiefs = User::with('profile.regional')->role('panitia')->get();
        $typeActivities = TypeActivity::all(['id', 'name']);
        // return $chiefs;
        return Inertia::render('Committee/Schedule/DetailSchedule', [
            'schedule' => $schedule,
            'classRooms' => $classRooms,
            'categories' => $categories,
            'chiefs' => $chiefs,
            'typeActivities' => $typeActivities,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    // public function edit(string $id)
    // {
    //     //
    // }

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
            Log::channel('daily')->info('function delete in ScheduleController', $errors);
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
            Log::channel('daily')->info('function delete in ScheduleController', $errors);
        }
    }

    public function speaker($classRoomId)
    {
        try {
            $speakers = Speaker::where('class_room_id', $classRoomId)->get();
            return response()->json($speakers);
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function speaker in SchedlueController', $errors);
        }
    }

    public function speakerList(Request $request, $scheduleId)
    {
        try {
            $speakers = Speaker::whereHas('schedule', function ($query) use ($scheduleId) {
                $query->where('id', $scheduleId);
            })
            ->when($request['search'], function($query, $request) {
                $query->whereHas('participant', function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request . '%');
                });
            })
            ->with('schedule.speaker', 'province', 'city', 'classRoom', 'city', 'category')
            ->paginate(10);

            // return $speakers;
            return Inertia::render('Committee/Schedule/listSpeaker', [
                'speakers' => $speakers,
            ]);
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function speakerList in SchedlueController', $errors);
        }
    }

    public function letter(Request $request, $scheduleId)
    {
        try {
            $letters = Letter::whereHas('schedule', function($query) use ($scheduleId) {
                $query->where('id', $scheduleId);
            })->with('schedule')->paginate(10);

            // Map over the paginated results to modify each letter
            $letters->getCollection()->transform(function ($letter) {
                if ($letter->file) {
                    $this->fileSettings();
                    $letter->link_file = $this->getFileAttribute($letter->file);
                }
                return $letter;
            });

            // return $letters;
            return Inertia::render('Committee/Schedule/letter', [
                'letters' => $letters,
            ]);
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function speakerList in SchedlueController', $errors);
        }
    }

    public function uploadLetter(Request $request, $scheduleId)
    {
        try {
            if($request->id) {
                $validasiData = $this->updateLetterValidator($request);
                $letter =  Letter::where('schedule_id', $scheduleId)->first();
                $file = $request->file('file');
                if($file) {
                    $this->fileSettings();
                    $uploadFile = $this->uploadFile($file);
                }else{
                    $uploadFile = $letter->file;
                }

                $result = Letter::where('schedule_id', $scheduleId)->update([
                    'file' => $uploadFile,
                    'name' => $request->post('name'),
                ]);
                if (!isset($result->id)) return redirect()->back()->withErrors($result)->withInput();
            }else{
                $validasiData = $this->storeLetterValidator($request);
                if ($validasiData) return redirect()->back()->withErrors($validasiData)->withInput();
                $file = $request->file('file');
                if($file) {
                    $this->fileSettings();
                    $uploadFile = $this->uploadFile($file);
                }else{
                    $uploadFile = '-';
                }

                $saveData = [
                    'schedule_id' => $request->post('schedule_id'),
                    'file' => $uploadFile,
                    'name' => $request->post('name'),
                ];

                $result = Letter::create($saveData);
                if (!isset($result->id)) return redirect()->back()->withErrors($result)->withInput();
            }

        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function uploadLetter in SchedlueController', $errors);
        }
    }

    private function updateLetterValidator(Request $request)
    {
        try {
            $rules = [
                'schedule_id' => 'required|string|max:36',
                'name' => 'required|string|max:1000',
                'file' => 'nullable|mimes:pdf|max:2048',

            ];
            $Validatedata = [
                'schedule_id' => $request->post('regional_id'),
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
            Log::channel('daily')->info('function updateLetterValidator in Committee/ScheduleController', $errors);
        }
    }

    private function storeLetterValidator(Request $request)
    {
        try {
            $rules = [
                'schedule_id' => 'required|string|max:36',
                'name' => 'required|string|max:36',
                'file' => 'required|mimes:pdf|max:2048',

            ];
            $Validatedata = [
                'schedule_id' => $request->post('regional_id'),
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
            Log::channel('daily')->info('function storeLetterValidator in Committee/ScheduleController', $errors);
        }
    }

    public function deleteLetter(Request $request, $scheduleId)
    {
        try {
            $letter = Letter::where('schedule_id', $scheduleId)->first();
            $this->fileSettings();
            if (isset($letter->file)) {
                $this->deleteFile($letter->file);
            }
            $letter->delete();
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function uploadLetter in SchedlueController', $errors);
        }
    }

    // public function rejectSubmission(Request $request)
    // {
    //     try {
    //         $id = $request->post('id');
    //         Schedule::where('id', $id)->update([
    //             'status' => 'Ditolak',
    //             'approval_date' => Carbon::now(),
    //         ]);
    //     } catch (\Exception $exception) {
    //         $errors['message'] = $exception->getMessage();
    //         $errors['file'] = $exception->getFile();
    //         $errors['line'] = $exception->getLine();
    //         $errors['trace'] = $exception->getTrace();
    //         Log::channel('daily')->info('function rejectSubmission in ScheduleController', $errors);
    //     }
    // }

    // public function approvalSubmission(Request $request)
    // {
    //     try {
    //         $id = $request->post('id');
    //         Schedule::where('id', $id)->update([
    //             'status' => 'Diterima',
    //             'approval_date' => Carbon::now(),
    //         ]);
    //     } catch (\Exception $exception) {
    //         $errors['message'] = $exception->getMessage();
    //         $errors['file'] = $exception->getFile();
    //         $errors['line'] = $exception->getLine();
    //         $errors['trace'] = $exception->getTrace();
    //         Log::channel('daily')->info('function rejectSubmission in ScheduleController', $errors);
    //     }
    // }

    // public function graduationSubmission(Request $request)
    // {
    //     try {
    //         $id = $request->post('id');
    //         Schedule::where('id', $id)->update([
    //             'status' => 'Lulus',
    //             'graduation_date' => Carbon::now(),
    //         ]);
    //     } catch (\Exception $exception) {
    //         $errors['message'] = $exception->getMessage();
    //         $errors['file'] = $exception->getFile();
    //         $errors['line'] = $exception->getLine();
    //         $errors['trace'] = $exception->getTrace();
    //         Log::channel('daily')->info('function rejectSubmission in ScheduleController', $errors);
    //     }
    // }

    // public function optionSubmission(Request $request)
    // {
    //     try {
    //         $ids = $request->post('id');
    //         $status = $request->post('status');
    //         foreach($ids as $id) {
    //             Schedule::where('id', $id)->update([
    //                 'status' => $status,
    //                 'graduation_date' => Carbon::now(),
    //             ]);
    //         }
    //     } catch (\Exception $exception) {
    //         $errors['message'] = $exception->getMessage();
    //         $errors['file'] = $exception->getFile();
    //         $errors['line'] = $exception->getLine();
    //         $errors['trace'] = $exception->getTrace();
    //         Log::channel('daily')->info('function rejectSubmission in ScheduleController', $errors);
    //     }
    // }
}
