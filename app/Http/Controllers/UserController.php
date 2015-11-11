<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public $user = null;

    public function __construct()
    {
        $this->middleware('jwt.auth', ['only' => ['destroy']]);

        Event::listen('tymon.jwt.valid', function($user){
            $this->user = $user;
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(User::all(),200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()],400);
        }

        Auth::login($this->create($request->all()));

        return response()->json(User::where('email',$request->only('email'))->first(), 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(User::find($id),200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if ($user == null){
            return response()->json(['error' => 'User does not exist'],404);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'email|max:255',
            'password' => 'min:6',
        ]);

        if ($validator->fails()){
            return response()->json(['error'=>$validator->errors()->all()],400);
        }

        if ($request->has('email')){
            $user->email = $request->email;
        }

        if ($request->has('password')){
            $user->password = $request->password;
        }

        $user->save();

        return response()->json(User::find($id), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       $user = User::find($id);

        if ($user == null){
            return response()->json(['error' => 'User does not exist'],404);
        }

        if ($this->user->id != $user->id){
            return response()->json(['error' => 'Not authorized'],401);
        }

        User::destroy($id);

        return response()->json(['message' => 'User deleted'], 200);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function getIndex(){

        $routeCollection = Route::getRoutes();

        $return = "";
        foreach ($routeCollection as $value) {
            $return .= $value->getPath() . "\n";
        }

        return response()->json($return, 200);
    }
}
