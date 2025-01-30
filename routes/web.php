<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InformeController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\TerapeutaController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ActividadController;
use App\Http\Controllers\SesionController;



// Rutas de vistas publicas
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin'    => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::post('/login', function (Request $request) {
    $terapeuta = \App\Models\Terapeuta::first(); 

    if ($terapeuta) {
        Auth::login($terapeuta); 
        $request->session()->put('terapeuta_id', $terapeuta->id);
        return redirect('/sesiones'); 
    }

    return back()->withErrors(['email' => 'Fallo de autenticación.']);
});



// Ruta para cerrar sesion
Route::post('/logout', function (Request $request) {
    Auth::guard('terapeuta')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');


//  autenticación dinamica 
Route::middleware(['web'])->group(function () {
    Route::group(['middleware' => function ($request, $next) {
       
        if (!Auth::guard('terapeuta')->check()) {
            
            $terapeuta = session('terapeuta_id') 
                ? \App\Models\Terapeuta::find(session('terapeuta_id')) 
                : null;

            if ($terapeuta) {
                Auth::guard('terapeuta')->login($terapeuta);
            } else {
                return redirect('/login')->withErrors(['error' => 'Sesión inválida.']);
            }
        }

        return $next($request);
    }], function () {
        // Rutas protegidas
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

      


     

        Route::get('/permiso', function () {
            return Inertia::render('Permiso'); 
        })->name('permiso');
        

        Route::resource('pacientes', PacienteController::class);

        Route::match(['get', 'post', 'put', 'delete'], '/pacientes/pagina', [PacienteController::class, 'pagina'])
            ->name('pacientes.pagina');



        Route::resource('sesiones', SesionController::class);

        Route::match(['get', 'post', 'put', 'delete'], '/sesiones/pagina', [SesionController::class, 'pagina'])
            ->name('sesiones.pagina');


        Route::resource('actividades', ActividadController::class);

        Route::match(['get', 'post', 'put', 'delete'], '/actividades/pagina', [ActividadController::class, 'pagina'])
            ->name('actividades.pagina');
            
        Route::resource('grupos', GrupoController::class);

        Route::match(['get', 'post', 'put', 'delete'], '/grupos/pagina', [GrupoController::class, 'pagina'])
            ->name('grupos.pagina');
            

        Route::resource('informes', InformeController::class);
        Route::match(['get', 'post', 'put', 'delete'], '/informes/pagina', [InformeController::class, 'pagina'])
            ->name('informes.pagina');
                
            
        Route::resource('terapeutas', TerapeutaController::class);
       
        Route::match(['get', 'post', 'put', 'delete'], '/terapeutas/pagina', [TerapeutaController::class, 'pagina'])
            ->name('terapeutas.pagina');


            
        Route::resource('informes', InformeController::class);    
        Route::match(['get', 'post', 'put', 'delete'], '/informes/pagina', [InformeController::class, 'pagina'])
        ->name('informes.pagina');
       
    });
});


require __DIR__ . '/auth.php';
