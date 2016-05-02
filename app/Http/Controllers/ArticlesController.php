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

    public function getArticles(Request $request)
    {
        $this->validate($request, [
            'page'      => 'required|integer|min:0',
            'limit'     => 'integer|min:1|max:30'
        ]);

        $count  = $request->input('limit') ? intval($request->input('limit')) : 15;
        $page   = intval($request->input('page'));
        
        //  1 -> 1 - 16          0 * x + 1   ->  1 * x + 1
        //  2 -> 16 - 31         1 * x + 1   ->  2 * x + 1
        //  3 -> 31 - 46         2 * x + 1   ->  3 * x + 1
        //  4 -> 46 - 61         3 * x + 1   ->  4 * x + 1

        $sliceStart = (($page - 1) * $count);
        $sliceEnd   = ($page * $count);

        $articlesCursor =
            r\table('pages')->slice($sliceStart, $sliceEnd)->run($this->conn);
        
        $response = collect();
        $response->put('data', collect());

        foreach ($articlesCursor as $item) {
            $response->get('data')->push($item);
        }
        
        return $response;
    }
}
