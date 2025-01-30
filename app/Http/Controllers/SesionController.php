<?php

namespace App\Http\Controllers;

use App\Models\Sesion;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Paciente;

class SesionController extends Controller
{
    
    public function index()
    {
        $sesiones = Sesion::all();
        $pacientes = Paciente::all();

        return Inertia::render('Sesiones', [
            'sesiones' => $sesiones,
            'pacientes' => $pacientes,
        ]);
    }
    
    

   
    public function pagina(Request $request)
    {
        return Inertia::render('Sesiones', [
            'sesiones' => Sesion::all(), // Todas las sesiones de la base de datos
        ]);
    }
    

   
    public function emocionesPredominantes(Request $request)
    {
        $pagina = $request->input('pagina', 'Emociones');
        $intervalo = $request->input('intervalo', 'mes');

        $datos = Sesion::selectRaw('emocion, COUNT(*) as total')
    ->when($intervalo === 'semana', fn($query) => $query->whereBetween('fecha_sesion', [now()->startOfWeek(), now()->endOfWeek()]))
    ->when($intervalo === 'mes', fn($query) => $query->whereMonth('fecha_sesion', now()->month))
    ->when($intervalo === 'año', fn($query) => $query->whereYear('fecha_sesion', now()->year))
    ->groupBy('emocion')
    ->get();


        return Inertia::render($pagina, [
            'emociones' => $datos,
        ]);
    }

    
    public function calidadSesiones(Request $request)
    {
        $pagina = $request->input('pagina', 'Calidad');
        $intervalo = $request->input('intervalo', 'mes');

        $datos = Sesion::selectRaw('calidad, COUNT(*) as total')
            ->when($intervalo === 'semana', fn($query) => $query->whereBetween('fecha_sesion', [now()->startOfWeek(), now()->endOfWeek()]))
            ->when($intervalo === 'mes', fn($query) => $query->whereMonth('fecha_sesion', now()->month))
            ->when($intervalo === 'año', fn($query) => $query->whereYear('fecha_sesion', now()->year))
            ->groupBy('calidad')
            ->get();

        return Inertia::render($pagina, [
            'calidad' => $datos,
        ]);
    }

    
    public function progresoPaciente(Request $request)
    {
        $pagina = $request->input('pagina', 'Progreso');
        $intervalo = $request->input('intervalo', 'mes');

        $datos = Sesion::selectRaw('progreso, COUNT(*) as total')
            ->when($intervalo === 'semana', fn($query) => $query->whereBetween('fecha_sesion', [now()->startOfWeek(), now()->endOfWeek()]))
            ->when($intervalo === 'mes', fn($query) => $query->whereMonth('fecha_sesion', now()->month))
            ->when($intervalo === 'año', fn($query) => $query->whereYear('fecha_sesion', now()->year))
            ->groupBy('progreso')
            ->get();

        return Inertia::render($pagina, [
            'progreso' => $datos,
        ]);
    }

    public function comparativoPacientes(Request $request)
    {
        $pagina = $request->input('pagina', 'Comparativo');
        $intervalo = $request->input('intervalo', 'mes');

        $datos = Sesion::selectRaw('id_paciente, AVG(progreso) as promedio_progreso')
            ->when($intervalo === 'semana', fn($query) => $query->whereBetween('fecha_sesion', [now()->startOfWeek(), now()->endOfWeek()]))
            ->when($intervalo === 'mes', fn($query) => $query->whereMonth('fecha_sesion', now()->month))
            ->when($intervalo === 'año', fn($query) => $query->whereYear('fecha_sesion', now()->year))
            ->groupBy('id_paciente')
            ->get();

        return Inertia::render($pagina, [
            'comparativo' => $datos,
        ]);
    }

    
    public function sesionesRealizadas(Request $request)
    {
        $pagina = $request->input('pagina', 'SesionesRealizadas');
        $intervalo = $request->input('intervalo', 'mes');

        $datos = Sesion::selectRaw('COUNT(*) as total, fecha_sesion')
            ->when($intervalo === 'semana', fn($query) => $query->whereBetween('fecha_sesion', [now()->startOfWeek(), now()->endOfWeek()]))
            ->when($intervalo === 'mes', fn($query) => $query->whereMonth('fecha_sesion', now()->month))
            ->when($intervalo === 'año', fn($query) => $query->whereYear('fecha_sesion', now()->year))
            ->groupBy('fecha_sesion')
            ->orderBy('fecha_sesion', 'asc')
            ->get();

        return Inertia::render($pagina, [
            'sesiones' => $datos,
        ]);
    }

   
    public function store(Request $request)
{
    // Validar los datos recibidos
    $validated = $request->validate([
        'fecha_sesion' => 'required|date',
        'id_paciente'  => 'required|exists:pacientes,id_paciente',
        'calificacion' => 'required|in:buena,mala,regular,muy buena,muy mala',
        'emocion'      => 'required|string|max:255',
        'sintoma'      => 'required|string|max:255',
        'duracion'     => 'nullable|string',
        'nota'         => 'nullable|string',
    ]);

   
    Sesion::create($validated);

   
    return Inertia::render('Sesiones', [
        'sesiones' => Sesion::with('paciente')->get(),
        'pacientes' => Paciente::all(),
        'flash'    => [
            'success' => 'Sesión creada exitosamente.',
        ],
    ]);
}


 
    public function show($id)
    {
        $sesion = Sesion::findOrFail($id); 
    
        return Inertia::render('Sesion', [
            'sesion' => $sesion, 
        ]);
    }
    

    public function update(Request $request, $id)
{
    $validated = $request->validate([
        'fecha_sesion' => 'required|date',
        'id_paciente'  => 'required|exists:pacientes,id_paciente',
        'calificacion' => 'required|in:buena,mala,regular,muy buena,muy mala',
        'emocion'      => 'required|string|max:255',
        'sintoma'      => 'required|string|max:255',
        'duracion'     => 'nullable|string',
        'nota'         => 'nullable|string',
    ]);

    $sesion = Sesion::findOrFail($id);
    $sesion->update($validated);

    return Inertia::render('Sesiones', [
        'sesiones' => Sesion::all(),
        'flash'    => [
            'success' => 'Sesión actualizada exitosamente.',
        ],
    ]);
}

public function destroy($id)
{
    $sesion = Sesion::findOrFail($id);
    $sesion->delete();

    return Inertia::render('Sesiones', [
        'sesiones' => Sesion::all(),
        'flash'    => [
            'success' => 'Sesión eliminada exitosamente.',
        ],
    ]);
}

    





}






