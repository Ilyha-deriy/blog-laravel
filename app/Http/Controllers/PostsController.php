<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Support\Facades\Cookie;
class PostsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show', 'search', 'read']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $posts = Post::all();

        return view('blog.index')
        ->with('posts', Post::orderBy('updated_at', 'DESC')->paginate(5));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('blog.create');
    }

    public function read(Post $posts)
    {
		$posts->incrementReadCount();

        return view('post.show', compact($posts));
    }

    public function search()
    {
        $posts= Post::latest();

        if (request('search')) {
            $posts
            ->where('title', 'like', '%' . request('search') . '%')
            ->orWhere('description', 'like', '%' . request('search') . '%');
        }

        return view('blog.index', [
            'posts' => $posts->paginate(5)
        ]);
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
            'title' => 'required',
            'description' => 'required',
            'image' => 'required|mimes:jpg,png,jpeg|max:5048'
        ]);

        $newImageName= uniqid() . '-' . $request->title . '.' .
        $request->image->extension();

        $request->image->move(public_path('images'), $newImageName);


        Post::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'slug' => SlugService::createSlug(Post::class, 'slug',
            $request->title),
            'image_path' => $newImageName,
            'user_id' => auth()->user()->id
        ]);

        return redirect('/blog')
        ->with('message', 'Post has been
        added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {

        $post = Post::where('slug', $slug)->first();
        $post->increment('reads');
        $post->save();
        return view('blog.show', compact('post'));
        //return view('blog.show')
        //->with('post', Post::where('slug', $slug)->first());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $string
     * @return \Illuminate\Http\Response
     */
    public function edit($slug)
    {
        return view('blog.edit')
        ->with('post', Post::where('slug', $slug)->first());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $slug
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slug)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'image' => 'mimes:jpg,png,jpeg|max:5048'
        ]);

        if (isset($request['image'])) {
            $newImageName= uniqid() . '-' . $request->title . '.' .
            $request->image->extension();

            $request->image->move(public_path('images'), $newImageName);

            Post::where('slug', $slug)
            ->update([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'slug' => SlugService::createSlug(Post::class, 'slug',
            $request->title),
            'image_path' => $newImageName,
            'user_id' => auth()->user()->id
        ]);
        }

        if (empty($request['image'])){
            Post::where('slug', $slug)
            ->update([
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'slug' => SlugService::createSlug(Post::class, 'slug',
                $request->title),
                'user_id' => auth()->user()->id
            ]);
        }



        return redirect('/blog')
        ->with('message', 'Post has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug)
    {
        $post= Post::where('slug', $slug);
        $post->delete();

        return redirect('/blog')
        ->with('message', 'Post has been deleted!');

    }
}
