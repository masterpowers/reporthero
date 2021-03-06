<?php

namespace Reporthero\Http\Controllers;

use Illuminate\Http\Request;
use Reporthero\User;
use Tymon\JWTAuth\Facades\JWTAuth;  
use Tymon\JWTAuth\Exceptions\JWTException;


class UsersController extends Controller
{

    // We Get user From Token
    private function user()
    {
        return $user = JWTAuth::parseToken()->authenticate();
    }
    // We Check if User is Admin
    private function isAdmin()
    {
        return \Bouncer::is($this->user())->an('admin');
    }
    // Show All Users
    public function index()
    {
        return response()->json(['users'=>User::all()], 200);
    }
    // Show All Deleted Users That Can Be Force Delete or Restored!
    public function showDeletedUsers()
    {
        $users = User::onlyTrashed()->get();
        return response()->json(['message'=> 'List Of All Deleted Users!', 'users'=> $users], 200);
    }

    // Get Current User and its Role
    public function me()
    {
        return response()->json(['auth'=> $this->user(), 'admin' => $this->isAdmin()], 200);
    }
    // Admin Can Create a New User
    public function addUser() 
    {
        $user = new User();
        $user->first_name = request()->input('first_name');
        $user->last_name = request()->input('last_name');
        $user->password = bcrypt($request->input('password'));
        $user->email = request()->input('email');
        $user->save();
        return response()->json(['message'=> 'A New User Has Been Created!', 'user'=> $user], 200);
    }
    // Admin Can Edit User
    public function editUser($id)
    {
        $data = request()->all();
        $user = User::find($id);
        $user->fill($data);
        $user->save();
        return response()->json(['message'=> 'You Have Edit A User'], 200);
    }
    // User Can Add Their Api Keys
    public function addKlaviyoApiKeys()
    {   
        $user = $this->user();
        $id = $user['id'];
        $user = User::find($id);
        $klaviyo_api = $user->klaviyo_api;
        $klaviyo_api['token'] = request()->input('public_key');
        $klaviyo_api['api_key'] = request()->input('secret_key');
        $user->klaviyo_api = $klaviyo_api;
        $user->save();
        return response()->json(['message'=> 'Api Keys Has Been Set!', 'klaviyo_keys' => $klaviyo_api], 200);
    }

    public function deleteUser($id)
    {
        // We can Return here the User Since we Have Soft Delete
        $user = User::find($id);
        $user->delete();
        return response()->json(['message'=> 'User Has Been Soft Deleted, and Can Still Be Restored!', 'user'=> $user], 200);
    }

    public function recoverUser($id)
    {
        $user = User::withTrashed()->where('id' ,$id)->first();
        $user->restore();
        return response()->json(['message'=> 'User Has Been Restored!', 'user'=> $user], 200);
    }

    public function permaDeleteUser($id)
    {
        $user = User::withTrashed()->where('id' ,$id)->first();
        $user->forceDelete();
        return response()->json(['message'=> 'User Has Been Force Deleted! And Cannot Be Restored!', 'user'=> $user], 200);
    }


    public function viewApiKeys()
    {
        $user = $this->user();
        $id = $user['id'];
        $user = User::find($id);
        $user->public = true;
        $user->save();
        $user->fresh();
        $apikeys = $user->klaviyo_api;
        $user->public = false;
        $user->save();
        return response()->json(['message'=> 'You Can Now Vie Your Api Keys', 'api_keys'=> $apikeys], 200);

    }


    public function changePassword()
    {
        $user = $this->user();
        $id = $user['id'];
        $user = User::find($id);
        $user->password = bcrypt(request()->input('password'));
        $user->save();
        return response()->json(['message'=> 'You Have Successfully Changed Your Password!'], 200);
    }

    public function editProfile()
    {
        $data = request()->all();
        $user = $this->user();
        $id = $user['id'];
        $user = User::find($id);
        $user->fill($data);
        $user->save();
        return response()->json(['message'=> 'You Have Successfully Edit Your Profile'], 200);
    }
}
