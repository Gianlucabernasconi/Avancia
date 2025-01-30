<script setup>
import { ref, computed, watch } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import DashboardLayout from '@/Components/DashboardLayout.vue'
import { defineProps } from 'vue'
import { Bar } from 'vue-chartjs'
import {Chart as ChartJS,BarElement,CategoryScale,LinearScale,Tooltip,Legend} from 'chart.js'


ChartJS.register(BarElement, CategoryScale, LinearScale, Tooltip, Legend)


const { sesiones, pacientes, flash } = defineProps({
  sesiones: { type: Array, required: true },
  pacientes: { type: Array, required: true },
  flash: { type: Object, default: () => ({}) },
})


const id_seleccionado = ref(null)

// Filtrar sesiones segun paciente
const sesionesFiltradas = computed(() => {
  if (!id_seleccionado.value) return []
  return sesiones.filter(s => s.id_paciente === id_seleccionado.value)
})


const chartData = computed(() => {

  if (sesionesFiltradas.value.length === 0) {
    return {
      labels: [],
      datasets: [
        {
          label: 'Síntomas',
          data: [],
          backgroundColor: 'rgba(75, 192, 192, 0.4)'
        },
        {
          label: 'Emociones',
          data: [],
          backgroundColor: 'rgba(255, 99, 132, 0.4)'
        },
        {
          label: 'Calificaciones',
          data: [],
          backgroundColor: 'rgba(153, 102, 255, 0.4)'
        },
      ],
    }
  }

  // Obtener arrays
  const sintomasArr = sesionesFiltradas.value.map(s => s.sintoma || 'Sin Síntoma')
  const emocionesArr = sesionesFiltradas.value.map(s => s.emocion || 'Sin Emoción')
  const calificacionesArr = sesionesFiltradas.value.map(s => s.calificacion || 'Sin Calificación')

  // Cuento ocurrencias
  const conteoSintomas = sintomasArr.reduce((acc, val) => {
    acc[val] = (acc[val] || 0) + 1
    return acc
  }, {})
  const conteoEmociones = emocionesArr.reduce((acc, val) => {
    acc[val] = (acc[val] || 0) + 1
    return acc
  }, {})
  const conteoCalif = calificacionesArr.reduce((acc, val) => {
    acc[val] = (acc[val] || 0) + 1
    return acc
  }, {})

  
  const allLabels = new Set([
    ...Object.keys(conteoSintomas),
    ...Object.keys(conteoEmociones),
    ...Object.keys(conteoCalif),
  ])
  const labels = Array.from(allLabels)

  return {
    labels,
    datasets: [
      {
        label: 'Síntomas',
        data: labels.map(l => conteoSintomas[l] || 0),
        backgroundColor: 'rgba(75, 192, 192, 0.4)',
        borderColor: 'rgba(75, 192, 192, 1)',
        borderWidth: 1,
      },
      {
        label: 'Emociones',
        data: labels.map(l => conteoEmociones[l] || 0),
        backgroundColor: 'rgba(255, 99, 132, 0.4)',
        borderColor: 'rgba(255, 99, 132, 1)',
        borderWidth: 1,
      },
      {
        label: 'Calificaciones',
        data: labels.map(l => conteoCalif[l] || 0),
        backgroundColor: 'rgba(153, 102, 255, 0.4)',
        borderColor: 'rgba(153, 102, 255, 1)',
        borderWidth: 1,
      },
    ],
  }
})

// Opciones del grafico
const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
}


const nombreBuscado = ref('')
const pacientesFiltrados = computed(() => {
  if (!nombreBuscado.value.trim()) {
    return []
  }
  return pacientes.filter(p =>
    p.nombre?.toLowerCase().includes(nombreBuscado.value.trim().toLowerCase())
  )
})
function idSeleccionado(paciente) {
  id_seleccionado.value = paciente.id_paciente
}
const pacienteEncontrado = computed(() =>
  pacientes.find(p => p.id_paciente === id_seleccionado.value)
)


const form = ref({
  fecha_sesion: '',
  id_paciente: '',
  emocion: '',
  sintoma: '',
  duracion: '',
  nota: '',
  calificacion: '',
})

// Obtener nombre completo del paciente
const obtenerNombrePaciente = (id_paciente) => {
  const paciente = pacientes.find(p => p.id_paciente === id_paciente)
  return paciente ? `${paciente.nombre} ${paciente.apellido}` : 'Desconocido'
}

// Enviar sesion
const enviarSesion = () => {
  if (!id_seleccionado.value || !form.value.fecha_sesion) {
    alert('Por favor selecciona un paciente y completa los campos requeridos.')
    return
  }

  form.value.id_paciente = id_seleccionado.value
  Inertia.post('/sesiones', form.value, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      alert('Sesión guardada exitosamente.')
      form.value = {
        fecha_sesion: '',
        id_paciente: '',
        emocion: '',
        sintoma: '',
        duracion: '',
        nota: '',
        calificacion: '',
      }
      id_seleccionado.value = null
    },
    onError: (errors) => {
      console.error('Errores:', errors)
    },
  })
}


// Actualizar sesion
const modalActualizarSesion = ref(false)
const current_sesion_id = ref(null)

const actualizarSesion = () => {
  if (!current_sesion_id.value) {
    alert('No se ha seleccionado una sesión para actualizar.')
    return
  }
  if (!form.value.id_paciente || !form.value.fecha_sesion) {
    alert('Por favor selecciona un paciente y completa los campos requeridos.')
    return
  }

  Inertia.put(`/sesiones/${current_sesion_id.value}`, form.value, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      alert('Sesión actualizada exitosamente.')
      form.value = {
        fecha_sesion: '',
        id_paciente: '',
        emocion: '',
        sintoma: '',
        duracion: '',
        nota: '',
        calificacion: '',
      }
      id_seleccionado.value = null
      cerrarModalActualizarSesion()
    },
    onError: (errors) => {
      console.error('Errores:', errors)
    },
  })
}

const ModalActualizarSesion = ref(false);

function abrirModalActualizarSesionPorId(id_sesion) {
  const sesion = sesiones.find(s => s.id_sesion === id_sesion)
  if (sesion) {
    form.value = { ...sesion }
    id_seleccionado.value = sesion.id_paciente
    modalActualizarSesion.value = true
    current_sesion_id.value = id_sesion
  } else {
    console.error('Sesión no encontrada con ID:', id_sesion)
  }
}
function cerrarModalActualizarSesion() {
  modalActualizarSesion.value = false
  form.value = {
    fecha_sesion: '',
    id_paciente: '',
    emocion: '',
    sintoma: '',
    duracion: '',
    nota: '',
    calificacion: '',
  }
  current_sesion_id.value = null
}

// Borrar sesion
function borrarSesion(id_sesion) {
  if (!confirm('¿Estás seguro de borrar esta sesión?')) {
    return
  }
  Inertia.delete(`/sesiones/${id_sesion}`, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      alert('Sesión eliminada exitosamente.')
    },
    onError: (errors) => {
      console.error('Errores:', errors)
    },
  })
}

//PDF
function printPDF() {
  window.print();
}

const limpiarTexto = (texto) => {
  return texto
    .normalize("NFD") 
    .replace(/[\u0300-\u036f]/g, '') 
    .toLowerCase() 
    .trim() 
    .replace(/[^a-z\s]/g, '') 
}


watch(() => form.value.emocion, (nuevoValor) => {
  form.value.emocion = limpiarTexto(nuevoValor)
})

watch(() => form.value.sintoma, (nuevoValor) => {
  form.value.sintoma = limpiarTexto(nuevoValor)
})

const texto = ref('');

</script>

<template>
  <DashboardLayout>
    <!-- Contenedor principal -->
    <div class="p-4 md:p-6 bg-gray-100 min-h-screen">
      
      <!-- Sección de graficos -->
      <div class="w-full max-w-4xl h-72 sm:h-96 mx-auto bg-white shadow-md rounded-lg p-4">
        <Bar :data="chartData" :options="chartOptions" />
      </div>

      

      <div v-if="flash?.success" class="bg-green-100 text-green-700 p-3 rounded-md mt-4 text-center">
        {{ flash.success }}
      </div>


    
      
      <div class="flex items-center justify-center h-full w-full px-4 mt-5">
  <p class="text-lg text-center font-semibold text-gray-800 break-words overflow-hidden w-full max-w-lg">
    {{ texto }}
  </p>
</div>



      <div class="flex flex-col items-center justify-center min-h-screen bg-gray-100 text-gray-800 p-6">
    <h1 class="text-2xl font-bold mb-4">Describe tu interpretación
      de los datos</h1>
    
      
    <!-- Input con v-model -->
    <input 
      v-model="texto" 
      type="text" 
      class="w-full max-w-md p-2 border rounded-md focus:ring focus:ring-green-400" 
      placeholder="Escribe aquí"
    />

    <button @click="printPDF" class=" mx-auto block w-full sm:w-auto mt-4  px-4 py-2 rounded bg-gradient-to-r from-slate-800 via-slate-800 to-slate-800 hover:bg-gradient-to-br focus:ring-4 focus:outline-none text-white">
        Imprimir
      </button>

   
    
  </div>


      <!-- Tabla de Sesiones -->
      <h1 class="text-2xl font-bold mt-6 mb-4">Sesiones</h1>
      <div class="overflow-x-auto">
        <table class="w-full bg-white border shadow-md rounded-lg hidden md:table">
          <thead class="bg-[#315A4F] text-white">
            <tr>
              <th class="py-3 px-4 text-sm sm:text-base">Fecha</th>
              <th class="py-3 px-4 text-sm sm:text-base">Paciente</th>
              <th class="py-3 px-4 text-sm sm:text-base">Emoción</th>
              <th class="py-3 px-4 text-sm sm:text-base">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="sesion in sesiones" :key="sesion.id_sesion" class="border-b hover:bg-gray-50">
              <td class="py-3 px-4">{{ sesion.fecha_sesion }}</td>
              <td class="py-3 px-4">{{ obtenerNombrePaciente(sesion.id_paciente) }}</td>
              <td class="py-3 px-4">{{ sesion.emocion }}</td>
              <td class="py-3 px-4 flex space-x-2">
                <button @click="abrirModalActualizarSesionPorId(sesion.id_sesion)" class="bg-yellow-500 text-white px-3 py-1 rounded text-xs sm:text-sm">
                  Editar
                </button>
                <button @click="borrarSesion(sesion.id_sesion)" class="bg-red-500 text-white px-3 py-1 rounded text-xs sm:text-sm">
                  Borrar
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Lista en moviles -->
        <div v-for="sesion in sesiones" :key="sesion.id_sesion" class="md:hidden bg-white p-4 rounded-lg shadow mt-2">
          <p class="font-semibold">{{ sesion.fecha_sesion }} - {{ obtenerNombrePaciente(sesion.id_paciente) }}</p>
          <p class="text-sm text-gray-600">Emoción: {{ sesion.emocion }}</p>
          <div class="mt-2 flex space-x-2">
            <button @click="abrirModalActualizarSesionPorId(sesion.id_sesion)" class="bg-yellow-500 text-white px-3 py-1 rounded text-xs">
              Editar
            </button>
            <button @click="borrarSesion(sesion.id_sesion)" class="bg-red-500 text-white px-3 py-1 rounded text-xs">
              Borrar
            </button>
          </div>
        </div>
      </div>

     
      <!-- Buscar Paciente -->
      <div class="mt-6">
        <label for="nombrebuscado" class="block mb-2">Buscar paciente:</label>
        <input v-model="nombreBuscado" type="search" id="nombrebuscado" class="w-full p-2 border rounded-md focus:ring focus:ring-green-400 text-sm" placeholder="Ingrese el nombre del paciente" />
        <div v-for="paciente in pacientesFiltrados" :key="paciente.id_paciente" class="bg-slate-100 p-2 my-2 rounded">
          <span @click="idSeleccionado(paciente)" class="cursor-pointer text-green-700 hover:underline">
            {{ paciente.nombre }} {{ paciente.apellido }}
          </span>
        </div>
      </div>

      <!-- Formulario para crear una sesion -->
      <div class="mt-8 bg-white p-6 rounded-lg shadow-md">
        <form @submit.prevent="enviarSesion" class="space-y-4">
          <div>
            <label for="fecha_sesion" class="block text-sm">Fecha de la sesión:</label>
            <input type="date" id="fecha_sesion" v-model="form.fecha_sesion" class="border p-2 rounded w-full text-sm" required />
          </div>
          <div>
            <label for="emocion" class="block text-sm">Emoción:</label>
            <input type="text" id="emocion" v-model="form.emocion" class="border p-2 rounded w-full text-sm" required />
          </div>
          <div>
            <label for="sintoma" class="block text-sm">Síntoma:</label>
            <input type="text" id="sintoma" v-model="form.sintoma" class="border p-2 rounded w-full text-sm" required />
          </div>
          <div>
            <label for="calificacion" class="block text-sm">Calificación:</label>
            <select id="calificacion" v-model="form.calificacion" class="border p-2 rounded w-full text-sm" required>
              <option value="" disabled>Seleccione una calificación</option>
              <option value="muy buena">Muy buena</option>
              <option value="buena">Buena</option>
              <option value="regular">Regular</option>
              <option value="mala">Mala</option>
              <option value="muy mala">Muy mala</option>
            </select>
          </div>
          <div>
            <label for="nota" class="block text-sm">Nota:</label>
            <textarea id="nota" v-model="form.nota" class="border p-2 rounded w-full text-sm"></textarea>
          </div>
          <button type="submit" class=" mx-auto block bg-gradient-to-r from-slate-800 via-slate-800 to-slate-800 hover:bg-gradient-to-br text-white p-2 rounded w-full sm:w-auto hover:bg-blue-700">
            Guardar sesión
          </button>
        </form>
      </div>
    </div>



    <!-- Modal para Actualizar Sesion -->
    <div
      v-if="modalActualizarSesion"
      class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center"
    >
      <div class="bg-white p-6 rounded shadow-lg w-1/2">
        <h2 class="text-xl font-semibold mb-4">Actualizar Sesión</h2>
        <form @submit.prevent="actualizarSesion" class="space-y-4">
          <div>
            <label for="fecha_sesion_modal" class="block">Fecha:</label>
            <input
              type="date"
              id="fecha_sesion_modal"
              v-model="form.fecha_sesion"
              class="border p-2 rounded w-full"
              required
            />
          </div>
          <div>
            <label for="id_paciente_modal" class="block">Paciente:</label>
            <select
              id="id_paciente_modal"
              v-model="form.id_paciente"
              class="border p-2 rounded w-full"
              required
            >
              <option disabled value="">Seleccione un paciente</option>
              <option
                v-for="paciente in pacientes"
                :key="paciente.id_paciente"
                :value="paciente.id_paciente"
              >
                {{ paciente.nombre }} {{ paciente.apellido }}
              </option>
            </select>
          </div>
          <div>
            <label for="emocion_modal" class="block">Emoción:</label>
            <input
              type="text"
              id="emocion_modal"
              v-model="form.emocion"
              class="border p-2 rounded w-full"
              required
            />
          </div>
          <div>
            <label for="sintoma_modal" class="block">Síntoma:</label>
            <input
              type="text"
              id="sintoma_modal"
              v-model="form.sintoma"
              class="border p-2 rounded w-full"
              required
            />
          </div>
          <div>
            <label for="duracion_modal" class="block">Duración:</label>
            <input
              type="text"
              id="duracion_modal"
              v-model="form.duracion"
              class="border p-2 rounded w-full"
            />
          </div>
          <div>
            <label for="calificacion_modal" class="block">Calificación:</label>
            <select
              id="calificacion_modal"
              v-model="form.calificacion"
              class="border p-2 rounded w-full"
              required
            >
              <option value="" disabled>Seleccione una calificación</option>
              <option value="muy buena">Muy buena</option>
              <option value="buena">Buena</option>
              <option value="regular">Regular</option>
              <option value="mala">Mala</option>
              <option value="muy mala">Muy mala</option>
            </select>
          </div>
          <div>
            <label for="nota_modal" class="block">Nota:</label>
            <textarea
              id="nota_modal"
              v-model="form.nota"
              class="border p-2 rounded w-full"
            ></textarea>
          </div>
          <div class="flex justify-end space-x-2">
            <button
              type="button"
              @click="cerrarModalActualizarSesion"
              class="bg-red-500 text-white px-4 py-2 mt-6 rounded-md"
            >
              Cancelar
            </button>
            <button  type="submit" class="bg-gradient-to-r from-slate-800 via-slate-800 to-slate-800 hover:bg-gradient-to-br focus:ring-4 focus:outline-none text-white px-4 py-2 mt-6 rounded-md">
              Actualizar Sesión
            </button>
          </div>
        </form>
      </div>
    </div>

  </DashboardLayout>
</template>

<style>


</style>