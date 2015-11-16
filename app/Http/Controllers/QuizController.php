<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

use App\Quiz;
use Validator;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class QuizController extends Controller
{

    public $user = null;

    public function __construct()
    {
        $this->middleware('jwt.auth');

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
        $quizzes = DB::table('quiz')->where(function ($query) {
            $query->where('owner_id', $this->user->id)
                ->orWhere('public', true);
        })->get();

        foreach ($quizzes as $quiz){
            $quiz->owner_ref = "http://46.101.238.99/user/" . $quiz->owner_id;
        }

        return response()->json($quizzes,200);
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
            'name' => 'required|max:255|unique:quiz',
            'description' => 'required|max:255',
            'public' => 'required|boolean'
        ]);

        if ($validator->fails()){
            return response()->json(['error'=>$validator->errors()->all()],400);
        }

        $data = $request->only('name','description','public');
        $data['owner_id'] = $this->user->id;

        $quiz = $this->create($data);
        $quiz->owner_ref = "http://46.101.238.99/user/" . $quiz->owner_id;

        return response()->json($quiz, 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $quiz = Quiz::find($id);

        if ($quiz == null){
            return response()->json(['error'=>'Quiz does not exist'],404);
        }

        if ($quiz->owner_id != $this->user->id){
            return response()->json(['error' => 'Not authorized'], 401);
        }

        return response()->json($quiz, 200);
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
        $quiz = Quiz::find($id);

        if ($quiz == null){
            return response()->json(['error' => 'Quiz does not exist'],404);
        }

        if ($quiz->owner_id != $this->user->id){
            return response()->json(['error' => 'Not authorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'max:255',
            'description' => 'max:255',
            'public' => 'boolean',
            'owner_id' => 'int'
        ]);

        if ($validator->fails()){
            return response()->json(['error'=>$validator->errors()->all()],400);
        }

        if ($request->has('name')){
            $quiz->name = $request->name;
        }

        if ($request->has('description')){
            $quiz->description = $request->description;
        }

        if ($request->has('public')){
            $quiz->public = $request->public;
        }

        if ($request->has('owner_id')){
            $quiz->owner_id = $request->owner_id;
        }

        $quiz->save();

        return response()->json(Quiz::find($id), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $quiz = Quiz::find($id);

        if ($quiz == null){
            return response()->json(['error' => 'Quiz does not exist'],404);
        }

        if ($quiz->owner_id != $this->user->id){
            return response()->json(['error' => 'Not authorized'], 401);
        }

        Quiz::destroy($id);

        return response()->json(['message' => 'Quiz deleted'], 200);
    }

    public function userQuizzes($id){

        if (User::find($id) == null){
            return response()->json(['error' => 'User does not exist'],404);
        }

        return response()->json(Quiz::where('owner_id',$id)->get(), 200);

    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     */
    protected function create(array $data)
    {
        return Quiz::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'public' => $data['public'],
            'owner_id' => $data['owner_id'],
        ]);
    }
}
