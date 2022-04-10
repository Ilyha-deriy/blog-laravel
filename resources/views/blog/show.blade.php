@extends('layouts.app')

@section('content')
<div class="w-4/5 m-auto text-left">
    <div class="py-15">
        <h1 class="text-6xl">
            {{ $post->title }}
        </h1>
    </div>
</div>

<div class="w-4/5 m-auto p-20">
    <div>
        <img src="{{asset('images/' . $post->image_path)}}" alt="" class="mx-auto mb-20 rounded-xl" width="700">
    </div>
    <span class="text-gray-500">
            By <span class="font-bold italic text-gray-800">
            {{ $post->user->name }}</span>, Created on {{ date('jS M Y',
            strtotime($post->updated_at)) }}
    </span>

    <p class="text-xl text-gray-700 pt-8 pb-10 leading-8 font-light">
        {{ $post->description }}
    </p>

    <section class="col-span-8 col-start-5 mt-10 space-y-6">

        @auth

        <form method="POST" action="/blog/{{ $post->slug }}/comments" class="border border-gray-200 p-6 rounded-xl">
            @csrf

            <header class="flex items-center">
                <img src="{{asset('images/avatars/'.Auth::user()->image)}}" alt="" class="inline object-cover w-16 h-16 mr-2 rounded-full">

                <h2 class="ml-4">Want to participate?</h2>
            </header>

            <div class="mt-6">
                <textarea name="body" class="w-full text-sm focus:outline-none focus:ring" rows="5" placeholder="Please, share your thougts" required></textarea>

                @error('body')
                    <span class="text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex justify-end mt-6 pt-6 border-t border-gray-200">
                <button type="submit" class="bg-blue-500 text-white uppercase font-semibold text-xs py-2 px-10 rounded-2xl hover:bg-blue-600">
                    Post
                </button>
            </div>
        </form>
        @else
            <p class="font-semibold">
                <a href="/register" class="hover:underline">Register </a>or <a href="/login" class="hover:underline">log in</a> leave a comment.
            </p>
        @endauth

        @foreach ($post->comments as $comment)
        @if (is_null($comment->parent_id))


        <article class="flex bg-gray-100 border border-gray-200 p-6 rounded-xl space-x-4">
            <div class="flex-shrink-0">
                <img src="{{asset('images/avatars/'. $comment->author->image)}}" alt="" class="inline object-cover w-16 h-16 mr-2 rounded-full">
            </div>

            <div>
                <header class="mb-4">
                    <h3 class="font-bold">{{ $comment->author->name }}</h3>

                <p class="text-xs">
                    Posted
                    <time>{{ $comment->created_at->format('F j, Y, g:i a') }}</time>
                </p>
                </header>

                <p>
                    {{ $comment->body }}
                </p>

            </div>

        </article>

        @auth
        <form  method="POST" action="/blog/{{ $post->slug }}/comments" class="border border-y-4 mb-10 border-gray-200 ml-40 p-6 rounded-xl">
            @csrf

            <div>
                <textarea name="body" class="w-full text-sm focus:outline-none focus:ring" rows="2" placeholder="Please, share your thougts" required></textarea>
                <input type="hidden" name="comment_id" value="{{ $comment->id }}" />

                @error('body')
                    <span class="text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex justify-end pt-6  border-t mt-10 border-gray-200">
                <button type="submit" class="bg-blue-500 text-white uppercase font-semibold text-xs  py-2 px-10 rounded-2xl hover:bg-blue-600">
                    Reply
                </button>
            </div>
        </form>
        @endauth

            @endif

            @if ( $comment->replies )
            @foreach($comment->replies as $reply)
            <article class="flex bg-gray-100 border-gray-200 ml-15 p-6 rounded-xl space-x-4">
            <div class="flex-shrink-0">
                <img src="{{asset('images/avatars/'. $reply->author->image)}}" alt="" class="inline object-cover w-16 h-16 mr-2 rounded-full">
            </div>

            <div>
                <header class="mb-4">
                    <h3 class="font-bold">{{ $reply->author->name }}</h3>

                <p class="text-xs">
                    Posted
                    <time>{{ $reply->created_at->format('F j, Y, g:i a') }}</time>
                </p>
                </header>

                <p>
                    {{ $reply->body }}
                </p>

            </div>
            </article>
            @auth
            <form  method="POST" action="/blog/{{ $post->slug }}/comments" class="border border-y-4 mb-10 border-gray-200 ml-40 p-6 rounded-xl">
                @csrf

                <div>
                    <textarea name="body" class="w-full text-sm focus:outline-none focus:ring" rows="2" placeholder="Please, share your thougts" required></textarea>
                    <input type="hidden" name="comment_id" value="{{ $comment->id }}" />

                    @error('body')
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex justify-end pt-6  border-t mt-10 border-gray-200">
                    <button type="submit" class="bg-blue-500 text-white uppercase font-semibold text-xs  py-2 px-10 rounded-2xl hover:bg-blue-600">
                        Reply
                    </button>
                </div>
            </form>
            @endauth

        @endforeach
        @endif

        @endforeach

    </section>

</div>


@endsection

