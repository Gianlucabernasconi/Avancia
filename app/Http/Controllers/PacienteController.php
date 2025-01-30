<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paciente;
use App\Models\Terapeuta;

use Inertia\Inertia;


class PacienteController extends Controller
{
    
    public function index(Request $request)
    {

         // obtengo el usuario autenticado usando el guard 
         $user = auth('terapeuta')->user();
    
         // Verifico si el usuario es admin
         if (!$user || $user->role !== 'admin') {
             return redirect()->route('permiso')->with('error', 'No tienes permisos para acceder a esta página.');
         }
     

        $pacientes = Paciente::with('terapeutas')->get();  // Cargar los terapeutas relacionados
        $terapeutas = Terapeuta::all();  // Obtener todos los terapeutas

        return Inertia::render('Pacientes', [
            'pacientes' => $pacientes,
            'terapeutas' => $terapeutas,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'fecha_nacimiento' => 'nullable|date',
            'genero' => 'nullable|in:masculino,femenino,otro',
            'rut' => 'required|string|unique:pacientes,rut',
            'telefono' => 'nullable|string|max:255',
            'telefono_emergencia' => 'nullable|string|max:255',
            'direccion' => 'required|string|max:255',
            'motivo_consulta' => 'required|string',
            'diagnostico' => 'nullable|string',
            'estado' => 'required|in:activo,inactivo',
            'fecha_ingreso' => 'required|date',
            'fecha_egreso' => 'nullable|date',
            'terapeuta_id' => 'nullable|exists:terapeutas,id_terapeuta',
        ]);

        $paciente = new Paciente();
        $paciente->fill($validated);
        $paciente->save();


        if ($request->has('terapeuta_id')) {
            $paciente->terapeutas()->sync([$request->terapeuta_id]);  // Asigno terapeuta al paciente
        }

        return redirect()->route('pacientes.index')->with('success', 'Paciente creado con éxito.');
    }

    public function update(Request $request, string $id)
    {
        $paciente = Paciente::find($id);

        if (!$paciente) {
            return redirect()->route('pacientes.index')->with('error', 'Paciente no encontrado.');
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'fecha_nacimiento' => 'nullable|date',
            'genero' => 'nullable|in:masculino,femenino,otro',
            'rut' => "required|string|unique:pacientes,rut,{$id},id_paciente",
            'telefono' => 'nullable|string|max:255',
            'telefono_emergencia' => 'nullable|string|max:255',
            'direccion' => 'required|string|max:255',
            'motivo_consulta' => 'required|string',
            'diagnostico' => 'nullable|string',
            'estado' => 'required|in:activo,inactivo',
            'fecha_ingreso' => 'required|date',
            'fecha_egreso' => 'nullable|date',
            'terapeuta_id' => 'nullable|exists:terapeutas,id_terapeuta',
        ]);

        $paciente->update($validated);

        if ($request->has('terapeuta_id')) {
            $paciente->terapeutas()->sync([$request->terapeuta_id]);  // Actualizar terapeuta asignado
        }

        return redirect()->route('pacientes.index')->with('success', 'Paciente actualizado con éxito.');
    }

   
    public function destroy(string $id)
    {
        $paciente = Paciente::find($id);

        if (!$paciente) {
            return redirect()->route('pacientes.index')->with('error', 'Paciente no encontrado.');
        }

        $paciente->delete();

        return redirect()->route('pacientes.index')->with('success', 'Paciente eliminado con éxito.');
    }

    public function show(string $id)
    {
        $paciente = Paciente::find($id);

        if (!$paciente) {
            return redirect()->route('pacientes.index')->with('error', 'Paciente no encontrado.');
        }

        // Renderizar una vista detallada del paciente si es necesario
        return Inertia::render('Paciente', ['paciente' => $paciente]);
    }


    


}
