<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfile;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('jwt.auth');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProfile  $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProfile $request)
    {
        $user = auth()->user();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return response()->json(['message' => 'Profile updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
