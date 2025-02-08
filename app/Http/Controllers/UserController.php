<?php

namespace App\Http\Controllers;

use App\Models\{User, Template};
use Illuminate\Http\Request;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->select(
            'users.name as name', 
            'users.uuid as uuid',
            'users.email as email', 
            'users.status as status',
            'users.avatar as avatar',
            'roles.name as role', 
        )
        ->leftJoin('model_has_roles', function ($join) {
            $join->on('model_has_roles.model_id', '=', 'users.id')
                ->where('model_has_roles.model_type', '=', 'App\Models\User');
        })
        ->leftJoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
        ->when($request->status, function ($query, $status) {
            $query->where('users.status', 'ILIKE', "%{$status}%");
        })
        ->when($request->role, function ($query, $role) {
            $query->where('roles.name', 'ILIKE', "%{$role}%");
        })
        ->when($request->search, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('users.name', 'ILIKE', "%{$search}%")
                    ->orWhere('users.email', 'ILIKE', "%{$search}%")
                    ->orWhere('roles.name', 'ILIKE', "%{$search}%");
            });
        })
        ->paginate(10);

        return response()->json($users);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'max:255'],
            'email' => ['required', 'email', 'unique:users']
        ]);

        sleep(3);

        $password = $request->boolean('is_internal') ? \Str::random(10) : $request->password;

        if (! $request->boolean('is_internal')) {
            $randomNumber = random_int(100000, 999999);
        } else {
            $randomNumber = hash_hmac('gost', $request->email, $password);
        }

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->uuid = \Str::uuid();
        $user->password = bcrypt($password);
        $user->verify_code = isset($randomNumber) ? $randomNumber : null;
        $user->status = 'Pending';
        $user->first_time_login = $request->boolean('is_internal');
        $user->save();

        $user->assignRole($request->boolean('is_internal') ? $request->role : 'Staff');

        if ($request->boolean('is_internal')) {
            $link = sprintf(
                '%s/sign-in?token=%s&email=%s&mode=verify_account', 
                config('app.web'), 
                $user->verify_code, 
                $user->email
            );
    
            $emailTemplate = Template::where('name', 'internal-email-activation')->first();
            
            $template = str_replace(
                ['{{ name }}', '{{ email }}', '{{ password }}', '{{ link }}'],
                [$user->name, $user->email, $password, $link],
                $emailTemplate->content
            );
        
            Mail::to($user)->queue(new SendMail($emailTemplate->subject, $template));
        } else {
            $emailTemplate = Template::where('name', 'staff-email-verification')->first();
            
            $template = str_replace(
                ['{{ name }}', '{{ verification_code }}'],
                [$user->name, $user->verify_code],
                $emailTemplate->content
            );
        
            Mail::to($user)->queue(new SendMail($emailTemplate->subject, $template));
        }

        return response()->json($user);
    }

    public function show(User $user)
    {
        $data = $user->load(['roles']);

        return response()->json([
            'name' => $data->name,
            'email' => $data->email,
            'status' => $data->status,
            'role' => $data->getRoleNames()[0] ? $data->getRoleNames()[0] : null,
            'avatar' => $data->avatar
        ]);
    }

    public function update(User $user, Request $request)
    {
        sleep(3);
        
        $user->name = $request->name;
        $user->email = $request->email;
        $user->status = $request->status;

        if ($request->hasFile('image')) {
            $user->clearMediaCollection('profile');

            $media = $user->addMedia($request->file('image'))
                        ->toMediaCollection('profile');

            $user->avatar = $media->getUrl('thumb');
        }

        $user->save();

        $user->syncRoles($request->role);

        return response()->json(['message' => 'User updated successfully!'], 200);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(['message' => 'Success']);
    }

    public function checkEmail(Request $request)
    {
        $email = User::where('email', $request->email)->first();
        
        if ($email) {
            return 'true';
        } else {
            return 'false';
        }
    }
}
