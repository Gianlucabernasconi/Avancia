<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Actividad;
use App\Models\Paciente;
use Inertia\Inertia;

class ActividadController extends Controller
{
    
    public function index()
    {
        $actividades = Actividad::all();
        $pacientes = Paciente::all();
        return Inertia::render('Actividades', [
            'actividades' => $actividades,
            'pacientes' => $pacientes,
        ]);
    } 
    

    
    public function create()
    {
        return Inertia::render('Actividades/Create');  
    }

  
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fecha_asignacion' => 'nullable|date',
            'fecha_limite' => 'nullable|date',
            'estado' => 'required|in:pendiente,completado,cancelado',
            'id_paciente' => 'required|exists:pacientes,id_paciente',
        ]);

        Actividad::create($validated);  // Crear actividad

        return redirect()->route('actividades.index')->with('success', 'Actividad creada con éxito.');
    }

    
    public function show(string $id)
    {
        $actividad = Actividad::with('paciente')->find($id);

        if (!$actividad) {
            return redirect()->route('actividades.index')->with('error', 'Actividad no encontrada.');
        }

        return Inertia::render('Actividades/Show', ['actividad' => $actividad]);  // Renderizar la vista de detalles
    }

    
    public function edit(string $id)
    {
        $actividad = Actividad::find($id);

        if (!$actividad) {
            return redirect()->route('actividades.index')->with('error', 'Actividad no encontrada.');
        }

        return Inertia::render('Actividades/Edit', ['actividad' => $actividad]);  // Renderizar la vista de edición
    }

   
    public function update(Request $request, string $id)
    {
        $actividad = Actividad::find($id);

        if (!$actividad) {
            return redirect()->route('actividades.index')->with('error', 'Actividad no encontrada.');
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fecha_asignacion' => 'nullable|date',
            'fecha_limite' => 'nullable|date',
            'estado' => 'required|in:pendiente,completado,cancelado',
            'id_paciente' => 'required|exists:pacientes,id_paciente',
        ]);

        $actividad->update($validated);  // Actualizar la actividad

        return redirect()->route('actividades.index')->with('success', 'Actividad actualizada con éxito.');
    }

   
    public function destroy(string $id)
    {
        $actividad = Actividad::find($id);

        if (!$actividad) {
            return redirect()->route('actividades.index')->with('error', 'Actividad no encontrada.');
        }

        $actividad->delete();  // Eliminar la actividad

        return redirect()->route('actividades.index')->with('success', 'Actividad eliminada con éxito.');
    }
}
