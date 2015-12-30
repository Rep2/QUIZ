<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use Illuminate\Routing\RouteCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Validator;
use File;

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

        echo "<table style='width:100%'>";
        echo "<tr>";
        echo "<td width='10%'><h4>HTTP Method</h4></td>";
        echo "<td width='10%'><h4>Route</h4></td>";
        echo "<td width='80%'><h4>Corresponding Action</h4></td>";
        echo "</tr>";
        foreach ($routeCollection as $value) {
            echo "<tr>";
            echo "<td>" . $value->getMethods()[0] . "</td>";
            echo "<td>" . $value->getPath() . "</td>";
            echo "<td>" . $value->getActionName() . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</br>";

        return response()->json("Ivan Rep 0036475497", 200);
    }

    public function  getLog(){
        $contents = File::get("log");

        return response()->json($contents, 200);
    }

    public function getStats(){
        $pos = File::get("posjecenost");// ::get("posjecenost");
        $browseri = File::get("browseri");

        echo trim($pos, '"');
        echo "\n\n\n";
        echo trim($browseri, '"');

        return response()->json("Ivan Rep 0036475497", 200);

    }
}
