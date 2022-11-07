<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repository\TokenRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Passport\Token;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;

class AuthController extends Controller
{
    private $userRepository;
    private $tokenRepository;
    public function __construct(
        UserRepositoryInterface $userRepository,
        TokenRepositoryInterface $tokenRepository
    )
    {
        $this->userRepository = $userRepository;
        $this->tokenRepository = $tokenRepository;
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return ApiResponses::send(
                422,
                (object)["errors" => $validator->errors()]
            );
        }

        // Check if user exist
        $user = $this->userRepository->findByEmail($request->email);
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                foreach ($user->tokens as $token) {
                    $token->revoke();
                    $this->tokenRepository->purgeOldTokens($user->id);
                }

                $token = $user->createToken('Panelone Access Token', ['*'])->accessToken;
                $response = (object)['token' => $token];

                // Unset Values
                unset($user->tokens);

                $response->user = $user;
                return ApiResponses::send(200, $response);
            } else {
                return ApiResponses::send(422, "Password mismatch.");
            }
        } else {
            return ApiResponses::send(422, 'User does not exist');
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:12'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => [
                'required',
                'string',
                'min:10',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&-+]/'
            ],
        ]);

        if ($validator->fails()) {
            return ApiResponses::send(
                422,
                (object)["errors" => $validator->errors()]
            );
        }

        $user = $this->userRepository->create([
            "name" => $request->name,
            "surname" => $request->surname,
            "phone" => $request->phone,
            "email" => $request->email,
            "password" => Hash::make($request->password),
        ]);

        if ($user) {
            return ApiResponses::send(201, $user);
        } else {
            return ApiResponses::send(400, $user);
        }
    }

    public function resetPassword(Request $request)
    {
        dd($request->all());
    }

    public function check()
    {
        return auth('api')->check();
    }

    public function logout(Request $request)
    {
        $user = Auth::guard('api')->user();
        if ($user) {
            $result = $user->token()->revoke();
            return ApiResponses::send(200, 'User successfully logged out.');
        } else {
            return ApiResponses::send(400, 'An error was encountered while logging out.');
        }
    }

    public function validateSession(Request $request)
    {
        $tokenId = (new Parser(new JoseEncoder()))->parse($request->token)->claims()->all()['jti'];
        $token = Token::where("id", $tokenId)->first();
        return response()->json($token);
    }

    /**
     * The user has been authenticated.
     *
     * @param Request $request
     * @param mixed $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        $user->update([
            'last_login_at' => Carbon::now()->toDateTimeString(),
            'last_login_ip' => $request->getClientIp()
        ]);
        return;
    }
}
