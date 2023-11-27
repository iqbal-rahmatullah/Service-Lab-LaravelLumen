<?php

namespace App\Http\Controllers;

use App\Models\Lab;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class DokterController extends Controller
{
    public function index()
    {
        $dokter = User::where('roles', 'dokter')->orderBy('name', 'asc')->get();
        return response()->json([
            'status' => 'success',
            'data' => $dokter
        ]);
    }
}
