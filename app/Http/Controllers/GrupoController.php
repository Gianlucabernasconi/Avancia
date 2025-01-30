<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Grupo;
use App\Models\Paciente;
use App\Models\Terapeuta;
use Inertia\Inertia;

class GrupoController extends Controller
{
    public function index()


    

    {

         // obtengo el usuario autenticado usando el guard 
         $user = auth('terapeuta')->user();
    
         // Verifico si el usuario es admin
         if (!$user || $user->role !== 'admin') {
             return redirect()->route('permiso')->with('error', 'No tienes permisos para acceder a esta página.');
         }
     


        $grupos = Grupo::with('pacientes')->get(); 
    
        return Inertia::render('Grupos', [
            'grupos' => $grupos,
            'pacientes' => Paciente::all(),
            'terapeutas' => Terapeuta::all()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:activo,inactivo',
            'fecha_inicio' => 'required|date',
            'pacientes' => 'array',
        ]);

        $grupo = Grupo::create($validated);

        if ($request->has('pacientes')) {
            $grupo->pacientes()->sync($request->pacientes);
        }

        return redirect()->route('grupos.index')->with('success', 'Grupo creado con éxito.');
    }

    public function update(Request $request, string $id)
    {
        $grupo = Grupo::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:activo,inactivo',
            'fecha_inicio' => 'required|date',
            'pacientes' => 'array',
        ]);

        $grupo->update($validated);

        if ($request->has('pacientes')) {
            $grupo->pacientes()->sync($request->pacientes);
        }

        return redirect()->route('grupos.index')->with('success', 'Grupo actualizado con éxito.');
    }

    public function destroy(string $id)
    {
        $grupo = Grupo::findOrFail($id);

       
        $grupo->pacientes()->detach();

        $grupo->delete();

        return redirect()->route('grupos.index')->with('success', 'Grupo eliminado con éxito.');
    }
}
