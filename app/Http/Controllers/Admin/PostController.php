<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Post;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::all();
        return view('admin.posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.posts.create', compact('categories'));
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
            'title' => 'required|max:255',
            'content' => 'required',
            // 'category_id' => 'nullable|exist:categories,id'
        ]);

        $form_data = $request->all();

        $slugTmp = Str::slug($form_data['title']);

        $count = 1;
        while (Post::where('slug', $slugTmp)->first()) {
            $slugTmp = Str::slug($form_data['title']) . "-" . $count;
            $count++;
        }

        $form_data['slug'] = $slugTmp;

        $new_post = new Post();
        $new_post->fill($form_data);
        $new_post->save();
        return redirect()->route('admin.posts.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return view('admin.posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        $categories = Category::all();
        return view('admin.posts.edit', compact('post', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            // 'category_id' => 'nullable|exist:categories,id'
        ]);

        $form_data = $request->all();

        if ($post->title == $form_data['title']) {
            $slug = $post->slug;
        } else {
            $slug = Str::slug($form_data['title']);
            $count = 1;
            while (Post::where('slug', $slug)
                ->where('slug', "!=", $post->id)
                ->first()
            ) {
                $slug = Str::slug($form_data['title']) . "-" . $count;
                $count++;
            }
        }

        $form_data['slug'] = $slug;

        $post->update($form_data);
        return redirect()->route('admin.posts.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('admin.posts.index');
    }
}
