<?php

namespace App\Http\Controllers\Committee;

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
use App\Models\Category;
use App\Models\ClassRoom;
use App\Models\Documentation;
use App\Models\Letter;
use App\Models\Profile;
use App\Models\RegencyRegional;
use App\Models\Schedule;
use App\Models\Speaker;
use App\Models\Submission;
use App\Models\TypeActivity;
use App\Models\User;
use App\Traits\EntityValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Traits\FileUpload;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            $users = Auth::user();
            $profile = Profile::where('profileable_id', $users->id)->first();
            $schedules = Schedule::orderBy('created_at', 'desc')
                ->when($request['search'], function ($query, $request) {
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
            $regency_regional_id = Auth::user()->profile->regency_regional_id;
            $regencyRegional = RegencyRegional::with('regional')
                ->where('id', $regency_regional_id)
                ->get();
            $regencyRegionals = RegencyRegional::with('regional')
                ->get();
            // return $regencyRegional;

            return Inertia::render('Committee/Schedule/Schedule', [
                'schedules' => $schedules,
                'committee' => $committee,
                'committees' => $committees,
                'classRooms' => $classRooms,
                'categories' => $categories,
                'typeActivities' => $typeActivities,
                'regencyRegionals' => $regencyRegionals,
                'regencyRegional' => $regencyRegional,
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $id = $request->post('id');
            $regionalIds = array();
            $jsonRegencyRegionalIds = $request->post('regency_regional_ids');
            // return $request;
            if($jsonRegencyRegionalIds) {
                if (json_last_error() === JSON_ERROR_NONE) {
                    // Proses setiap elemen dalam array menggunakan foreach
                    foreach ($jsonRegencyRegionalIds as $item) {
                        // return "Name: " . $item['name'] . ", ID: " . $item['id'] . "\n";
                        $regionalIds[] = $item['id'];
                    }
                } else {
                    return "Error decoding JSON: " . json_last_error_msg();
                }
            }
            // return $regionalIds;
            if ($id) {
                $validasiData = $this->updateValidator($request);
                if ($validasiData) return redirect()->back()->withErrors($validasiData)->withInput();
                // return $validasiData;

                // return $validasiData;
                $schedule = Schedule::where('id', $id)->first();

                $poster = $request->file('poster');
                if ($poster) {
                    $this->fileSettings();
                    $uploadPoster = $this->uploadFile($poster);
                } else {
                    $uploadPoster = $schedule->poster;
                }

                $proposal = $request->file('proposal');
                if ($proposal) {
                    $this->fileSettings();
                    $uploadProposal = $this->uploadFile($proposal);
                } else {
                    $uploadProposal = $schedule->proposal;
                }


                $saveData = [
                    'regional_id' => $request->post('regional_id'),
                    'committee_id' => $request->post('committee_id'),
                    'class_room_id' => $request->post('class_room_id'),
                    'category_id' => $request->post('category_id'),
                    'regency_regional_id' => $request->post('category_id'),
                    'regency_regional_ids' => json_encode($regionalIds),
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
                if (!$result) return redirect()->back()->withErrors($result)->withInput();
            } else {
                // return $request->all();
                $validasiData = $this->storeValidator($request);
                if ($validasiData) return redirect()->back()->withErrors($validasiData)->withInput();

                $poster = $request->file('poster');
                if ($poster) {
                    $this->fileSettings();
                    $uploadPoster = $this->uploadFile($poster);
                } else {
                    $uploadPoster = "Poster Tidak Ada";
                }

                $proposal = $request->file('proposal');
                if ($proposal) {
                    $this->fileSettings();
                    $uploadProposal = $this->uploadFile($proposal);
                } else {
                    $uploadProposal = "Proposal Tidak Ada";
                }

                $saveData = [
                    'regional_id' => $request->post('regional_id'),
                    'committee_id' => $request->post('committee_id'),
                    'class_room_id' => $request->post('class_room_id'),
                    'category_id' => $request->post('category_id'),
                    // 'regency_regional_id' => $request->post('regency_regional_id'),
                    'regency_regional_ids' => json_encode($regionalIds),
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
                    'speaker_id' => $request->post('speaker_id'), //pemateri
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
                // 'regency_regional_id' => 'required|string|max:36',
                'regency_regional_ids' => 'nullable|array',
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
                'speaker_id' => 'required|string|max:36',
                'total_activity' => 'required|integer',
                'price' => 'required|integer',
                'facility' => 'required|string|max:20000',
                'total_rooms_stay' => 'required|integer',
                'benefit' => 'required|string|max:20000',
                'proposal' => 'required|mimes:pdf|max:2048',

            ];
            $Validatedata = [
                'regional_id' => $request->post('regional_id'),
                'committee_id' => $request->post('committee_id'),
                'class_room_id' => $request->post('class_room_id'),
                'category_id' => $request->post('category_id'),
                // 'regency_regional_id' => $request->post('regency_regional_id'),
                'regency_regional_ids' => $request->post('regency_regional_ids'),
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
                'speaker_id' => $request->post('speaker_id'),
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
                // 'regency_regional_id' => 'required|string|max:36',
                'regency_regional_ids' => 'nullable|array',
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
                'speaker_id' => 'required|string|max:36',
                'total_activity' => 'required|integer',
                'price' => 'required|integer',
                'facility' => 'required|string|max:20000',
                'total_rooms_stay' => 'required|integer',
                'benefit' => 'required|string|max:20000',
                'proposal' => 'nullable|mimes:pdf|max:2048',
            ];
            $Validatedata = [
                'regional_id' => $request->post('regional_id'),
                'committee_id' => $request->post('committee_id'),
                'class_room_id' => $request->post('class_room_id'),
                'category_id' => $request->post('category_id'),
                // 'regency_regional_id' => $request->post('regency_regional_id'),
                'regency_regional_ids' => $request->post('regency_regional_ids'),
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
                'speaker_id' => $request->post('speaker_id'),
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
        $schedule = Schedule::with('committee', 'committee.profile.regional', 'chief.profile', 'speaker', 'letter')
            ->where('id', $id)
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
        $regency_regional_id = Auth::user()->profile->regency_regional_id;
        $regencyRegional = RegencyRegional::with('regional')
            ->where('id', $regency_regional_id)
            ->get();
        $regencyRegionals = RegencyRegional::with('regional')
            ->get();
        return Inertia::render('Committee/Schedule/DetailSchedule', [
            'schedule' => $schedule,
            'classRooms' => $classRooms,
            'categories' => $categories,
            'chiefs' => $chiefs,
            'typeActivities' => $typeActivities,
            'regencyRegionals' => $regencyRegionals,
            'regencyRegional' => $regencyRegional,
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
            DB::transaction(function () use ($id) {
                $schedule = Schedule::with('letter')->findOrFail($id);
                if ($schedule->letter && $schedule->letter->file && $schedule->poster) {
                    $this->fileSettings();
                    $this->deleteFile($schedule->letter->file);
                    $this->deleteFile($schedule->poster);
                }
                $schedule->letter->delete();
                $schedule->delete();
            });
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
            DB::transaction(function () use ($ids) {
                foreach ($ids as $id) {
                    $schedule = Schedule::with('letter')->findOrFail($id);
                    if ($schedule->letter && $schedule->letter->file && $schedule->poster) {
                        $this->fileSettings();
                        $this->deleteFile($schedule->letter->file);
                        $this->deleteFile($schedule->poster);
                    }
                    $schedule->letter->delete();
                    $schedule->delete();
                }
            });
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
                ->when($request['search'], function ($query, $request) {
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
            $letters = Letter::whereHas('schedule', function ($query) use ($scheduleId) {
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
            if (Letter::where('schedule_id', $scheduleId)->exists()) {
                $validasiData = $this->updateLetterValidator($request, $scheduleId);
                $letter =  Letter::where('schedule_id', $scheduleId)->first();
                $file = $request->file('file');
                if ($file) {
                    $this->fileSettings();
                    $uploadFile = $this->uploadFile($file);
                } else {
                    $uploadFile = $letter->file;
                }

                $result = Letter::where('schedule_id', $scheduleId)->update([
                    'file' => $uploadFile,
                    'name' => $request->post('name'),
                ]);
                if (!isset($result->id)) return redirect()->back()->withErrors($result)->withInput();
            } else {
                $validasiData = $this->storeLetterValidator($request, $scheduleId);
                if ($validasiData) return redirect()->back()->withErrors($validasiData)->withInput();
                $file = $request->file('file');
                if ($file) {
                    $this->fileSettings();
                    $uploadFile = $this->uploadFile($file);
                } else {
                    $uploadFile = '-';
                }

                $saveData = [
                    'schedule_id' => $scheduleId,
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

    private function updateLetterValidator(Request $request, $scehduleId)
    {
        try {
            $rules = [
                'schedule_id' => 'required|string|max:36',
                'name' => 'required|string|max:1000',
                'file' => 'nullable|mimes:pdf|max:2048',

            ];
            $Validatedata = [
                'schedule_id' => $scehduleId,
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

    private function storeLetterValidator(Request $request, $scheduleId)
    {
        try {
            $rules = [
                'schedule_id' => 'required|string|max:36',
                'name' => 'required|string|max:36',
                'file' => 'required|mimes:pdf|max:2048',

            ];
            $Validatedata = [
                'schedule_id' => $scheduleId,
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
            Log::channel('daily')->info('function deleteLetter in SchedlueController', $errors);
        }
    }

    public function participant(Request $request, $scheduleId)
    {
        try {
            $submissions = Submission::orderBy('created_at', 'desc')
                ->when($request['search'], function ($query, $request) {
                    $query->whereHas('participant', function ($query) use ($request) {
                        $query->where('name', 'like', '%' . $request . '%');
                    });
                })
                ->where('schedule_id', $scheduleId)
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
            return Inertia::render('Committee/Schedule/Participant', [
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

    public function documentation(Request $request, $scheduleId)
    {
        try {
            $documentations = Documentation::where('schedule_id', $scheduleId)
                ->with('schedule')
                ->get();

            $documentations->map(function ($documentation) {
                $this->fileSettings();
                if (isset($documentation['image'])) {
                    $documentation['image'] = $this->getFileAttribute($documentation['image']);
                } else {
                    $documentation['link_image'] = null;
                }
                return $documentation;
            });
            // return $documentations;

            return Inertia::render('Committee/Schedule/Documentation', [
                'documentations' => $documentations,
            ]);
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function documentation in SchedlueController', $errors);
        }
    }

    public function documentationStore(Request $request)
    {
        try {

            $id = $request->post('id');
            if ($id) {
                $validasiData = $this->updateDocumetationValidator($request);
                if ($validasiData) return redirect()->back()->withErrors($validasiData)->withInput();
                // return $validasiData;
                $documentation = Documentation::where('id', $id)->first();

                $image = $request->file('image');
                if ($image) {
                    $this->fileSettings();
                    $uploadImage = $this->uploadFile($image);
                } else {
                    $uploadImage = $documentation->image;
                }

                $saveData = [
                    'schedule_id' => $request->post('schedule_id'),
                    'title' => $request->post('title'),
                    'description' => $request->post('description'),
                    'image' => $uploadImage,
                ];

                $result = $documentation->update($saveData);

                // return $schedule;
                if (!$result) return redirect()->back()->withErrors($result)->withInput();
            } else {
                // return $request->all();
                $validasiData = $this->storeDocumentationValidator($request);
                if ($validasiData) return redirect()->back()->withErrors($validasiData)->withInput();

                $image = $request->file('image');
                if ($image) {
                    $this->fileSettings();
                    $uploadImage = $this->uploadFile($image);
                } else {
                    $uploadImage = "Image Tidak Ada";
                }

                $saveData = [
                    'schedule_id' => $request->post('schedule_id'),
                    'title' => $request->post('title'),
                    'description' => $request->post('description'),
                    'image' => $uploadImage,
                ];

                $result = Documentation::create($saveData);

                if (!isset($result->id)) return redirect()->back()->withErrors($result)->withInput();
            }
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function documentationStore in Committee/ScheduleController', $errors);
        }
    }

    private function updateDocumetationValidator(Request $request)
    {
        try {
            $rules = [
                'schedule_id' => 'required|string|max:36',
                'title' => 'required|string|max:36',
                'description' => 'required|string|max:5000',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

            ];
            $Validatedata = [
                'schedule_id' => $request->post('schedule_id'),
                'title' => $request->post('title'),
                'description' => $request->post('description'),
                'image' => $request->file('image'),
            ];
            $validator = EntityValidator::validate($Validatedata, $rules);
            if ($validator->fails()) return $validator->errors();
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function updateDocumetationValidator in Committee/ScheduleController', $errors);
        }
    }

    private function storeDocumentationValidator(Request $request)
    {
        try {
            $rules = [
                'schedule_id' => 'required|string|max:36',
                'title' => 'required|string|max:36',
                'description' => 'required|string|max:5000',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

            ];
            $Validatedata = [
                'schedule_id' => $request->post('schedule_id'),
                'title' => $request->post('title'),
                'description' => $request->post('description'),
                'image' => $request->file('image'),
            ];
            $validator = EntityValidator::validate($Validatedata, $rules);
            if ($validator->fails()) return $validator->errors();
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function storeDocumetationValidator in Committee/ScheduleController', $errors);
        }
    }

    public function documentationDelete(Request $request)
    {
        try {
            $id = $request->post('id');
            $documentation = Documentation::findOrFail($id);
            $this->fileSettings();
            if (isset($documentation['image'])) {
                $this->deleteFile($documentation['image']);
            }
            $documentation->delete();
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function deleteDocumetation in Committee/ScheduleController', $errors);
        }
    }

    public function report(Request $reques, $scheduleId)
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

            return Inertia::render('Committee/Schedule/Report', [
                'reports' => $reports,
                'appointmentFiles' => $appointmentFiles,
            ]);
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function report in Committee/ScheduleController', $errors);
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
            Log::channel('daily')->info('function updateAppointmentFileValidator in Committee/ScheduleController', $errors);
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
            Log::channel('daily')->info('function storeAppointmentFileValidator in Committee/ScheduleController', $errors);
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

    public function committee($userId)
    {
        try {
            $users = User::with('profile.regional')->where('id', $userId)->first();
            return response()->json($users);
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function speaker in SchedlueController', $errors);
        }
    }
}
