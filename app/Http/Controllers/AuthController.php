<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Template;
use App\Mail\SendMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function whois(Request $request)
    {
        return $request->user();
    }

    public function authenticated(Request $request)
    {
        sleep(3);
        
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        if (! \Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $user = $request->user();

        if ($user->status == 'Inactive') {
                return response()->json([
                    'message' => 'Inactive'
                ], 401);
        }

        if ($user->status == 'Pending' && ! $user->verify_code) {
            return response()->json([
                'message' => 'Pending'
            ], 401);
        }

        $token = $user->createToken('meet-auth')->plainTextToken;

        return response()->json([
            'uuid' => $request->user()->uuid,
            'name' => $request->user()->name,
            'email' => $request->user()->email,
            'avatar' => $request->user()->avatar,
            'access_token' => $token,
            'first_time_login' => $request->user()->first_time_login,
            'token_type' => 'Bearer',
            'provider' => $request->user()->provider ?? null,
            'role' => $request->user()->getRoleNames()[0],
            'email_verified_at' => $request->user()->email_verified_at,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
     
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function forgotPassword(Request $request)
    {   
        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return;
        }

        $user->verify_code = hash_hmac('gost', $request->email, 'forgotPassword');
        $user->save();

        $link = sprintf(
            '%s/reset-password?token=%s&email=%s&mode=reset_password', 
            config('app.web'), 
            $user->verify_code, 
            $user->email
        );

        $emailTemplate = Template::where('name', 'reset-password')->first();

        $template = str_replace(
            ['{{ name }}', '{{ link }}'],
            [$user->name, $link],
            $emailTemplate->content
        );

        Mail::to($user)->queue(new SendMail($emailTemplate->subject, $template));
    }

    public function resetPassword(Request $request)
    {
        $user = User::query()
            ->where('verify_code', $request->token)
            ->where('email', $request->email)
            ->first();
        
        if (! $user) {
            return response()->json([
                'message' => 'Invalid Token'
            ], 404);
        }


        if ($user->first_time_login == true) {
            $user->first_time_login = false;
        }

        $user->password = bcrypt($request->password);
        $user->verify_code = null;

        if ($user->status == 'Pending') {
            $user->status = 'Active';
            $user->first_time_login = true;
        }

        $user->save();

        return response()->json([
            'message' => 'Password reset'
        ], 200);
    }


    public function changePassword(Request $request)
    {
        $user = User::find($request->user()->id);

        if (! $user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $user->password = bcrypt($request->password);
        $user->verify_code = null;
        $user->first_time_login = false;
        $user->save();

        return response()->json([
            'message' => 'Password changed'
        ], 200);
    }

    public function verify(Request $request)
    {

        $user = User::query()
            ->where('verify_code', $request->token)
            ->where('email', $request->email)
            ->first();
        
        if (! $user) {
            return response()->json([
                'message' => 'Invalid Token'
            ], 404);
        }

        $user->status = 'Active';
        $user->verify_code = null;
        $user->email_verified_at = now();
        $user->save();

        return response()->json([
            'message' => 'Account verified'
        ], 200);
    }

    public function resendEmailVerification(Request $request)
    {
        $user = $request->user();

        $key = 'resend-email-verification-' . $user->id;

        if (RateLimiter::tooManyAttempts($key, 3)) {
            return response()->json([
                'message' => 'Too many requests. Please try again later.'
            ], 429);
        }

        RateLimiter::hit($key, 60);
        
        if (! $user->email_verified_at) {
            $randomNumber = random_int(100000, 999999);
            $user = User::find($request->user()->id);
            $user->verify_code = $randomNumber;
            $user->save();

            $emailTemplate = Template::where('name', 'staff-email-verification')->first();
            
            $template = str_replace(
                ['{{ name }}', '{{ verification_code }}'],
                [$user->name, $user->verify_code],
                $emailTemplate->content
            );
        
            Mail::to($user)->queue(new SendMail($emailTemplate->subject, $template));
        }

        return response()->json(['message' => 'Success']);
    }

    public function verifyAccount(Request $request)
    {
        $user = $request->user();

        $key = 'request-verification-code-' . $user->id;

        if (RateLimiter::tooManyAttempts($key, 3)) {
            return response()->json([
                'message' => 'Too many requests. Please try again later.'
            ], 429);
        }

        RateLimiter::hit($key, 60);

        if (! $user->email_verified_at && $user->verify_code == $request->verify_code) {
            $verifiedUser = User::find($user->id);
            $verifiedUser->status = 'Active';
            $verifiedUser->verify_code = null;
            $verifiedUser->email_verified_at = now();
            $verifiedUser->save();
            
            sleep(3);
        }

        return response()->json(['message' => 'Success']);
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
    
            $user = User::whereEmail($googleUser->getEmail())->first();
            sleep(3);
            if ($user) {
                $user->provider_id = $googleUser->getId();
                $user->provider = 'Google';
                $user->first_time_login = false;
                $user->status = 'Active';
                $user->save();
            } else {
                $user = new User;
                $user->name = $googleUser->getName();
                $user->email = $googleUser->getEmail();
                $user->uuid = \Str::uuid();
                $user->avatar = $googleUser->getAvatar();
                $user->provider_id = $googleUser->getId();
                $user->provider = 'Google';
                $user->email_verified_at = now();
                $user->status = 'Active';
                $user->first_time_login = false;
                $user->save();
    
                $user->assignRole('Staff');
            }
    
            $token = $user->createToken('GoogleAuthToken')->plainTextToken;
    
            $data = [
                'uuid' => $user->uuid,
                'name' => $user->name,
                'email' => $user->email,
                'access_token' => $token,
                'first_time_login' => $user->first_time_login,
                'avatar' => $user->avatar ?? null,
                'token_type' => 'Bearer',
                'provider' => $user->provider ?? null,
                'role' => $user->getRoleNames()[0],
                'email_verified_at' => $user->email_verified_at,
            ];

            return response()->view('google-response', compact('data'));
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Failed to login with Google');
        }
    }

    public function updateProfile(User $user, Request $request)
    {
        sleep(3);
        
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->password) {
            $user->password = bcrypt($request->password);
        }

        if ($request->hasFile('image')) {
            $user->clearMediaCollection('profile');

            $media = $user->addMedia($request->file('image'))
                        ->toMediaCollection('profile');

            $user->avatar = $media->getUrl('thumb');
        }

        $user->save();

        return response()->json($user);
    }
}
