<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VerificationCode as Code;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;

class VerificationCodeController extends Controller
{
    //
    public static function saveCode($code, $user_id) {

        try {
            $codigo = new Code();

            $codigo->code = bcrypt($code);
            $codigo->user_id = $user_id;

            $codigo->save();

        } catch (QueryException $e) {

            Log::channel('slackNotification')->error('Query Exception '.$e->getMessage());
            
            return response()->json([
                'message' => 'Ocurrio un error al guardar el codigo'
            ], 422);
        }
    }

    public static function verifyCodeView(Request $request) {

        if (!$request->hasValidSignature()) {

            $userId = Cookie::get('id');
            $code = Code::where("user_id", $userId)->first();

            if ($code instanceOf Code) {
                $code->delete();
            }

            Session::flush();
            Auth::logout();
        
            return redirect()->route('auth');
        }

        return view('code');
    }

    public static function validateCode(Request $request) {
        try {

            $rules = [
                'code' => 'required|numeric|digits:5',
            ];

            $messages = [
                'required' => 'El :attribute es requerido',
                'numeric' => 'El :attribute debe ser numérico',
                'digits' => 'El :attribute debe tener :digits dígitos',
            ];

            $attributes = [
                'code' => 'código de verificación',
            ];

            $validator = Validator::make($request->all(), $rules, $messages, $attributes);
    
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $userId = Cookie::get('id');
            $codeModel = Code::where('user_id', $userId)->first();

            $codigo = $codeModel->code;
            $user = User::find($userId);

            if (password_verify($request->code, $codigo)) {

                Auth::loginUsingId($user->id);
                $request->session()->regenerate();
                                
                $user->is_active = 1;
                $user->save();
                
                Log::channel('slackNotification')
                    ->info('Usuario ingresó código correcto', ['email' => $user->email]);


                $codeModel->delete();

                Log::channel('slackNotification')
                    ->info('Usuario inicio sesion', ['email' => $user->email]);

                return redirect()->route('welcome')->with(
                    [
                        'success' => $user->name,
                        'role' => $user->role_id
                    ]
                );
                
            } else {

                Log::channel('slackNotification')
                    ->error('Usuario ingresó código incorrecto', ['email' => $user->email]);

                $codeModel->delete();

                return redirect()->back()->with('error', 'Código incorrecto');
            }
        } catch (ValidationException $e) {

            Log::channel('slackNotification')->error('Validation Exception '.$e->getMessage());

            return response()->json([
                'message' => 'Ocurrio un error al validar los datos',
            ], 422);   

        } catch (QueryException $e) {

            Log::channel('slackNotification')->error('Query Exception '.$e->getMessage());

            return response()->json([
                'message' => 'Ocurrio un error al consultar los datos'
            ], 422);
        }

    }

}
