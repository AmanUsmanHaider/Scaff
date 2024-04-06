<?php

namespace App\Http\Controllers\Admin\UserManagement;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the Profile view.
     */
    public function edit($id)
    {
        if (auth()->user()->id == $id) {
            $record = User::where('id', $id)->first();
            $data = [
                'page_title' => 'Profile',
                'p_title' => 'Profile',
                'p_summary' => 'Edit Profile',
                'p_description' => null,
                'method' => 'POST',
                'action' => route('admin.profile.update', $record->id),
                'enctype' => 'multipart/form-data', // (Default)Without attachment
//                'enctype' => 'application/x-www-form-urlencoded', // With attachment like file or images in form
                'data' => $record
            ];
            return view('admin.userManagement.profile.edit')->with($data);
        }
        else{
            abort(404, 'NOT FOUND');
        }
    }
    /**
     * Update the specified resource in storage.
     * @param  String_  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (auth()->user()->id == $id) {
            $record = User::where('id','=',$id )->first();
            if (empty($record)){
                abort(404, 'NOT FOUND');
            }
            $this->validate($request, [
                'name' => 'required',
                'email' => 'required|email|unique:users,email,'.$id,
                'password' => 'same:password_confirmation',
                "image" => 'file|mimes:jpg,jpeg,png,gif|max:1024',
            ]);
            //
            $arr =  [
                'name' => $request->input('name'),
                'email' => $request->input('email'),
            ];
            if(!empty($request->input('password'))){
                $arr['password'] = Hash::make($request->input('password'));
            }else{
                $arr = Arr::except($arr,array('password'));
            }
            // Crop Image.
            if ($request->hasFile('image')) {
                //Unlink previous image
                if (isset($record) && $record->image) {
                    $prevImage = Storage::disk('private')->path('user/profile/'.$record->image);
                    if (File::exists($prevImage)) { // unlink or remove previous image from folder
                        File::delete($prevImage);
                    }
                }
                //Get requested image
                $image = $request->file('image');
                $imageOriginalName = $image->getClientOriginalName();
                $imageName = pathinfo($imageOriginalName, PATHINFO_FILENAME);
                $imageExtension = $image->getClientOriginalExtension();
                $imageSize = $image->getSize();
                if ($request->base64image || $request->base64image > '0') {
                    $folderPath = 'user/profile/';
                    $image_parts = explode(";base64,", $request->base64image);
                    $image_type_aux = explode("image/", $image_parts[0]);
                    $image_type = $image_type_aux[1];
                    $image_base64 = base64_decode($image_parts[1]);
                    $filename = date('Y').'/'.date('m').'/'.date('d').'/'.time().'-'. rand(0, 999999).$imageName.'.'.$image_type;
                    $file = $folderPath.$filename;
                    Storage::disk('private')->put($file, $image_base64);
                    $arr['image'] = $filename;
                }
            }
            else{
                $arr['image'] = $record->image;
            }
            $record->update($arr);
            $messages =  [
                array(
                    'message' => 'Record updated successfully',
                    'message_type' => 'success'
                ),
            ];
            Session::flash('messages', $messages);
            return redirect()->back();
        }
        else{
            abort(404, 'NOT FOUND');
        }
    }
    /**
     * Update the specified resource in storage.
     * @param  String_  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getImage($id)
    {
        $record = User::where('id','=',$id )->first();
        if (empty($record)){
            abort(404, 'NOT FOUND');
        }
        $path = Storage::disk('private')->path('user/profile/' . $record->image);
        if (File::exists($path)) {
            $file = File::get($path);
            $type = File::mimeType($path);
            $response = Response::make($file, 200);
            $response->header("Content-Type", $type);
            return $response;
        }
        else{
            abort(404, 'NOT FOUND');
        }

    }
}
