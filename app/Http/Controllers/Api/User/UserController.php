<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;
use App\Models\Role;
use App\Models\User;
use App\Services\User\UserService;
use App\Values\StatusValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use PHPUnit\Exception;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $offset = $request->page['offset'] ?? 0;
        $limit = $request->page['limit'] ?? 10;
        $data = [
            'data' => UserResource::collection(User::offset($offset)->limit($limit)->get())
        ];
        return response($data, StatusValue::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        try {
            $data = [];
            $validator = Validator::make($request->all(), [
                'firstName' => 'required',
                'lastName' => 'required',
                'role' => 'required',
                'email' => "unique:users,email",
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                $data['errors'] = $validator->errors()->all();
                throw new \Exception("Validation errors", StatusValue::HTTP_UNPROCESSABLE_ENTITY);
            }

            $findUser = User::where('email', $request->email)->first();
            if ($findUser) {
                $data['errors'] = ['This email already has existed'];
                throw new \Exception("", StatusValue::HTTP_UNPROCESSABLE_ENTITY);
            }

            $role = Role::find($request['role']);

            if(!$role){
                throw  new \Exception('Role not found',StatusValue::HTTP_UNPROCESSABLE_ENTITY);
            }

            $userService = new UserService();

            $user = new User;
            $user->first_name = $request['firstName'];
            $user->last_name =  $request['lastName'];
            $user->email = $request['email'];
            $user->user_name = $userService->generateUserName( $request['firstName'],  $request['lastName']);
            $user->password = $userService->generatePassword( $request['password']);
            $user->save();
            $user->attachRole($role);

            if ($user->save()) {
                $data['message'] = 'Successfully registered.';
            }

            return response($data, StatusValue::HTTP_OK);
        } catch (\Exception $e) {
            $data['message'] = $e->getMessage();
            return response($data, $e->getCode());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
