<?php

namespace App\Http\Controllers;

use Auth;
use Hash;
use Validator;
use Throwable;
use App\Models\User;
use App\Models\hobby;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Requests\HobbyRequest;
use App\Http\Controllers\BaseController;


class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $users = User::where('status','ACTIVE')->where('role','USER')->get();
            return $this->sendResponse($users, trans('response.user_index'));
        } catch (Throwable $ex) {
            return $this->sendError($ex->getMessage(), $ex->getCode());
        }
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
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        try {
            $requestData = $request->all();

            if($request->file('user_photo')){
                $file= $request->file('user_photo');
                $filename= date('YmdHi').'.'.$file->getClientOriginalExtension();
                $file->move(public_path('image'), $filename);
                $requestData['user_photo'] = $filename;
            }
            $requestData['password'] = Hash::make($request->password);

            $users = User::create($requestData);
            return $this->sendResponse($users, trans('response.user_store'));
        } catch (Throwable $ex) {
            return $this->sendError($ex->getMessage(), $ex->getCode());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
    public function update(UserRequest $request, $id)
    {
        try {
            $requestData = $request->all();

            if($request->file('user_photo')){
                $file= $request->file('user_photo');
                $filename= date('YmdHi').'.'.$file->getClientOriginalExtension();
                $file->move(public_path('image'), $filename);
                $requestData['user_photo'] = $filename;
            }
            $requestData['password'] = Hash::make($request->password);

            $users = User::where('id',$id)->update($requestData);
            return $this->sendResponse($users, trans('response.user_update'));
        } catch (Throwable $ex) {
            return $this->sendError($ex->getMessage(), $ex->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
          
            User::where('id',$id)->delete();
            return $this->sendResponse('', trans('response.user_destroy'));
        } catch (Throwable $ex) {
            return $this->sendError($ex->getMessage(), $ex->getCode());
        }
    }

    /**
     * get all login user hobbies
     * 
     * @param 
     * @return \Illuminate\Http\Response
     */
    public function getHobbies(){
        try {
          
            $id = Auth::user()->id;
            $user = User::find($id);
            $hobby = $user->hobby()->get();
            return $this->sendResponse($hobby, trans('response.data_index'));
        } catch (Throwable $ex) {
            return $this->sendError($ex->getMessage(), $ex->getCode());
        }
      
    }

    /**
     * update hobby by hobby id
     * 
     * @param $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function updateHobbies(HobbyRequest $request,$id){
        try {
          
            $hobby = hobby::where('id',$id)->update(['hobby' => $request->hobby]);
            return $this->sendResponse($hobby, trans('response.data_update'));
        } catch (Throwable $ex) {
            return $this->sendError($ex->getMessage(), $ex->getCode());
        }
      
    }

     /**
     * get all users hobby for admin
     * 
     * @param 
     * @return \Illuminate\Http\Response
     */
    public function getHobbiesForAdmin(Request $request){
        try {
          

            if($request->hobby != ""){
                $user = hobby::with('user')->where('hobby',$request->hobby)->select('id','hobby','user_id')->get();
            } else {
                $user = hobby::with('user')->select('id','hobby','user_id')->get();
            }
            $user = $user->transform(function($query){
                $userData = $query->user()->get(['first_name','email'])->toArray();
                $query->users = $userData;
                unset($query->user);
                return $query;
             });
          
            return $this->sendResponse($user, trans('response.data_index'));
        } catch (Throwable $ex) {
            return $this->sendError($ex->getMessage(), $ex->getCode());
        }
      
    }
}
