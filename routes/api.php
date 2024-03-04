<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::get('/users', function () {
//     // Your logic to fetch user data from the database or any other source
//     $users = [
//         ['id' => 1, 'name' => 'John'],
//         ['id' => 2, 'name' => 'Jane'],
//         ['id' => 3, 'name' => 'Doe'],
//     ];

//     // Return the API response as JSON
//     return response()->json($users);
// });
