<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use r;

class ResultsController extends Controller
{
    protected $conn;

    public function __construct()
    {
        $this->conn = r\connect(env('RDB_HOST'), 28015, 'themis');
    }

    public function getExperiments(Request $request) {

        $experiments = r\table('results')->run($this->conn);

        $response = collect();

        foreach ($experiments as $experiment) {
            $response->push($experiment);
        }

        return $response;
    }

}
