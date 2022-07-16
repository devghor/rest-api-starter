<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\StatusCodeEnum;
use App\Http\Controllers\Controller;
use App\Repositories\User\UserRepository;
use App\Services\Token\TokenService;
use App\Services\Token\TokenServiceInterface;
use App\Services\User\UserService;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    private $userRepo;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepo = $userRepository;
    }

    /**
     * Handle a login request to the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        try {
            $response = [];
            $validator = Validator::make($request->all(), [
                'email' => "email|required",
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                $response['errors'] = $validator->errors()->all();
                throw new \Exception("Validation errors", StatusCodeEnum::HTTP_UNPROCESSABLE_ENTITY);
            }

            $userService = new UserService();
            $tokenService = new TokenService();
            $user = $this->userRepo->findWhere([
                "email" => $request['email']
            ])->first();
            if (!$user) {
                throw new \Exception('This email does not exist.');
            }
            if (Auth::attempt(['email' => $request['email'], 'password' => $request['password']])) {

                $response['token'] = $tokenService->createUserAccessToken($user);
                $response['user'] = $userService->getUserInformation($user);
            } else {
                throw new \Exception("Email or password does not match");
            }

            return response($response, StatusCodeEnum::HTTP_OK);
        } catch (ValidationException $e) {
            $response['message'] = $e->getMessage();
            return response($response, $e->getCode());
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage();
            return response($response, StatusCodeEnum::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    function logout(Request $request, TokenServiceInterface $tokenService)
    {
        try{
            $authUser = Auth::user();
            $tokenService->deleteUserAccessToken($authUser);
            return response([], StatusCodeEnum::HTTP_OK);
        } catch(Exception $e){
            $response['message'] = $e->getMessage();
            return response($response, StatusCodeEnum::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
