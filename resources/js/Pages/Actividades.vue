<script setup>
import { ref, computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import DashboardLayout from '@/Components/DashboardLayout.vue'
import { defineProps } from 'vue'

const { actividades, pacientes, flash } = defineProps({
  actividades: { type: Array, required: true },
  pacientes: { type: Array, required: true },
  flash: { type: Object, default: () => ({}) },
})

const id_seleccionado = ref(null)


const actividadesFiltradas = computed(() => {
  if (!id_seleccionado.value) return []
  return actividades.filter(a => a.id_paciente === id_seleccionado.value)
})


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
  nombre: '',
  descripcion: '',
  fecha_asignacion: '',
  fecha_limite: '',
  estado: 'pendiente',
  id_paciente: null,
})


const enviarActividad = () => {
  if (!id_seleccionado.value || !form.value.nombre || !form.value.descripcion || !form.value.fecha_asignacion) {
    alert('Por favor selecciona un paciente y completa los campos requeridos.')
    return
  }

  form.value.id_paciente = id_seleccionado.value

 

  Inertia.post('/actividades', form.value, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      alert('Actividad guardada exitosamente.')
      form.value = { nombre: '', descripcion: '', fecha_asignacion: '', fecha_limite: '', estado: 'pendiente', id_paciente: null }
      id_seleccionado.value = null
    },
    onError: (errors) => {
      console.error('Errores:', errors)
      alert('Error al crear la actividad. Revisa la consola.')
    },
  })
}


const modalActualizarActividad = ref(false)
const current_actividad_id = ref(null)

function abrirModalActualizarActividadPorId(id_actividad) {
  const actividad = actividades.find(a => a.id_actividad === id_actividad)
  if (actividad) {
    form.value = { ...actividad }
    id_seleccionado.value = actividad.id_paciente
    modalActualizarActividad.value = true
    current_actividad_id.value = id_actividad
  } else {
    console.error('Actividad no encontrada con ID:', id_actividad)
  }
}

const actualizarActividad = () => {
  if (!current_actividad_id.value) {
    alert('No se ha seleccionado una actividad para actualizar.')
    return
  }

  

  Inertia.put(`/actividades/${current_actividad_id.value}`, form.value, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      alert('Actividad actualizada exitosamente.')
      form.value = { 
        nombre: '', 
        descripcion: '', 
        fecha_asignacion: '', 
        fecha_limite: '', 
        estado: 'pendiente', 
        id_paciente: null }
      id_seleccionado.value = null
      cerrarModalActualizarActividad()
    },
    onError: (errors) => {
      console.error('Errores:', errors)
      alert('Error al actualizar la actividad. Revisa la consola.')
    },
  })
}

function cerrarModalActualizarActividad() {
  modalActualizarActividad.value = false
  form.value = { nombre: '', descripcion: '', fecha_asignacion: '', fecha_limite: '', estado: 'pendiente', id_paciente: null }
  current_actividad_id.value = null
}


function borrarActividad(id_actividad) {
  if (!confirm('¿Estás seguro de borrar esta actividad?')) {
    return
  }
  Inertia.delete(`/actividades/${id_actividad}`, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      alert('Actividad eliminada exitosamente.')
    },
    onError: (errors) => {
      console.error('Errores:', errors)
    },
  })
}
</script>

<template>
  <DashboardLayout>
    <div class="p-6 bg-gray-100 min-h-screen">
      <h1 class="text-2xl font-bold mb-4 text-center">Actividades</h1>

      <div class="mt-6">
        <label for="nombrebuscado" class="block text-gray-700 font-semibold text-sm md:text-base">Buscar paciente:</label>
        <input v-model="nombreBuscado" type="search" id="nombrebuscado" class="w-full p-2 border rounded-md focus:ring focus:ring-green-400 text-sm" placeholder="Escribe el nombre..." />

        <div class="mt-4 space-y-2">
          <div v-for="paciente in pacientesFiltrados" :key="paciente.id_paciente" @click="idSeleccionado(paciente)" class="bg-white p-3 rounded-lg shadow flex justify-between items-center text-sm sm:text-base cursor-pointer hover:bg-gray-100 transition">
            <span class="text-green-700 hover:underline">
              {{ paciente.nombre }} {{ paciente.apellido }}
            </span>
          </div>
        </div>
      </div>

      <!-- Tabla en escritorio -->
      <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full bg-white border mt-8 shadow-md rounded-lg">
          <thead class="bg-[#315A4F] text-white">
            <tr>
              <th class="py-3 px-4">Nombre</th>
              <th class="py-3 px-4">Descripción</th>
              <th class="py-3 px-4">Fecha Asignación</th>
              <th class="py-3 px-4">Estado</th>
              <th class="py-3 px-4">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="actividad in actividadesFiltradas" :key="actividad.id_actividad" class="border-b hover:bg-gray-50">
              <td class="py-3 px-4">{{ actividad.nombre }}</td>
              <td class="py-3 px-4">{{ actividad.descripcion }}</td>
              <td class="py-3 px-4">{{ actividad.fecha_asignacion }}</td>
              <td class="py-3 px-4">{{ actividad.estado }}</td>
              <td class="py-3 px-4 flex space-x-2">
                <button @click="abrirModalActualizarActividadPorId(actividad.id_actividad)" class="bg-yellow-500 text-white px-3 py-1 rounded">
                  Editar
                </button>
                <button @click="borrarActividad(actividad.id_actividad)" class="bg-red-500 text-white px-3 py-1 rounded">
                  Borrar
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Tarjetas en moviles -->
      <div class="md:hidden mt-4 space-y-4">
        <div v-for="actividad in actividadesFiltradas" :key="actividad.id_actividad" class="bg-white p-4 rounded-lg shadow-md">
          <h3 class="text-lg font-bold text-gray-800">{{ actividad.nombre }}</h3>
          <p class="text-sm text-gray-600">{{ actividad.descripcion }}</p>
          <p class="text-sm"><strong>Fecha Asignación:</strong> {{ actividad.fecha_asignacion }}</p>
          <p class="text-sm"><strong>Estado:</strong> {{ actividad.estado }}</p>
          <div class="flex space-x-2 mt-2">
            <button @click="abrirModalActualizarActividadPorId(actividad.id_actividad)" class="w-full px-3 py-1 rounded bg-yellow-500 text-white">
              Editar
            </button>
            <button @click="borrarActividad(actividad.id_actividad)" class="w-full px-3 py-1 rounded bg-red-500 text-white">
              Borrar
            </button>
          </div>
        </div>
      </div>

      <!-- Formulario -->
      <div class="mt-8 max-w-lg mx-auto bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold text-gray-900 mb-4 text-center">Crear Actividad</h2>

        <form @submit.prevent="enviarActividad" class="space-y-4">
          <label class="block font-semibold text-sm text-gray-700">Nombre de la actividad</label>
          <input v-model="form.nombre" type="text" class="w-full p-2 border rounded-md focus:ring focus:ring-green-400 text-sm" placeholder="Descripcion de la actividad" required />
          <label class="block font-semibold text-sm text-gray-700">Descripción de la actividad</label>
          <input v-model="form.descripcion" type="text" class="w-full p-2 border rounded-md focus:ring focus:ring-green-400 text-sm" placeholder="Descripción" required />
          <label class="block font-semibold text-sm text-gray-700">Fecha de asignación</label>
          <input v-model="form.fecha_asignacion" type="date" class="w-full p-2 border rounded-md focus:ring focus:ring-green-400 text-sm" required />
          <label class="block font-semibold text-sm text-gray-700">Fecha limte</label>
          <input v-model="form.fecha_limite" type="date" class="w-full p-2 border rounded-md focus:ring focus:ring-green-400 text-sm" />

          <button type="submit" class="w-full px-4 py-2 rounded-md bg-gradient-to-r from-slate-800 via-slate-800 to-slate-800 hover:bg-gradient-to-br text-white">
            Guardar Actividad
          </button>
        </form>
      </div>

      <!-- Modal para actualizar actividad -->
      <div v-if="modalActualizarActividad" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center p-4">
        <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-lg">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Actualizar Actividad</h2>
          <form @submit.prevent="actualizarActividad" class="space-y-4">
            <input v-model="form.nombre" type="text" class="w-full p-2 border rounded-md focus:ring focus:ring-green-400 text-sm" placeholder="Nombre de la Actividad" required />
            <input v-model="form.descripcion" type="text" class="w-full p-2 border rounded-md focus:ring focus:ring-green-400 text-sm" placeholder="Descripción" required />
            <input v-model="form.fecha_asignacion" type="date" class="w-full p-2 border rounded-md focus:ring focus:ring-green-400 text-sm" required />
            <input v-model="form.fecha_limite" type="date" class="w-full p-2 border rounded-md focus:ring focus:ring-green-400 text-sm" />
            
            <select v-model="form.estado" class="w-full p-2 border rounded-md focus:ring focus:ring-green-400 text-sm" required>
              <option value="pendiente">Pendiente</option>
              <option value="completado">Completado</option>
              <option value="cancelado">Cancelado</option>
            </select>

            <div class="flex flex-col sm:flex-row gap-4 mt-4">
              <button type="button" @click="cerrarModalActualizarActividad" class="w-full sm:w-auto px-4 py-2 rounded-md bg-red-500 text-white hover:bg-red-600">
                Cancelar
              </button>
              <button type="submit" class="w-full sm:w-auto px-4 py-2 rounded-md bg-gradient-to-r from-slate-800 via-slate-800 to-slate-800 hover:bg-gradient-to-br text-white">
                Actualizar Actividad
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>
