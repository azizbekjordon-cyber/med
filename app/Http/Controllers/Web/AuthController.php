<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Med;
use App\Models\User;
use Database\Seeders\MedicalSystemSeeder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Show the Admin & Medical Staff login panel.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('med.index');
        }

        return view('auth.login');
    }

    /**
     * Process authentication request.
     */
    public function login(Request $request): JsonResponse|RedirectResponse
    {
        if (Med::count() === 0) {
            try {
                (new MedicalSystemSeeder)->run();
            } catch (\Throwable $e) {
                Log::warning('Medical system auto-seeding in AuthController failed: '.$e->getMessage());
            }
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'login' => 'required|string|max:255',
            'password' => 'required|string|min:4',
            'role' => 'required|string|in:doctor,patient,admin',
            'remember' => 'nullable|boolean',
        ], [
            'login.required' => 'Telefon raqamingiz yoki Gmail/Email manzilingizni kiriting.',
            'password.required' => 'Maxfiy parolingizni kiriting.',
            'password.min' => 'Parol kamida 4 ta belgidan iborat bo\'lishi kerak.',
            'role.required' => 'Iltimos, kirish huquqini tanlang (Shifokor yoki Bemor)!',
            'role.in' => 'Kirish huquqi faqat Shifokor yoki Bemor bo\'lishi kerak.',
        ]);

        $loginInput = trim($validated['login']);
        $rawPassword = $validated['password'];
        $nameInput = trim($validated['name'] ?? '');
        $remember = $request->boolean('remember', true);

        $isEmail = str_contains($loginInput, '@') || filter_var($loginInput, FILTER_VALIDATE_EMAIL);
        $cleanDigits = preg_replace('/[^\d]/', '', $loginInput);
        $last9 = strlen($cleanDigits) >= 9 ? substr($cleanDigits, -9) : $cleanDigits;

        // 1. Foydalanuvchini email yoki telefon bo'yicha qidirish
        $user = User::query()
            ->when($isEmail, function ($query) use ($loginInput) {
                $query->whereRaw('LOWER(email) = ?', [strtolower($loginInput)]);
            })
            ->when(! $isEmail, function ($query) use ($loginInput, $cleanDigits, $last9) {
                $query->where(function ($sub) use ($loginInput, $cleanDigits, $last9) {
                    $sub->where('phone', $loginInput)
                        ->orWhere('phone', '+'.$cleanDigits)
                        ->orWhere('phone', $cleanDigits);
                    if (! empty($last9)) {
                        $sub->orWhere('phone', 'like', '%'.$last9);
                    }
                });
            })
            ->first();

        // Agar telefon/email bilan topilmasa, lekin ism ko'rsatilgan bo'lsa, ism bo'yicha qidirib ko'ramiz
        if (! $user && ! empty($nameInput)) {
            $user = User::whereRaw('LOWER(name) = ?', [strtolower($nameInput)])->first();
        }

        $requestedRole = $validated['role'] ?? null;

        // 2. Agar foydalanuvchi mavjud bo'lsa, parolini tekshiramiz
        if ($user) {
            if (! Hash::check($rawPassword, $user->password)) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Kiritilgan maxfiy parol noto\'g\'ri. Qaytadan urinib ko\'ring.',
                    ], 422);
                }

                return back()->withErrors([
                    'password' => 'Kiritilgan maxfiy parol noto\'g\'ri. Qaytadan urinib ko\'ring.',
                ])->withInput($request->except('password'));
            }

            // Agar aniq rol (shifokor yoki bemor) tanlangan bo'lsa, uni belgilaymiz
            if (in_array($requestedRole, ['doctor', 'patient'])) {
                $user->role = $requestedRole;
            } elseif ($user->phone === '+998 910226667' || $last9 === '910226667' || $user->email === 'admin@med.uz' || empty($user->role)) {
                $user->role = 'admin';
                $user->specialty = 'Tizim Bosh Administratori';
            }

            if (! empty($nameInput)) {
                $user->name = $nameInput;
            }
            $user->save();

            // Asosiy ko'rgazmali tibbiy kartani kirgan foydalanuvchiga bog'laymiz
            $primaryMed = Med::where('med_number', 'MED-2026-7841-9012')->first();
            if ($primaryMed) {
                $primaryMed->user_id = $user->id;
                $primaryMed->save();
            }

            Auth::login($user, false);
            $request->session()->regenerate();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Xush kelibsiz, {$user->name}!",
                    'redirect' => route('med.index'),
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'role' => $user->role,
                        'email' => $user->email,
                        'phone' => $user->phone,
                    ],
                ]);
            }

            return redirect()->intended(route('med.index'))->with('status', "Xush kelibsiz, {$user->name}!");
        }

        // 3. Agar foydalanuvchi mavjud bo'lmasa, yangi hisob ochiladi
        $finalRole = in_array($requestedRole, ['doctor', 'patient']) ? $requestedRole : 'admin';
        $finalName = ! empty($nameInput) ? $nameInput : ($isEmail ? explode('@', $loginInput)[0] : ($finalRole === 'admin' ? 'Administrator' : ($finalRole === 'doctor' ? 'Shifokor' : 'Bemor')));
        $email = $isEmail ? strtolower($loginInput) : ($finalRole.'_'.($cleanDigits ?: Str::random(6)).'@med.uz');
        $phone = ! $isEmail ? $loginInput : null;

        $newUser = User::create([
            'name' => $finalName,
            'email' => $email,
            'phone' => $phone,
            'password' => Hash::make($rawPassword),
            'role' => $finalRole,
            'specialty' => $finalRole === 'doctor' ? 'Umumiy amaliyot shifokori' : ($finalRole === 'admin' ? 'Tizim Bosh Administratori' : null),
        ]);

        $primaryMed = Med::where('med_number', 'MED-2026-7841-9012')->first();
        if ($primaryMed) {
            $primaryMed->user_id = $newUser->id;
            $primaryMed->save();
        }

        Auth::login($newUser, false);
        $request->session()->regenerate();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Hisob muvaffaqiyatli yaratildi va tizimga kirildi. Xush kelibsiz, {$newUser->name}!",
                'redirect' => route('med.index'),
                'user' => [
                    'id' => $newUser->id,
                    'name' => $newUser->name,
                    'role' => $newUser->role,
                    'email' => $newUser->email,
                    'phone' => $newUser->phone,
                ],
            ]);
        }

        return redirect()->route('med.index')->with('status', "Hisob muvaffaqiyatli yaratildi! Xush kelibsiz, {$newUser->name}!");
    }

    /**
     * Log out from the system.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Tizimdan muvaffaqiyatli chiqildi.');
    }
}
