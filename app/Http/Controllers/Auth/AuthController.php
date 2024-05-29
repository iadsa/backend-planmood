<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private $response = [
        'message' => null,
        'data' => null,
    ];

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function register(Request $req)
    {
        Log::info('Register method called.');
        Log::info('Request Method: ' . $req->method());
        Log::info('Request Data: ' . json_encode($req->all()));

        try {
            // Validasi permintaan
            Log::info('Validating request...');
            $req->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6'
            ]);
            Log::info('Request validated.');

            // Membuat pengguna baru
            Log::info('Creating user...');
            $user = User::create([
                'name' => $req->name,
                'email' => $req->email,
                'password' => Hash::make($req->password)
            ]);
            Log::info('User created: ' . json_encode($user));

            // Membuat token untuk pengguna
            Log::info('Creating token...');
            $token = $user->createToken('default')->plainTextToken;
            Log::info('Token created: ' . $token);

            // Menyiapkan respons
            $this->response['message'] = 'success';
            $this->response['data'] = [
                'user' => $user,
                'token' => $token
            ];

            Log::info('Returning response.');
            return response()->json($this->response, 200);
        } catch (ValidationException $e) {
            Log::warning('Validation failed: ' . $e->getMessage());
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error in register: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());
            return response()->json(['message' => 'Registration failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function login(Request $req)
    {
        Log::info('Login method called.');
        Log::info('Request Data: ' . json_encode($req->all()));

        try {
            $req->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            $user = User::where('email', $req->email)->first();

            if (!$user || !Hash::check($req->password, $user->password)) {
                Log::warning('Login failed for email: ' . $req->email);
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            $token = $user->createToken('default')->plainTextToken;

            $this->response['message'] = 'success';
            $this->response['data'] = [
                'token' => $token
            ];

            return response()->json($this->response, 200);
        } catch (ValidationException $e) {
            Log::warning('Validation failed: ' . $e->getMessage());
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error in login: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());
            return response()->json(['message' => 'Login failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function me()
    {
        try {
            $user = auth()->user();
            Log::info('User info retrieved: ' . json_encode($user));

            $this->response['message'] = 'success';
            $this->response['data'] = $user;

            return response()->json($this->response, 200);
        } catch (\Exception $e) {
            Log::error('Error in me: ' . $e->getMessage());
            return response()->json(['message' => 'Could not retrieve user info'], 500);
        }
    }

    public function logout()
    {
        try {
            $user = auth()->user();
            Log::info('User logging out: ' . json_encode($user));

            auth()->user()->currentAccessToken()->delete();

            $this->response['message'] = 'success';
            return response()->json($this->response, 200);
        } catch (\Exception $e) {
            Log::error('Error in logout: ' . $e->getMessage());
            return response()->json(['message' => 'Logout failed'], 500);
        }
    }
}
