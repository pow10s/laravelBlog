<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Article;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::all();
        return view('articles', ['articles' => $articles]);

    }

    public function show($id)
    {
        $article = Article::find($id);
        return view('show', ['article' => $article]);
    }

    public function create()
    {

        $this->authorize('create');
        return view('create');
    }


    public function store(Request $request)
    {
        Article::create($request->all());
        return redirect('/articles');
    }

    public function upload()
    {

    }


    public function edit($id)
    {
        $this->authorize('edit');
        $article = Article::find($id);
        return view('edit', ['article' => $article]);
    }


    public function update(Request $request, $id)
    {
        $article = Article::find($id);
        $article->title = $request->title;
        $article->content = $request->content;
        $article->save();
        return redirect('/articles');
    }

    public function delete($id)
    {
        $this->authorize('delete');
        $article = Article::find($id);
        $article->delete();
        return redirect('/articles');


    }
}
