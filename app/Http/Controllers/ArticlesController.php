<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use r;

class ArticlesController extends Controller
{
    protected $conn;

    public function __construct()
    {
        $this->conn = r\connect(env('RDB_HOST'), 28015, 'themis');
    }
    
    public function getArticle($id) {
        $article =
            r\table('pages')->get($id)->run($this->conn);

        if ($article) {
            return $article;
        }

        return response('No ID matching.', 404);
    }

    public function getArticles(Request $request)
    {
        $this->validate($request, [
            'page'      => 'required|integer|min:0',
            'limit'     => 'integer|min:1|max:30',
            'except'    => 'alpha'
        ]);

        $except     = $request->input('except');
        $count      = $request->input('limit') ? intval($request->input('limit')) : 15;
        $page       = intval($request->input('page'));
        $nextPage   = $page + 1;
        $prevPage   = $page === 1 ? $page : $page - 1;

        // Example:
        //
        //  1 -> 1 - 16          0 * x + 1   ->  1 * x + 1
        //  2 -> 16 - 31         1 * x + 1   ->  2 * x + 1
        //  3 -> 31 - 46         2 * x + 1   ->  3 * x + 1
        //  4 -> 46 - 61         3 * x + 1   ->  4 * x + 1

        $sliceStart = (($page - 1) * $count);
        $sliceEnd   = ($page * $count);

        $articlesCursor =
            r\table('pages')->slice($sliceStart, $sliceEnd)->run($this->conn);

        $response = collect();

        $nextPageLink   =
             $except ?
                 env('BASE_URL')."/articles?limit=$count&page=$nextPage&except=$except" :
                 env('BASE_URL')."/articles?limit=$count&page=$nextPage";

        $prevPageLink   =
            $except ?
                env('BASE_URL')."/articles?limit=$count&page=$prevPage&except=$except" :
                env('BASE_URL')."/articles?limit=$count&page=$prevPage";


        $response->put('count', $count);
        $response->put('data', collect());
        $response->put('valid', $articlesCursor->valid());
        $response->put('cursor', [
            'next'      => $nextPageLink,
            'previous'  => $prevPageLink
        ]);

        foreach ($articlesCursor as $item) {
            $item = $except ? array_except($item, [$request->input('except')]) : $item;
            $response->get('data')->push($item);
        }

        return $response;
    }
}
