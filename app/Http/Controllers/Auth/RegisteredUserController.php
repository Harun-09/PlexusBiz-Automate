<?php

namespace App\Http\Controllers\Auth;

use App\Enums\RoleName;
use App\Enums\UserStatus;
use App\Domains\Notifications\Services\MessageService;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register', [
            'accountTypes' => RoleName::publicOptions(),
        ]);
    }

    public function pending(Request $request): Response
    {
        return Inertia::render('Auth/RegisterPending', [
            'accountType' => $request->query('account_type'),
            'accountTypes' => RoleName::publicOptions(),
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, MessageService $messages): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'account_type' => ['required', 'string', Rule::in(RoleName::publicValues())],
            'company_name' => ['required', 'string', 'max:255'],
            'job_title' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'employees' => ['required', 'string', 'max:50'],
            'country' => ['required', 'string', 'max:120'],
            'agree_terms' => ['accepted'],
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = DB::transaction(function () use ($request, $messages): User {
            $name = trim($request->string('first_name').' '.$request->string('last_name'));

            $user = User::create([
                'name' => $name,
                'company_name' => $request->string('company_name')->toString(),
                'job_title' => $request->string('job_title')->toString(),
                'phone' => $request->string('phone')->toString(),
                'employees' => $request->string('employees')->toString(),
                'country' => $request->string('country')->toString(),
                'account_type' => $request->string('account_type')->toString(),
                'email' => $request->string('email')->toString(),
                'password' => Hash::make($request->password),
                'status' => UserStatus::Pending->value,
            ]);

            $this->notifyAdminsOfApplication($user, $messages);
            return $user;
        });

        return redirect()->route('register.pending', [
            'account_type' => $user->account_type,
        ]);
    }

    private function notifyAdminsOfApplication(User $applicant, MessageService $messages): void
    {
        $accountType = RoleName::tryFrom((string) $applicant->account_type);
        $accountLabel = $accountType?->label() ?? 'Account';

        User::query()
            ->whereHas('roles', fn ($query) => $query->where('name', RoleName::Admin->value))
            ->get()
            ->each(function (User $admin) use ($applicant, $accountLabel, $messages): void {
                $messages->sendToUser(
                    receiver: $admin,
                    subject: sprintf('New %s application: %s', strtolower($accountLabel), $applicant->company_name ?: $applicant->name),
                    body: sprintf(
                        '%s (%s) applied as %s and is waiting for approval.',
                        $applicant->name,
                        $applicant->email,
                        $accountLabel,
                    ),
                );
            });
    }
}
