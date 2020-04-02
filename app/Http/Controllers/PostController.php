<?php

namespace App\Http\Controllers;

use App\Posts;
use App\User;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostFormRequest;
use Illuminate\Http\Request;

class PostController extends Controller {

    public function index() {
        // 5 posts
        $posts = Posts::where('active', 1)->orderBy('created_at', 'desc')->paginate(5);
        // title page
        $title = 'Recent Posts';
        
        return view('home')->withPosts($posts)->withTitle($title);
    }

    public function create(Request $request) {
        // if the user can publish the author or administrator
        if (Auth::user()->can_post()) {
            return view('posts.create');
        } else {
            return redirect('/')->withErrors('You do not have sufficient rights to write a post');
        }
    }

    public function store(PostFormRequest $request) {
        $post = new Posts();
        $post->title = $request->get('title');
        $post->body = $request->get('body');
        $post->slug = str_slug($post->title);
        $post->author_id = Auth::user()->id;
        if ($request->has('save')) {
            $post->active = 0;
            $message = 'Post successfully saved';
        } else {
            $post->active = 1;
            $message = 'Post posted successfully';
        }
        $post->save();
        return redirect('edit/' . $post->slug)->withMessage($message);
    }

    public function show($slug) {
        $post = Posts::where('slug', $slug)->first();
        if (!$post) {
            return redirect('/')->withErrors('requested page not found');
        }
        $comments = $post->comments;
        return view('posts.show')->withPost($post)->withComments($comments);
    }

    public function edit(Request $request, $slug) {
        $post = Posts::where('slug', $slug)->first();
        if ($post && ($request->user()->id == $post->author_id || $request->user()->is_admin()))
            return view('posts.edit')->with('post', $post);
        return redirect('/')->withErrors('you do not have sufficient rights');
    }

    public function update(Request $request) {
        //
        $post_id = $request->input('post_id');
        $post = Posts::find($post_id);
        if ($post && ($post->author_id == $request->user()->id || $request->user()->is_admin())) {
            $title = $request->input('title');
            $slug = str_slug($title);
            $duplicate = Posts::where('slug', $slug)->first();
            if ($duplicate) {
                if ($duplicate->id != $post_id) {
                    return redirect('edit/' . $post->slug)->withErrors('Title already exists.')->withInput();
                } else {
                    $post->slug = $slug;
                }
            }
            $post->title = $title;
            $post->body = $request->input('body');
            if ($request->has('save')) {
                $post->active = 0;
                $message = 'Post saved successfully';
                $landing = 'edit/' . $post->slug;
            } else {
                $post->active = 1;
                $message = 'Post updated successfully';
                $landing = $post->slug;
            }
            $post->save();
            return redirect($landing)->withMessage($message);
        } else {
            return redirect('/')->withErrors('you do not have sufficient rights');
        }
    }

    public function destroy(Request $request, $id) {
        //
        $post = Posts::find($id);
        if ($post && ($post->author_id == $request->user()->id || $request->user()->is_admin())) {
            $post->delete();
            $data['message'] = 'Post deleted successfully';
        } else {
            $data['errors'] = 'Incorrect operation. You do not have sufficient rights';
        }
        return redirect('/')->with($data);
    }

}
