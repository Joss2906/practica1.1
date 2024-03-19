<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Rules\ReCaptcha;
use App\Http\Controllers\VerificationCodeController;
use App\Mail\MailService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;


class LoginController extends Controller
{
    
    public static function getValidationMessages(){
        return [
            'required' => 'Este campo es requerido',
            'string' => 'El campo :attribute debe ser una cadena de texto',
            'min' => 'El campo :attribute debe tener al menos :min caracteres',
            'max' => 'El campo :attribute debe tener como máximo :max caracteres',
            'email' => 'El campo :attribute debe ser una dirección de correo válida',
            'unique' => 'El campo :attribute es invalido',
        ];

    }

    public static function getValidationAttributes(){
        return [
            'name' => 'nombre',
            'email' => 'correo electrónico',
            'password' => 'contraseña',
            'g-recaptcha-response' => 'recaptcha'
        ];
    }

    public function welcome()
    {
        return view('welcome');
    }

    public function registerForm()
    {
        return view('register');
    }

    public function loginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        try {

            $rules = [
                'email' => 'required|email',
                'password' => 'required|string|min:8|max:20',
                'g-recaptcha-response' => ['required', new ReCaptcha]
            ];
    
            $email = $request->input('email');
            $password = $request->input('password');
    
            $validator = Validator::make(
                $request->all(), 
                $rules, 
                self::getValidationMessages(), 
                self::getValidationAttributes()
            );
    
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $userToValidate = User::query()->where('email', $email)->first();
            $passwdChecked = false;

            if ($userToValidate instanceof User) {
                $passwdChecked = password_verify($password, $userToValidate->password);
            }

            $user = $passwdChecked ? $userToValidate : null;

            if (!$user) {
                return redirect()->back()->with('error', 'Algun dato proporcionado es incorrecto');
            }
    
            if ($user->role_id == 1) {

                $verificationCode = rand(10000, 99999);
                $url = URL::temporarySignedRoute('verify', now()->addMinutes(5));
                Cookie::queue('id', $user->id, 5);

                VerificationCodeController::saveCode($verificationCode, $user->id);

                Mail::to($user->email)->send(new MailService($user->name, $verificationCode));

                Log::channel('slackNotification')
                    ->info(
                        'Se envió código de verificacion al correo del usuario',
                        ['email' => $email]
                    );

                return Redirect::to($url);

            } else {
                Auth::loginUsingId($user->id);
                $request->session()->regenerate();

                $userCreated = User::where('id', $user->id)->first();

                $userCreated->is_active = 1;
                $userCreated->save();

                Log::channel('slackNotification')
                    ->info('El usuario inició sesión', ['email' => $email]);

                return redirect()->route('welcome')->with(
                    [
                        'success' => $user->name,
                        'role' => $user->role_id
                    ]
                );

            }

        } catch (ValidationException $e) {

            Log::channel('slackNotification')
                ->error('Validation Exception ' . $e->getMessage());

            return response()->json([
                'message' => 'Ocurrio un error al validar los datos',
            ], 422);

        } catch (QueryException $e) {

            Log::channel('slackNotification')
                ->error('Query Exception ' . $e->getMessage());

            return response()->json([
                'message' => 'Ocurrio un error al realizar la consulta',
            ], 422);
            
        }
    }

    public static function create(Request $request)
    {
        try {
            $rules = [
                'name' => 'required|string|min:5|max:50',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|max:20',
                'g-recaptcha-response' => ['required', new ReCaptcha]
            ];
                        

            $validator = Validator::make(
                $request->all(), 
                $rules, 
                self::getValidationMessages(), 
                self::getValidationAttributes()
            );
            
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $users = User::get();
            $user = new User();
            
            if ($users->isEmpty()) {
                $user->role_id = 1;
            } else {
                $user->role_id = 2;
            }

            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->save();


            Log::channel('slackNotification')->info('Nuevo usuario registrado', 
                ['email' => $user->email]);
                
            return redirect()->back()->with('success', 'Usuario creado correctamente');

        } catch (ValidationException $e) {

            Log::channel('slackNotification')
                ->error('Validation Exception ' . $e->getMessage());


            return response()->json([
                'message' => 'Ocurrio un error al validar los datos',
            ], 422);

        } catch (QueryException $e) {

            Log::channel('slackNotification')
                ->error('Query Exception ' . $e->getMessage());

            return response()->json([
                'message' => 'Ocurrio un error al realizar la consulta',
            ], 422);
            
        }
        
    }

    public static function logout() {
        
        Log::channel('slackNotification')
            ->info('El usuario cerró sesión', ['email' => Auth::user()->email]);
        
        Cookie::queue(Cookie::forget('id'));
        Auth::logout();

        return redirect()->route('auth');
    }
}
