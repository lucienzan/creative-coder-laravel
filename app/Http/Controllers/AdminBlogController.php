<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class AdminBlogController extends Controller
{
    public function index()
    {
        return view("admin.blogs.index");
    }

    public function create()
    {
        $categories = Category::all();
        return view('blog.create',compact('categories'));
    }

    public function store(Request $request)
    {
        request()->validate([
            'title' => ['required','string','min:3'],
            'slug' => ['required','string',Rule::unique('blogs','slug')],
            'description' => ['required','string'],
            'category' => ['required',Rule::exists('categories','id')],
            'thumbnail' => 'required|mimes:jpg,bmp,png',
        ]);
        $file = request()->file('thumbnail');
        $newName = uniqid().".".$file->getClientOriginalExtension();
        $file->storeAs("public/thumbnail",$newName);
    
        $blog = new Blog();
        $blog->title = $request->title;
        if($blog->slug == $request->slug){
        $blog->slug = Str::slug($request->slug).".".uniqid();
        }
        $blog->slug = Str::slug($request->slug).".".uniqid();
        $blog->thumbnail = $newName;
        $blog->description = $request->description;
        $blog->excerpt = Str::words($blog->description,50);
        $blog->user_id = Auth::id();
        $blog->category_id = $request->category;
        $blog->save();
        return redirect('/');
    }
}