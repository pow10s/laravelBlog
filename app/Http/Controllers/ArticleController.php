<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Article;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Intervention\Image;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::all();
        return view('articles', ['articles' => $articles]);

    }

    public function show($id)
    {
        $user = Auth::user();
        if ($user->can('can_read')) {
            $article = Article::find($id);
            return view('show', ['article' => $article]);
        }
    }

    public function create()
    {
        $user = Auth::user();
        if ($user->can('can_edit')) {
            return view('create');
        }
    }

    public function store(Request $request)
    {
        Article::create($request->all());
        return redirect('/articles');
    }

    public function edit($id)
    {
        $user = Auth::user();
        if ($user->can('can_edit')) {
            $article = Article::find($id);
            return view('edit', ['article' => $article]);
        }
        return 'You dont have permissions';
    }


    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->can('can_edit')) {
            $article = Article::find($id);
            $article->title = $request->title;
            $article->content = $request->content;
            $article->save();
            return redirect('/articles');
        }
        return 'You dont have permissions';
    }

    public function delete($id)
    {
        $user = Auth::user();
        if ($user->can('can_edit')) {
            $article = Article::find($id);
            $article->delete();
            return redirect('/articles');
        }
    }
}
