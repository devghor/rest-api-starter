<?php

namespace App\Http\Controllers\User;

use App\Enums\StatusCodeEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;
use App\Models\Role;
use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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
        return response($data, StatusCodeEnum::HTTP_OK);
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
            $validator = Validator::make($request->all(), [
                'firstName' => 'required',
                'lastName' => 'required',
                'roleId' => 'required',
                'email' => "unique:users,email",
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }

            $role = Role::find($request['roleId']);

            if (!$role) {
                throw  new \Exception('Role not found');
            }

            $userService = new UserService();

            $user = new User;
            $user->first_name = $request['firstName'];
            $user->last_name = $request['lastName'];
            $user->email = $request['email'];
            $user->user_name = $userService->generateUserName($request['firstName'], $request['lastName']);
            $user->password = $userService->generatePassword($request['password']);
            $user->save();
            $user->attachRole($role);
            return response()->json(['data' => new UserResource($user)], StatusCodeEnum::HTTP_OK);
        } catch (\Exception $e) {
            if ($e instanceof ValidationException) {
                return response()->json(['errors' => [$e->getMessage()]], StatusCodeEnum::HTTP_UNPROCESSABLE_ENTITY);
            } else {
                return response()->json(['errors' => [$e->getMessage()]], StatusCodeEnum::HTTP_UNPROCESSABLE_ENTITY);
            }
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

        try {
            $user = User::find($id);
            if (!$user) {
                throw  new \Exception('User is not found');
            }
            return response()->json(['data' => new UserResource($user)], StatusCodeEnum::HTTP_OK);
        } catch (\Exception $e) {
            if ($e instanceof ValidationException) {
                return response()->json(['errors' => [$e->getMessage()]], StatusCodeEnum::HTTP_UNPROCESSABLE_ENTITY);
            } else {
                return response()->json(['errors' => [$e->getMessage()]], StatusCodeEnum::HTTP_UNPROCESSABLE_ENTITY);
            }
        }
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

        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'firstName' => 'required',
                'lastName' => 'required',
                'roleId' => 'required',
                'email' => 'unique:users,email,' . $id . ',id'
            ]);


            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }

            $role = Role::find($request['roleId']);
            if (!$role) {
                throw  new \Exception('Role is not found');
            }

            $user = User::find($id);
            if (!$user) {
                throw  new \Exception('User is not found');
            }

            $user->first_name = $request['firstName'];
            $user->last_name = $request['lastName'];
            $user->email = $request['email'];
            $user->save();
            $user->syncRoles([$role->id]);

            return response()->json(['data' => new UserResource($user)], StatusCodeEnum::HTTP_OK);
        } catch (\Exception $e) {
            if ($e instanceof ValidationException) {
                return response()->json(['errors' => [$e->getMessage()]], StatusCodeEnum::HTTP_UNPROCESSABLE_ENTITY);
            } else {
                return response()->json(['errors' => [$e->getMessage()]], StatusCodeEnum::HTTP_UNPROCESSABLE_ENTITY);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                throw  new \Exception('User is not found');
            }
            $user->delete();
            return response()->json(['data' => true], StatusCodeEnum::HTTP_OK);
        } catch (\Exception $e) {
            if ($e instanceof ValidationException) {
                return response()->json(['errors' => [$e->getMessage()]], StatusCodeEnum::HTTP_UNPROCESSABLE_ENTITY);
            } else {
                return response()->json(['errors' => [$e->getMessage()]], StatusCodeEnum::HTTP_UNPROCESSABLE_ENTITY);
            }
        }
    }
}
