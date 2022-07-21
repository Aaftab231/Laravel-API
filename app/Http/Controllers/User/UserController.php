<?php

namespace App\Http\Controllers\User;

use App\Mail\UserCreated;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Mail;

class UserController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
//        return  response()->json(['data'=> $users], 200);
        return $this->showAll($users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            "name" => "required",
            "email"=> "required|email|unique:users",
            "password"=> "required|min:8|confirmed",
        ];

        $this->validate($request, $rules);
        $data = $request->all();
        $data['password']= bcrypt($request->password);
        $data['verified']= User::UNVERIFIED_USER;
        $data["verification_token"]= (new \App\User)->generateVerificationCode();
        $data["admin"]=User::REGULAR_USER;

        $user = User::create($data);
//        return response()->json(["data"=>$user], 201);
        return $this->showOne($user, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)   #$id
    {
//        $user = User::findorFail($id);

//        return response()->json(['data'=> $user], 200);
        return $this->showOne($user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)    #$id
    {
//        $user = User::findorFail($id);

        $rules = [
            "email" => "email|unique:users,email".$user->id,
            "password" => "min:8|confirmed",
            "admin"=> "in:".User::ADMIN_USER.",".User::REGULAR_USER,
        ];

        if ($request->has("name")){
            $user->name = $request->name;
        };

        if ($request->has("password")){
            $user->password = bcrypt($request->password);
        };
        if ($request->has("email") && $user->email != $request->email){
            $user->verified = User::VERIFIED_USER;
            $user->verification_token = (new \App\User)->generateVerificationCode();
            $user->email = $request->email;
        };
        if ($request->has("admin")){
            if (!$user->isVerified()){
                return response()->json(["error"=>"Only Verified user can modified Admin field","code"=>409],409);
//                return $this->errorResponse("Only Verified user can modified Admin field", 409);
            }
            $user->admin = $request->admin;
        };
        if (!$user->isDirty()){
//            return response()->json(["error"=>"", "code"=>422],422);
            return $this->errorResponse("You need to specify different value", 422);

        }
        $user->save();
//        return response()->json(["data"=>$user],200);
        return $this->showOne($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)    #$id
    {
//        $user = User::findorFail($id);

        $user->delete();
//        return response()->json(["data"=>$user], 200);
        return $this->showOne($user);
    }

    public function verify($token){
        $user = User::where('verification_token', $token)->firstOrFail();
        $user->verified = User::VERIFIED_USER;
        $user->verification_token = null;
        $user->save();

        return $this->showMessage('The account has been verified succesfully');
    }

    public function resend(User $user){
        if ($user->isVerified()){
            return $this->errorResponse("This User is already Verified", 409);
        }
        retry(5, function () use ($user){
            Mail::to($user)->send(new UserCreated($user));
        }, 100);
        return $this->showMessage("The Verification email has been resend");
    }
}
