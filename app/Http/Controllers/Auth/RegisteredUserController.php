<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Terapeuta;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:terapeutas,email', 
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $terapeuta = Terapeuta::create([
            'nombre' => $request->name, 
            'apellido' => $request->apellido,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            
        ]);

        event(new Registered($terapeuta));

        Auth::guard('terapeuta')->login($terapeuta); 

        return redirect()->route('sesiones.index');
    }
}