<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Informe;
use App\Models\Paciente;
use App\Models\Terapeuta;
use Inertia\Inertia;

class InformeController extends Controller
{
    public function index()
    {
        $informes = Informe::all();
        $terapeutas = Terapeuta::all();
        $pacientes = Paciente::all();

        return Inertia::render('Informes', [
            'informes' => $informes,
            'pacientes' => Paciente::all(),
            'terapeutas' => Terapeuta::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha_informe' => 'required|date',
            'resumen' => 'required|string',
            'id_paciente' => 'required|exists:pacientes,id_paciente',
            'id_terapeuta' => 'required|exists:terapeutas,id_terapeuta',
        ]);

        Informe::create($validated);

        return redirect()->route('informes.index')->with('success', 'Informe creado con éxito.');
    }

    public function update(Request $request, string $id)
    {
        $informe = Informe::findOrFail($id);

        $validated = $request->validate([
            'fecha_informe' => 'required|date',
            'resumen' => 'required|string',
            'id_paciente' => 'required|exists:pacientes,id_paciente',
            'id_terapeuta' => 'required|exists:terapeutas,id_terapeuta',
        ]);

        $informe->update($validated);

        return redirect()->route('informes.index')->with('success', 'Informe actualizado con éxito.');
    }

    public function destroy(string $id)
    {
        $informe = Informe::findOrFail($id);
        $informe->delete();

        return redirect()->route('informes.index')->with('success', 'Informe eliminado con éxito.');
    }
}
