<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\App;
use App\Models\Post;
use App\Models\User;
use App\Models\Tag;
use App\Models\Comment;
use App\Models\LangCode;
use App\Forms\CommentForm;

class IndexController extends Controller
{
    public function index(Request $request, LangCode $langCode)
    {
        $data = [];
        $data['title'] = __('Home');        
        $data['rows'] = Post::whereShowInList(true)
            ->whereActive(true)
            ->whereLangCode($langCode->name)
            ->orderByDesc('id')
            ->get();

        return view('index.index', $data);
    }

    public function post(Request $request, FormBuilder $formBuilder, LangCode $langCode, Post $post)
    {
        $data = [];
        $data['title'] = $post->title;
        $data['row'] = $post;
        $data['commentForm'] = $formBuilder->create(CommentForm::class);

        return view('index.post', $data);
    }

    public function storeComment(Request $request, LangCode $langCode, Post $post)
    {
        $validator = Validator::make($request->all(), [
            'parent_id' => 'nullable|exists:comments,id',
            'name' => 'sometimes|required|min:2',
            'email' => 'sometimes|required|email',
            'comment' => 'required|min:5',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $validated = $validator->validated();

        $obj = new Comment();
        $obj->comment = $validated['comment'];
        $obj->post_id = $post->id;
        $obj->parent_id = $validated['parent_id'];
        $obj->active = false;

        if ($request->user())
            $obj->user_id = $request->user()->id;
        else {
            $obj->name = $validated['name'];
            $obj->email = $validated['email'];
        }

        $obj->save();
        return back()->with('messages', [
            ['sucess', __('Comment saved successfully')],
        ]);
    }


    public function profile(Request $request, LangCode $langCode, User $user)
    {
        $data = [];
        $data['title'] = __('Profile');
        $data['user'] = $user;

        return view('index.profile', $data);
    }

    public function tag(Request $request, LangCode $langCode, Tag $tag)
    {
        $data = [];
        $data['title'] = $tag->name;
        $data['tag'] = $tag;
        $data['rows'] = $tag->posts()
            ->whereActive(true)
            ->whereLangCode($langCode->name)
            ->orderByDesc('id')
            ->get();

        return view('index.index', $data);
    }

    public function author(Request $request, LangCode $langCode, User $user)
    {
        $data = [];
        $data['title'] = __('Posts of') . ' ' . $user->name;
        $data['author'] = $user;
        $data['rows'] = $user->posts()
            ->whereActive(true)
            ->whereLangCode($langCode->name)
            ->orderByDesc('id')
            ->get();

        return view('index.index', $data);
    }
}
