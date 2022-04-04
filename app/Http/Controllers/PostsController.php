<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Post;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Cookie;
class PostsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show', 'search', 'reads']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $posts = Post::latest()->get();

        $categories= Category::all();

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
            'posts' => $posts->paginate(5),
            'categories' => Category::all()
        ]);
    }

    public function category(Category $category)
    {


        return view('blog.index', [
          'posts' => $category->posts()->paginate(5),
           'currentCategory' => $category,
          'categories' => Category::all()
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
            'image' => 'required|mimes:jpg,png,jpeg|max:5048',
            'category_id' => ['required', Rule::exists('categories', 'id')]
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
            'user_id' => auth()->user()->id,
            'category_id' => $request->input('category_id')
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
            'image' => 'mimes:jpg,png,jpeg|max:5048',
            'category_id' => ['required', Rule::exists('categories', 'id')]
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
            'user_id' => auth()->user()->id,
            'category_id' => $request->input('category_id')
        ]);
        }

        if (empty($request['image'])){
            Post::where('slug', $slug)
            ->update([
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'slug' => SlugService::createSlug(Post::class, 'slug',
                $request->title),
                'user_id' => auth()->user()->id,
                'category_id' => $request->input('category_id')
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
