<script setup>
import { ref, computed } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import DashboardLayout from '@/Components/DashboardLayout.vue';
import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, BarElement, CategoryScale, LinearScale, Tooltip, Legend } from 'chart.js';

ChartJS.register(BarElement, CategoryScale, LinearScale, Tooltip, Legend);

const { pacientes, terapeutas } = defineProps({
  pacientes: { type: Array, required: true },
  terapeutas: { type: Array, required: true }
});

const form = ref({
  id_paciente: null,
  nombre: '',
  apellido: '',
  fecha_nacimiento: '',
  genero: '',
  rut: '',
  telefono: '',
  telefono_emergencia: '',
  direccion: '',
  motivo_consulta: '',
  diagnostico: '',
  estado: 'activo',
  fecha_ingreso: new Date().toISOString().split('T')[0],
  fecha_egreso: '',
  id_terapeuta: null
});

const modal = ref(false);
const modalPaciente = ref(false);

const openModal = () => {
  form.value.id_paciente = null;
  modal.value = true;
};

const closeModal = () => {
  modal.value = false;
};

const openModalPacienteEditar = (paciente) => {
  form.value = { ...paciente, id_terapeuta: paciente.terapeutas?.[0]?.id_terapeuta || null };
  modalPaciente.value = true;
};

const closeModalPaciente = () => {
  modalPaciente.value = false;
};

const enviarFormulario = () => {
  if (!validarFormulario()) {
    console.log("Errores en el formulario:", errores.value); 
    return; 
  }

  if (form.value.id_paciente) {
    Inertia.put(`/pacientes/${form.value.id_paciente}`, form.value, {
      onSuccess: () => {
        alert('Paciente actualizado con éxito');
        closeModal();
        closeModalPaciente();
      }
    });
  } else {
    Inertia.post('/pacientes', form.value, {
      onSuccess: () => {
        alert('Paciente creado con éxito');
        closeModal();
        closeModalPaciente();
      }
    });
  }
};


const enviarFormularioPOST = () => {
  if (!validarFormulario()) {
    console.log("Errores en el formulario:", errores.value); 
    return; 
  }

  
  Inertia.post('/pacientes', form.value, {
    onSuccess: () => {
      alert('Paciente creado con éxito');
      closeModal();
      closeModalPaciente();
    }
  });
};




const eliminarPaciente = (id_paciente) => {
  if (!id_paciente) return;

  if (confirm('¿Estás seguro de eliminar este paciente?')) {
    Inertia.delete(`/pacientes/${id_paciente}`, {
      onSuccess: () => alert('Paciente eliminado con éxito')
    });
  }
};

const id_seleccionado = ref(null);

const idSeleccionado = (paciente) => {
  id_seleccionado.value = paciente.id_paciente;
};

const pacienteEncontrado = computed(() =>
  pacientes.find((p) => p.id_paciente === id_seleccionado.value)
);

const chartData = computed(() => {
  const activos = pacientes.filter((p) => p.estado === 'activo').length;
  const inactivos = pacientes.filter((p) => p.estado === 'inactivo').length;
  const totales = pacientes.length;

  return {
    labels: ['Activos', 'Inactivos', 'Totales'],
    datasets: [
      {
        label: 'Pacientes',
        backgroundColor: ['#82e0aa', '#FF6384', '#42A5F5'],
        data: [activos, inactivos, totales]
      }
    ]
  };
});

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false
};

const nombreBuscado = ref('');

const pacientesFiltrados = computed(() => {
  if (!nombreBuscado.value.trim()) {
    return [];
  }
  return pacientes.filter((paciente) =>
    paciente.nombre?.toLowerCase().includes(nombreBuscado.value.trim().toLowerCase())
  );
});

const mostrarPacientes = computed(() => {
  return [...pacientes].sort(() => Math.random() - 0.5).slice(0, 12);
});

const errores = ref({});
const validarRut = (rut) => {
  if (!rut) return false;


  rut = rut.replace(/\./g, '').replace(/\s/g, '').toUpperCase();

 
  const regex = /^[0-9]+-[0-9K]$/;
  if (!regex.test(rut)) return false;

 
  const [numero, digitoVerificador] = rut.split('-');

  let suma = 0;
  let multiplicador = 2;

  for (let i = numero.length - 1; i >= 0; i--) {
    suma += parseInt(numero[i]) * multiplicador;
    multiplicador = multiplicador < 7 ? multiplicador + 1 : 2;
  }

  
  const dvEsperado = 11 - (suma % 11);
  const dvCalculado = dvEsperado === 11 ? '0' : dvEsperado === 10 ? 'K' : dvEsperado.toString();



  return dvCalculado === digitoVerificador;
};



// Función de validacion general
const validarFormulario = () => {
  errores.value = {}; 

  if (!form.value.nombre.trim()) errores.value.nombre = 'El nombre es obligatorio';
  if (!form.value.apellido.trim()) errores.value.apellido = 'El apellido es obligatorio';

  if (!form.value.fecha_nacimiento) {
    errores.value.fecha_nacimiento = 'La fecha de nacimiento es obligatoria';
  }

  if (!form.value.genero) errores.value.genero = 'El género es obligatorio';

  if (!form.value.rut.trim() || !validarRut(form.value.rut)) {
    errores.value.rut = 'El RUT no es válido';
  }

  const telefonoRegex = /^[0-9]{9}$/;
  if (form.value.telefono && !telefonoRegex.test(form.value.telefono)) {
    errores.value.telefono = 'El teléfono debe tener 9 dígitos numéricos';
  }
  if (form.value.telefono_emergencia && !telefonoRegex.test(form.value.telefono_emergencia)) {
    errores.value.telefono_emergencia = 'El teléfono de emergencia debe tener 9 dígitos numéricos';
  }

  if (!form.value.direccion.trim()) errores.value.direccion = 'La dirección es obligatoria';
  if (!form.value.motivo_consulta.trim()) errores.value.motivo_consulta = 'El motivo de consulta es obligatorio';

  if (!form.value.id_terapeuta) errores.value.id_terapeuta = 'Debe seleccionar un terapeuta';

  
  return Object.keys(errores.value).length === 0;
};



</script>

<template>
  <DashboardLayout>
    <!-- Contenedor principal -->
    <div class="p-4 md:p-6 bg-gray-100 min-h-screen">
      
      <!-- Sección de graficos -->
      <div class="w-full max-w-4xl h-72 sm:h-96 mx-auto bg-white shadow-md rounded-lg p-4">
        <Bar v-if="chartData" :data="chartData" :options="chartOptions" />
      </div>

      <!-- Buscador de pacientes -->
      <div class="mt-6">
        <label for="nombrebuscado" class="block text-gray-700 font-semibold text-sm md:text-base">Buscar paciente:</label>
        <input 
          v-model="nombreBuscado"type="search"id="nombrebuscado" name="nombrebuscado" class="w-full p-2 border rounded-md focus:ring focus:ring-green-400 text-sm" placeholder="Escribe el nombre..."
        />
      </div>

      <!-- Lista de pacientes -->
      <div class="mt-4 space-y-2">
        <div 
          v-for="paciente in pacientesFiltrados" 
          :key="paciente.id_paciente" 
          class="bg-white p-3 rounded-lg shadow flex justify-between items-center text-sm sm:text-base"
        >
          <span @click="openModalPacienteEditar(paciente)" class="cursor-pointer text-green-700 hover:underline">
            {{ paciente.nombre }}
            
          </span>
          <button @click="eliminarPaciente(paciente.id_paciente)" class="bg-red-500 text-white px-3 py-1 rounded text-xs">
              Eliminar
            </button>
        </div>
      </div>

   
      <div class="overflow-x-auto mt-6">
        <table class="w-full bg-white border shadow-md rounded-lg hidden md:table">
          <thead class="bg-[#315A4F] text-white">
            <tr>
              <th class="py-3 px-4 text-sm sm:text-base">Nombre</th>
              <th class="py-3 px-4 text-sm sm:text-base">Terapeuta</th>
              <th class="py-3 px-4 text-sm sm:text-base">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="paciente in mostrarPacientes" :key="paciente.id_paciente" class="border-b hover:bg-gray-50">
              <td class="py-3 px-4">{{ paciente.nombre }} {{ paciente.apellido }}</td>
              <td class="py-3 px-4">{{ paciente.terapeutas?.[0]?.nombre || 'No asignado' }}</td>
              <td class="py-3 px-4 flex space-x-2">
                <button @click="openModalPacienteEditar(paciente)" class="bg-yellow-500 text-white px-3 py-1 rounded text-xs sm:text-sm">
                  Editar
                </button>
                <button @click="eliminarPaciente(paciente.id_paciente)" class="bg-red-500 text-white px-3 py-1 rounded text-xs sm:text-sm">
                  Eliminar 
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        <button  @click="openModal" type="submit" class=" mt-5 mx-auto block px-4 py-2 rounded-md bg-gradient-to-r from-slate-800 via-slate-800 to-slate-800 hover:bg-gradient-to-br text-white">
            Agregar Paciente
          </button>

        <!-- Lista en moviles -->
        <div v-for="paciente in mostrarPacientes" :key="paciente.id_paciente" class="md:hidden bg-white p-4 rounded-lg shadow mt-2">
          <p class="font-semibold">{{ paciente.nombre }} {{ paciente.apellido }}</p>
          <p class="text-sm text-gray-600">Terapeuta: {{ paciente.terapeutas?.[0]?.nombre || 'No asignado' }}</p>
          <div class="mt-2 flex space-x-2">
            <button @click="openModalPacienteEditar(paciente)" class="bg-yellow-500 text-white px-3 py-1 rounded text-xs">
              Editar
            </button>
            <button @click="eliminarPaciente(paciente.id_paciente)" class="bg-red-500 text-white px-3 py-1 rounded text-xs">
              Eliminar 

            </button>
          </div>
        </div>
      </div>

      <!-- Modal Actualizar de paciente -->
  <div v-if="modal || modalPaciente" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 overflow-auto">
    <div class="w-full max-w-lg bg-white rounded-lg shadow-lg p-6 relative mx-4">
      <form @submit.prevent="enviarFormulario" class="space-y-4 max-h-[85vh] overflow-y-auto">
        <h2 class="text-lg font-bold text-gray-900 mb-4 text-center">
          {{ form.id_paciente ? 'Editar Paciente' : 'Nuevo Paciente' }}
        </h2>

        <!-- Campos del formulario -->
        <div>
          <label for="nombre" class="block text-gray-700">Nombre:</label>
          <input v-model="form.nombre" type="text" class="w-full border rounded-md p-2 focus:ring focus:ring-[#315A4F]" />
          <span v-if="errores.nombre" class="text-red-500 text-sm">{{ errores.nombre }}</span>
        </div>

        <div>
          <label for="apellido" class="block text-gray-700">Apellido:</label>
          <input v-model="form.apellido" type="text" class="w-full border rounded-md p-2 focus:ring focus:ring-[#315A4F]" />
          <span v-if="errores.apellido" class="text-red-500 text-sm">{{ errores.apellido }}</span>
        </div>

        <div>
          <label for="fecha_nacimiento" class="block text-gray-700">Fecha de nacimiento:</label>
          <input v-model="form.fecha_nacimiento" type="date" class="w-full border rounded-md p-2 focus:ring focus:ring-[#315A4F]" />
          <span v-if="errores.fecha_nacimiento" class="text-red-500 text-sm">{{ errores.fecha_nacimiento }}</span>
        </div>

        <div>
          <label for="genero" class="block text-gray-700">Género:</label>
          <select v-model="form.genero" class="w-full border rounded-md p-2 focus:ring focus:ring-[#315A4F]">
            <option value="">Seleccione un género</option>
            <option value="masculino">Masculino</option>
            <option value="femenino">Femenino</option>
            <option value="otro">Otro</option>
          </select>
          <span v-if="errores.genero" class="text-red-500 text-sm">{{ errores.genero }}</span>
        </div>

        <div>
          <label for="rut" class="block text-gray-700">RUT:</label>
          <input v-model="form.rut" type="text" class="w-full border rounded-md p-2 focus:ring focus:ring-[#315A4F]" />
          <span v-if="errores.rut" class="text-red-500 text-sm">{{ errores.rut }}</span>
        </div>

        <div>
          <label for="telefono" class="block text-gray-700">Teléfono:</label>
          <input v-model="form.telefono" type="text" class="w-full border rounded-md p-2 focus:ring focus:ring-[#315A4F]" />
          <span v-if="errores.telefono" class="text-red-500 text-sm">{{ errores.telefono }}</span>
        </div>

        <div>
          <label for="telefono_emergencia" class="block text-gray-700">Teléfono de emergencia:</label>
          <input v-model="form.telefono_emergencia" type="text" class="w-full border rounded-md p-2 focus:ring focus:ring-[#315A4F]" />
        </div>

        <div>
          <label for="direccion" class="block text-gray-700">Dirección:</label>
          <input v-model="form.direccion" type="text" class="w-full border rounded-md p-2 focus:ring focus:ring-[#315A4F]" />
          <span v-if="errores.direccion" class="text-red-500 text-sm">{{ errores.direccion }}</span>
        </div>

        <div>
          <label for="motivo_consulta" class="block text-gray-700">Motivo de Consulta:</label>
          <textarea v-model="form.motivo_consulta" class="w-full border rounded-md p-2 focus:ring focus:ring-[#315A4F]"></textarea>
          <span v-if="errores.motivo_consulta" class="text-red-500 text-sm">{{ errores.motivo_consulta }}</span>
        </div>

        <div>
          <label for="diagnostico" class="block text-gray-700">Diagnóstico:</label>
          <textarea v-model="form.diagnostico" class="w-full border rounded-md p-2 focus:ring focus:ring-[#315A4F]"></textarea>
        </div>

        <div>
          <label for="estado" class="block text-gray-700">Estado:</label>
          <select v-model="form.estado" class="w-full border rounded-md p-2 focus:ring focus:ring-[#315A4F]">
            <option value="activo">Activo</option>
            <option value="inactivo">Inactivo</option>
          </select>
        </div>

        <div>
          <label for="fecha_ingreso" class="block text-gray-700">Fecha de ingreso:</label>
          <input v-model="form.fecha_ingreso" type="date" class="w-full border rounded-md p-2 focus:ring focus:ring-[#315A4F]" />
        </div>

        <div>
          <label for="fecha_egreso" class="block text-gray-700">Fecha de egreso:</label>
          <input v-model="form.fecha_egreso" type="date" class="w-full border rounded-md p-2 focus:ring focus:ring-[#315A4F]" />
        </div>

        <div>
          <label for="id_terapeuta" class="block text-gray-700">Terapeuta:</label>
          <select v-model="form.id_terapeuta" class="w-full border rounded-md p-2 focus:ring focus:ring-[#315A4F]">
            <option value="">Seleccione un terapeuta</option>
            <option v-for="terapeuta in terapeutas" :key="terapeuta.id_terapeuta" :value="terapeuta.id_terapeuta">
              {{ terapeuta.nombre }} {{ terapeuta.apellido }}
            </option>
          </select>
          <span v-if="errores.id_terapeuta" class="text-red-500 text-sm">{{ errores.id_terapeuta }}</span>
        </div>

        <button type="submit" class="w-full bg-gradient-to-r from-[#315A4F] to-[#315A4F] hover:bg-green-700 text-white p-2 rounded">
          Enviar
        </button>
        <button @click="closeModalPaciente(), closeModal()" class="mt-3 w-full text-red-500 hover:text-red-700">
          Cerrar
        </button>
        </form>
       
    </div>
  </div>
  
    </div>


     <!-- Modal Agregar de paciente -->
  <div v-if="modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 overflow-auto">
    <div class="w-full max-w-lg bg-white rounded-lg shadow-lg p-6 relative mx-4">
      <form @submit.prevent="enviarFormularioPOST" class="space-y-4 max-h-[85vh] overflow-y-auto">
        <h2 class="text-lg font-bold text-gray-900 mb-4 text-center">
          Agregar Paciente
        </h2>

        <!-- Campos del formulario -->
        <div>
          <label for="nombre" class="block text-gray-700">Nombre:</label>
          <input v-model="form.nombre" type="text" class="w-full border rounded-md p-2 focus:ring focus:ring-[#315A4F]" />
          <span v-if="errores.nombre" class="text-red-500 text-sm">{{ errores.nombre }}</span>
        </div>

        <div>
          <label for="apellido" class="block text-gray-700">Apellido:</label>
          <input v-model="form.apellido" type="text" class="w-full border rounded-md p-2 focus:ring focus:ring-[#315A4F]" />
          <span v-if="errores.apellido" class="text-red-500 text-sm">{{ errores.apellido }}</span>
        </div>

        <div>
          <label for="fecha_nacimiento" class="block text-gray-700">Fecha de nacimiento:</label>
          <input v-model="form.fecha_nacimiento" type="date" class="w-full border rounded-md p-2 focus:ring focus:ring-[#315A4F]" />
          <span v-if="errores.fecha_nacimiento" class="text-red-500 text-sm">{{ errores.fecha_nacimiento }}</span>
        </div>

        <div>
          <label for="genero" class="block text-gray-700">Género:</label>
          <select v-model="form.genero" class="w-full border rounded-md p-2 focus:ring focus:ring-[#315A4F]">
            <option value="">Seleccione un género</option>
            <option value="masculino">Masculino</option>
            <option value="femenino">Femenino</option>
            <option value="otro">Otro</option>
          </select>
          <span v-if="errores.genero" class="text-red-500 text-sm">{{ errores.genero }}</span>
        </div>

        <div>
          <label for="rut" class="block text-gray-700">RUT:</label>
          <input v-model="form.rut" type="text" class="w-full border rounded-md p-2 focus:ring focus:ring-[#315A4F]" />
          <span v-if="errores.rut" class="text-red-500 text-sm">{{ errores.rut }}</span>
        </div>

        <div>
          <label for="telefono" class="block text-gray-700">Teléfono:</label>
          <input v-model="form.telefono" type="text" class="w-full border rounded-md p-2 focus:ring focus:ring-[#315A4F]" />
          <span v-if="errores.telefono" class="text-red-500 text-sm">{{ errores.telefono }}</span>
        </div>

        <div>
          <label for="telefono_emergencia" class="block text-gray-700">Teléfono de emergencia:</label>
          <input v-model="form.telefono_emergencia" type="text" class="w-full border rounded-md p-2 focus:ring focus:ring-[#315A4F]" />
        </div>

        <div>
          <label for="direccion" class="block text-gray-700">Dirección:</label>
          <input v-model="form.direccion" type="text" class="w-full border rounded-md p-2 focus:ring focus:ring-[#315A4F]" />
          <span v-if="errores.direccion" class="text-red-500 text-sm">{{ errores.direccion }}</span>
        </div>

        <div>
          <label for="motivo_consulta" class="block text-gray-700">Motivo de Consulta:</label>
          <textarea v-model="form.motivo_consulta" class="w-full border rounded-md p-2 focus:ring focus:ring-[#315A4F]"></textarea>
          <span v-if="errores.motivo_consulta" class="text-red-500 text-sm">{{ errores.motivo_consulta }}</span>
        </div>

        <div>
          <label for="diagnostico" class="block text-gray-700">Diagnóstico:</label>
          <textarea v-model="form.diagnostico" class="w-full border rounded-md p-2 focus:ring focus:ring-[#315A4F]"></textarea>
        </div>

        <div>
          <label for="estado" class="block text-gray-700">Estado:</label>
          <select v-model="form.estado" class="w-full border rounded-md p-2 focus:ring focus:ring-[#315A4F]">
            <option value="activo">Activo</option>
            <option value="inactivo">Inactivo</option>
          </select>
        </div>

        <div>
          <label for="fecha_ingreso" class="block text-gray-700">Fecha de ingreso:</label>
          <input v-model="form.fecha_ingreso" type="date" class="w-full border rounded-md p-2 focus:ring focus:ring-[#315A4F]" />
        </div>

        <div>
          <label for="fecha_egreso" class="block text-gray-700">Fecha de egreso:</label>
          <input v-model="form.fecha_egreso" type="date" class="w-full border rounded-md p-2 focus:ring focus:ring-[#315A4F]" />
        </div>

        <div>
          <label for="id_terapeuta" class="block text-gray-700">Terapeuta:</label>
          <select v-model="form.id_terapeuta" class="w-full border rounded-md p-2 focus:ring focus:ring-[#315A4F]">
            <option value="">Seleccione un terapeuta</option>
            <option v-for="terapeuta in terapeutas" :key="terapeuta.id_terapeuta" :value="terapeuta.id_terapeuta">
              {{ terapeuta.nombre }} {{ terapeuta.apellido }}
            </option>
          </select>
          <span v-if="errores.id_terapeuta" class="text-red-500 text-sm">{{ errores.id_terapeuta }}</span>
        </div>



        <button type="submit" class="w-full bg-gradient-to-r from-[#315A4F] to-[#315A4F] hover:bg-green-700 text-white p-2 rounded">
          Registrar
        </button>
        <button @click="closeModalPaciente(), closeModal()" class="mt-3 w-full text-red-500 hover:text-red-700">
          Cerrar
        </button>
        
      </form>
      
    </div>
    
  </div>
  
   
    
  </DashboardLayout>
</template>
