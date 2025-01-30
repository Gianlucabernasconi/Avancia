<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Terapeuta;
use Inertia\Inertia;

class TerapeutaController extends Controller
{
  
    public function index()
    {
        // obtengo el usuario autenticado usando el guard 
        $user = auth('terapeuta')->user();
    
        // Verifico si el usuario es admin
        if (!$user || $user->role !== 'admin') {
            return redirect()->route('permiso')->with('error', 'No tienes permisos para acceder a esta página.');
        }
    
        // obtengo la lista de terapeutas
        $terapeutas = Terapeuta::all();
    
        return Inertia::render('Terapeutas', [
            'terapeutas' => $terapeutas,
            'action' => 'index',
            'flash' => session()->get('success'),
        ]);
    }
    
    
    
    
    
    public function create()
    {
        return Inertia::render('Terapeutas', [
            'action' => 'create',
            'flash' => session()->get('success'),
        ]);
    }

    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|string|email|unique:terapeutas,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:terapeuta', 
        ]);
    
        Terapeuta::create([
            'nombre' => $validated['nombre'],
            'apellido' => $validated['apellido'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
        ]);
    
        return redirect()->route('terapeutas.index')->with('success', 'Terapeuta creado con éxito.');
    }
    
   
    public function show(string $id)
    {
        $terapeuta = Terapeuta::find($id);

        if (!$terapeuta) {
            return redirect()->route('terapeutas.index')->with('error', 'Terapeuta no encontrado.');
        }

        return Inertia::render('Terapeutas', [
            'terapeuta' => $terapeuta,
            'action' => 'show',
            'flash' => session()->get('success'),
        ]);
    }

    
    public function edit(string $id)
    {
        $terapeuta = Terapeuta::find($id);

        if (!$terapeuta) {
            return redirect()->route('terapeutas.index')->with('error', 'Terapeuta no encontrado.');
        }

        return Inertia::render('Terapeutas', [
            'terapeuta' => $terapeuta,
            'action' => 'edit',
            'flash' => session()->get('success'),
        ]);
    }

    
    public function update(Request $request, string $id)
    {
        $terapeuta = Terapeuta::find($id);

        if (!$terapeuta) {
            return redirect()->route('terapeutas.index')->with('error', 'Terapeuta no encontrado.');
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => "required|string|email|unique:terapeutas,email,{$id},id_terapeuta",
            'password' => 'nullable|string|min:6',
        ]);

        $terapeuta->nombre = $validated['nombre'];
        $terapeuta->apellido = $validated['apellido'];
        $terapeuta->email = $validated['email'];
        if (!empty($validated['password'])) {
            $terapeuta->password = bcrypt($validated['password']);
        }
        $terapeuta->save();

        return redirect()->route('terapeutas.index')->with('success', 'Terapeuta actualizado con éxito.');
    }

    
    public function destroy(string $id)
    {
        $terapeuta = Terapeuta::find($id);

        if (!$terapeuta) {
            return redirect()->route('terapeutas.index')->with('error', 'Terapeuta no encontrado.');
        }

        $terapeuta->delete();

        return redirect()->route('terapeutas.index')->with('success', 'Terapeuta eliminado con éxito.');
    }
}