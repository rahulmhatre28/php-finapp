<?php


namespace App\Http\Controllers;


use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Activitylog\Facades\LogBatch;
use Stringy\Stringy;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:12',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role_id' => 'string',
            'parent_id' => '',
        ]);

        $user = User::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'password' => Hash::make($validatedData['password']),
            'role_id'=>$validatedData['role_id'],
            'parent_id'=>$validatedData['parent_id']
        ]);

        return $this->success(['id'=>$user->id]);
    }

    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:3',
        ]);

        if (!Auth::attempt($request->all())) {
            return $this->error('Credentials not match', 401);
        }
        return $this->success([
            'token' => auth()->user()->createToken('auth_token')->plainTextToken,
            "user" => User::select("id","first_name", "last_name", "email","role_id","parent_id")->where("id", auth()->id())->first()->toArray()
        ]);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return [
            'message' => 'Tokens Revoked'
        ];
    }

    public function forgotPassword(Request $request)
    {
        $validatedData = $request->validate(['email' => 'required|email']);
        $user = User::where("email", $validatedData["email"])->first();
        //$reset_link = ;
        if ($user) {
            $user->reset_token = uniqid();
            $user->save();
        }
    }


}
