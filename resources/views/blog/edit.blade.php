@extends('layouts.app')

@section('content')
<div class="w-4/5 m-auto text-left">
    <div class="py-15">
        <h1 class="text-6xl">
            Update A Post
        </h1>
    </div>
</div>

@if ($errors->any())
    <div class="w-4/5 m-auto">
        <ul>
            @foreach ($errors->all() as $error)
                <li class="w-1/5 mb-4 text-gray-50 bg-red-700 rounded-2xl
                py-4">
                    {{ $error }}
                </li>
            @endforeach
        </ul>
    </div>

@endif

<div class="w-4/5 m-auto p-20">
    <form action="/blog/{{ $post->slug }}"
    method="POST"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <input type="text"
    name="title"
    value="{{ $post->title }}"
    class="bg-transparent black border-b-2 w-full h-20 text-6xl
    outline-none">

        <textarea name="description"
        placeholder="Description..."
        class="py-20 bg-transparent block border-b-2 w-full h-60 text-xl
        outline-none">{{ $post->description }}</textarea>

        <div class="flex bg-gray-lighter pt-15 mt-6">
            <label class="w-44 flex flex-col items-centere px-2 py-3
            bg-white-rounded-lg shadow-lg tracking-wide uppercase border
            border-blue cursor-pointer">
                <span class="mt-2 text-base leadong-normal">
                    Select a file
                </span>
            <input
            type="file"
            name="image"
            class="hidden" value="old('image_path', $post->image_path)">
        </label>
            <img src="{{asset('images/' . $post->image_path)}}" alt="" class="rounded-xl ml-6" width="100">
        </div>

        <div class="mt-15">
            <label class="block mb-2 uppercase font-bold text-xs text-gray-700" for="category_id">
                Category
            </label>

            <select name="category_id" id="category_id">
                @foreach (\App\Models\Category::all() as $category)
                <option value="{{ $category->id }}" {{ old('category_id', $post->category_id) == $category->id ? 'selected' : '' }}>{{ ucwords($category->name) }}</option>
                @endforeach
            </select>

        </div>


        <button
        type="submit"
        class="uppercase mt-15 bg-blue-500 text-gray-100 text-lg
        font-extrabold py-4 px-8 rounded-3xl hover:bg-blue-600">
            Submit A Post
        </button>
    </form>
</div>

@endsection
