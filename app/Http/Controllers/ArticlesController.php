<?php

namespace App\Http\Controllers;

use r;

class ArticlesController extends Controller
{
    protected $conn;

    public function __construct()
    {
        $this->conn = r\connect('localhost', 28015, 'themis');
    }

    public function getArticles()
    {
        $articles = r\table('pages');
        return $articles;
    }
}
