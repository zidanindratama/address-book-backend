<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Exception;


class ContactController extends Controller
{
    public function index()
    {
        $search = request()->get("search") ? request()->get("search") : "";
        $count = request()->get("count") ? request()->get("count") : 10;

        return response()->json(["success" => true, "data" =>  Contact::where("name", 'LIKE', '%' . $search . '%')->paginate($count)]);
    }

    public function show(string $id)
    {
        return response()->json(["success" => true, "data" =>  Contact::where("id", $id)->first()]);
    }

    public function store()
    {
        try {
            request()->validate([
                'name' => 'required|min:4|max:255',
                'address' => 'required|min:4|max:255',
                'phoneNumber' => 'required|max:255',
            ]);
        } catch (Exception $err) {

            return response()->json(["success" => false, "error" =>  $err->getMessage()], 403);
        }


        $data = Contact::create(request()->all());
        return response()->json(["success" => true, "data" =>  $data]);
    }

    public function update(string $id)
    {
        try {
            request()->validate([
                'name' => 'required|min:4|max:255',
                'address' => 'required|min:4|max:255',
                'phoneNumber' => 'required|max:255',
            ]);
        } catch (Exception $err) {

            return response()->json(["success" => false, "error" =>  $err->getMessage()], 403);
        }
        $dataId = Contact::where("id", $id)->update(request()->all());
        $data = Contact::where("id", $dataId)->first();
        return response()->json(["success" => true, "data" =>  $data]);
    }

    public function destroy(string $id)
    {
        $flight = Contact::find($id);
        $flight->delete();
        return response()->json(["success" => true, "message" => "Success Deleted Contact"]);
    }

    public function upload()
    {
        try {
            request()->validate(['file' => 'required|mimes:json|max:2048']);
        } catch (Exception $err) {

            return response()->json(["success" => false, "error" =>  $err->getMessage()], 403);
        }


        // Proses penyimpanan file ke folder lokal
        $file = request()->file('file');
        $destinationPath = 'storage/app/public'; // Lokasi folder penyimpanan
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move($destinationPath, $filename);

        // Baca file JSON
        $jsonContent = file_get_contents($destinationPath . '/' . $filename);
        $jsonData = json_decode($jsonContent, false);
        if (is_array($jsonData)) {
            foreach ($jsonData as $data) {

                Contact::create([
                    "name" => $data->name,
                    "address" => $data->address,
                    "phoneNumber" => $data->phoneNumber
                ]);
            }
        } else {
            Contact::create([
                "name" => $jsonData->name,
                "address" => $jsonData->address,
                "phoneNumber" => $jsonData->phoneNumber
            ]);
        }



        try {
            unlink($destinationPath . "/" . $filename);
        } catch (Exception $err) {
            echo "Gagal menghapus file";
        }


        return response()->json(["success" => true, "data" => $jsonData]);
    }
}