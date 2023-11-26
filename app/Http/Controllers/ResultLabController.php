<?php

namespace App\Http\Controllers;

use App\Models\ResultLab;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Pasien;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class ResultLabController extends Controller
{
    public function index()
    {
        $resultLabs = ResultLab::with(['lab', 'user', 'kunjungan'])->orderBy('id', 'ascgit ')->get();
        $pasienId = $resultLabs->pluck('kunjungan.pasien_id')->unique();
        $pasien = Pasien::whereIn('id', $pasienId)->get();

        $resultLabs = $resultLabs->map(function ($resultLab) use ($pasien) {
            $resultLab->pasien = $pasien->firstWhere('id', $resultLab->kunjungan->pasien_id);
            return $resultLab;
        });

        return response()->json([
            'status' => 'success',
            'data' => $resultLabs,
        ]);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => 'required',
            "lab_id" => 'required',
            "kunjungan_id" => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        $request['status'] = "proses";

        $resultLab = ResultLab::create($request->all());

        return response()->json([
            'status' => 'success',
            'data' => $resultLab
        ]);
    }

    public function show($id)
    {
        $resultLab = ResultLab::find($id);

        if (!$resultLab) {
            return response()->json([
                'status' => 'success',
                'data' => $resultLab
            ], 404);
        }

        $resultLab->load(['user', 'lab', 'kunjungan']);
        $pasienId = $resultLab->kunjungan->pasien_id;
        $resultLab->pasien = Pasien::find($pasienId);


        return response()->json([
            'status' => 'success',
            'data' => $resultLab
        ]);
    }

    public function destroy($id)
    {
        $resultLab = ResultLab::find($id);

        if (!$resultLab) {
            return response()->json([
                'status' => 'error',
                'data' => 'data not found',
            ], 404);
        }

        if ($resultLab->hasil_lab) {
            File::delete("result-lab/" . $resultLab->hasil_lab);
        }
        $resultLab->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'data deleted successfully',
        ]);
    }

    public function update(Request $request, $id)
    {
        $resultLab = ResultLab::find($id);

        if (!$resultLab) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'kunjungan_id' => 'required',
            'user_id' => 'required',
            'lab_id' => 'required',
            'hasil_lab' => 'required',
            'status' => 'required',
            'description' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 400);
        }

        if ($request->file('hasil_lab')) {
            $name_file = time() . uniqid() . '.pdf';

            $request->file('hasil_lab')->move('result-lab', $name_file);
            $resultLab->hasil_lab = $name_file;
        }

        $resultLab->update($request->except('hasil_lab'));

        return response()->json([
            'status' => 'success',
            'data' => $resultLab,
        ]);
    }
}
