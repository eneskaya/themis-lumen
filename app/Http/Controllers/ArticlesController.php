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
        $articlesCursor = r\table('pages')->run($this->conn);
        return $articlesCursor->current();
    }
}
