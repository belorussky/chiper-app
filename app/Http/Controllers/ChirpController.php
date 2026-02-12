<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChirpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $chirps = [
            [
                'author' => 'John Doe',
                'message' => 'Just deployed my first Laravel app!',
                'time' => '1 hour ago',
            ],
            [
                'author' => 'Jane Smith',
                'message' => 'Laravel makes building web apps fun again!',
                'time' => '2 hours ago',
            ],
            [
                'author' => 'Alice Johnson',
                'message' => 'Working on some new features for the app.',
                'time' => '3 hours ago',
            ]
            ];
        return view('home', ['chirps' => $chirps]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
