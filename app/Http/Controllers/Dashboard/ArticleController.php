<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Traits\EntityValidator;
use App\Traits\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Response;
use Inertia\Inertia;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    use EntityValidator;
    use FileUpload;

    protected function fileSettings()
    {
        $this->settings = [
            'attributes'  => ['jpeg', 'jpg', 'png'],
            'path'        => 'file/article/',
            'softdelete'  => false
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {

            $articles = Article::orderBy('created_at', 'desc')
                    ->when($request['search'],function($query, $request) {
                        $query->where('title','like','%'.$request.'%');
                    })
                    ->with('category')
                    ->paginate(5)
                    ->withQueryString()
                    ->appends(['search' => $request['search']]);

                    $articles->map(function ($profile) {
                        $this->fileSettings();
                        if (isset($profile['image'])) {
                            $profile['image'] = $this->getFileAttribute($profile['image']);
                        } else {
                            $profile['image'] = null;
                        }
                        return $profile;
                    });
                    // return $articles;
            return Inertia::render('Dashboard/Article', [
                'articles' => $articles,
                'categories' => Category::all(['id', 'name']),
            ]);
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function index in Dashboard/ArticleController', $errors);
        }
    }

    /**
     * Show exceptione form for creating a new resource.
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
            if ($validasiData) return $validasiData;

            $id = $request->post('id');
            $file = $request->file('image');
            if($id) {

                $article = Article::where('id', $id)->first();
                if($file) {
                    $this->fileSettings();
                    if ($article && $article->image) {
                        $this->deleteFile($article->image); // Menghapus file jika ada
                    }
                    $upload = $this->uploadFile($file);
                }else{
                    $upload = $article->image;
                }

                $saveData = [
                    'title' => $request->post('title'),
                    'body' => $request->post('body'),
                    'label' => $request->post('label'),
                    'category_id' => $request->post('category_id'),
                    'slug' => Str::slug($request->post('title')),
                    'image' => $upload,
                    'user_id' => auth()->user()->id,
                ];
                $result = $article->update($saveData);
                if(!isset($result->id)) return redirect()->back()->withErrors($result)->withInput();
            }else{
                if($file) {
                    $this->fileSettings();
                    $upload = $this->uploadFile($file);
                }else{
                    $upload = null;
                }

                $saveData = [
                    'title' => $request->post('title'),
                    'body' => $request->post('body'),
                    'label' => $request->post('label'),
                    'category_id' => $request->post('category_id'),
                    'slug' => Str::slug($request->post('title')),
                    'image' => $upload,
                    'user_id' => auth()->user()->id,
                ];

                $result = Article::create($saveData);
                if(!isset($result->id)) return redirect()->back()->withErrors($result)->withInput();
            }
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function store in ArticleController', $errors);
        }
    }

private function storeValidator(Request $request)
    {
        try {
            $rules = [
                'title' => 'required|string|max:100',
                'body' => 'required',
                'label' => 'nullable|required|string|max:1000',
                'category_id' => 'nullable|required|string|max:50',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ];
            $Validatedata = [
                'title' => $request->post('title'),
                'body' => $request->post('body'),
                'label' => $request->post('label'),
                'category_id' => $request->post('category_id'),
                'image' => $request->post('image'),
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
    public function update(Request $request, string $id)
    {
        //
    }

    public function delete(string $id)
    {
        try {
            Article::find($id)->delete();
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function delete in ArticleController', $errors);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try {
            $ids = $request->post('id');
            foreach($ids as $id) {
                $article = Article::find($id)->delete();
            }
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function delete in ArticleController', $errors);
        }
    }

    public function uploadFileWithCkeditor(Request $request)
    {
        try {
            $request->validate([
                'upload' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $file = $request->file('upload');

            $upload = $this->uploadFile($file);

            // $path = $request->file('upload')->store('images', 'public');

            return response()->json([
                'url' => 'http://localhost:8000/file/article/1KYZsPpZc4ZsrnHoCIwKLwvMYmghJi7n0vx2ZwFM.png-Screenshot%20from%202024-06-07%2016-18-40.png'
            ]);
        } catch (\Exception $exception) {
            $errors['message'] = $exception->getMessage();
            $errors['file'] = $exception->getFile();
            $errors['line'] = $exception->getLine();
            $errors['trace'] = $exception->getTrace();
            Log::channel('daily')->info('function uploadFileWithCkeditor in ArticleController', $errors);
        }
    }
}
