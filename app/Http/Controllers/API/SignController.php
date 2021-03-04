<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController as BaseController;

class SignController extends BaseController
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $this->sendErorr('Validate Error', $validator->errors());
        }

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('ToDoList_Tasks_Inj@ZZ-2021')->accessToken;
        $success['name'] = $user->name;
        return $this->sendResponse($success, 'User Successfully Registred');
    }


    public function login(Request $request)
    {

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('ToDoList_Tasks_Inj@ZZ-2021')->accessToken;
            $success['name'] = $user->name;
            return $this->sendResponse($success, 'User Successfully Logged in');
        } else {
            return $this->sendErorr('Unauthorised', ['error', 'Unauthorised']);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return response()->json(['message' => ' Successfully logged out']);
    }

    public function user()
    {
        return Auth::user();
    }
}



/*


    public function userDetail()
    {
        $user           =       Auth::user();
        if (!is_null($user)) {
            return response()->json(["status" => $this->sucess_status, "success" => true, "user" => $user]);
        } else {
            return response()->json(["status" => "failed", "success" => false, "message" => "Whoops! no user found"]);
        }
    } */
