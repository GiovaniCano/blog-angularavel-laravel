<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class PostsController extends Controller
{
    function __construct()
    {
        $this->middleware(['auth:sanctum', 'verified'])->except('index', 'show', 'getImage', 'filterByCategory');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // return Post::all()->load(['category', 'user']);
        return Post::with(['category', 'user'])->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|integer|min:1|exists:categories,id',
            'title' => 'required|string|min:2|max:50',
            'content' => 'required|string|min:100|max:65535',
            'image' => 'required|mimes:jpg,jpeg,webp,png|max:2000', // 2000kb = 2mb
        ]);

        $image_name = time() .'-'. uniqid() . '.webp';

        $post = Post::create([
            'user_id' => $request->user()->id,
            'category_id' => +$request->category_id,
            'title' => $request->title,
            'content' => $request->content,
            'image' => $image_name,
        ]);
        $post->save();

        $img = Image::make($request->image);
        $img->fit(1280, 720);
        $img->encode('webp');
        Storage::disk('posts')->put($image_name, $img);

        return response($post->load(['category', 'user']), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Post::findOrFail($id)->load(['category', 'user']);
    }

    public function getImage($image) {
        if(Storage::disk('posts')->exists($image)) {
            return Storage::disk('posts')->get($image);
        } else {
            return response(null, 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $post = Post::where(['id'=>$id, 'user_id'=>auth()->id()])->firstOrFail();
        $request->validate([
            'category_id' => 'required|integer|min:1|exists:categories,id',
            'title' => 'required|string|min:2|max:50',
            'content' => 'required|string|min:100|max:65535',
            'image' => 'mimes:jpg,jpeg,webp,png|max:2000', // 2000kb = 2mb
        ]);

        if($request->image) {
            if($post->image){
                Storage::disk('posts')->delete($post->image);
            }

            $image_name = time() .'-'. uniqid() . '.webp';

            $post->image = $image_name; //update
        }

        $post->update([
            'category_id' => +$request->category_id,
            'title' => $request->title,
            'content' => $request->content,
            // 'image',
        ]);

        if($request->image) {
            $img = Image::make($request->image);
            $img->fit(1280, 720);
            $img->encode('webp');
            Storage::disk('posts')->put($image_name, $img);    
        }

        return response($post->load(['category', 'user']), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::where(['id'=>$id, 'user_id'=>auth()->id()])->firstOrFail();
        $post->delete();
    }

    function filterByCategory($id) {
        $category = Category::findOrFail($id);
        return $category->load(['posts.category', 'posts.user']);
    }
}
