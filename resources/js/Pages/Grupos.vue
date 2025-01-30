<script setup>
import { ref, computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import DashboardLayout from '@/Components/DashboardLayout.vue'
import { defineProps } from 'vue'

const { grupos, terapeutas, pacientes } = defineProps({
  grupos: { type: Array, required: true },
  terapeutas: { type: Array, required: true },
  pacientes: { type: Array, required: true }
})

const form = ref({
  nombre: '',
  descripcion: '',
  fecha_inicio: '',
  estado: 'activo',
  terapeutas: [],
  pacientes: []
})

const modalActualizarGrupo = ref(false)
const current_grupo_id = ref(null)

const enviarGrupo = () => {
  if (!form.value.nombre || !form.value.fecha_inicio) {
    alert('Completa los campos requeridos.')
    return
  }

  Inertia.post('/grupos', form.value, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      alert('Grupo creado exitosamente.')
      form.value = { 
        nombre: '', 
        descripcion: '', 
        fecha_inicio: '', 
        estado: 'activo', 
        terapeutas: [], 
        pacientes: [] }
    },
    onError: (errors) => {
      console.error('Errores:', errors)
      alert('Error al crear el grupo.')
    },
  })
}

const abrirModalActualizarGrupoPorId = (id_grupo) => {
  const grupo = grupos.find(g => g.id_grupo === id_grupo)
  if (grupo) {
    form.value = {
      ...grupo,
      terapeutas: Array.isArray(grupo.terapeutas) ? grupo.terapeutas.map(t => t.id_terapeuta) : [],
      pacientes: Array.isArray(grupo.pacientes) ? grupo.pacientes.map(p => p.id_paciente) : []
    }
    modalActualizarGrupo.value = true
    current_grupo_id.value = id_grupo
  }
}

const actualizarGrupo = () => {
  if (!current_grupo_id.value) {
    alert('No se ha seleccionado un grupo para actualizar.')
    return
  }

  Inertia.put(`/grupos/${current_grupo_id.value}`, form.value, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      alert('Grupo actualizado exitosamente.')
      form.value = { 
        nombre: '', 
        descripcion: '', 
        fecha_inicio: '', 
        estado: '', 
        terapeutas: []
        ,pacientes: [] }
      modalActualizarGrupo.value = false
    },
    onError: (errors) => {
      console.error('Errores:', errors)
      alert('Error al actualizar el grupo.')
    },
  })
}

const cerrarModalActualizarGrupo = () => {
  modalActualizarGrupo.value = false
  form.value = { 
  nombre: '', 
  descripcion: '', 
  fecha_inicio: '', 
  estado: 'activo', 
  terapeutas: [], 
  pacientes: [] }
  current_grupo_id.value = null
}

const borrarGrupo = (id_grupo) => {
  if (!confirm('¿Estás seguro de borrar este grupo?')) {
    return
  }
  Inertia.delete(`/grupos/${id_grupo}`, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      alert('Grupo eliminado exitosamente.')
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
      <h1 class="text-2xl font-bold mb-4 text-center">Grupos</h1>

      <!-- Tabla en pantallas grandes -->
      <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full bg-white border mt-8 shadow-md rounded-lg">
          <thead class="bg-[#315A4F] text-white">
            <tr>
              <th class="py-3 px-4">Nombre</th>
              <th class="py-3 px-4">Descripción</th>
              <th class="py-3 px-4">Fecha Inicio</th>
              <th class="py-3 px-4">Estado</th>
              <th class="py-3 px-4">Pacientes</th>
              <th class="py-3 px-4">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="grupo in grupos" :key="grupo.id_grupo" class="border-b hover:bg-gray-50">
              <td class="py-3 px-4">{{ grupo.nombre }}</td>
              <td class="py-3 px-4">{{ grupo.descripcion }}</td>
              <td class="py-3 px-4">{{ grupo.fecha_inicio }}</td>
              <td class="py-3 px-4">  {{ grupo.estado === true || grupo.estado === 'activo' ? 'Activo' : 'Inactivo' }}</td>
              <td class="py-3 px-4">
                <ul v-if="Array.isArray(grupo.pacientes) && grupo.pacientes.length">
                  <li v-for="paciente in grupo.pacientes" :key="paciente.id_paciente">
                    {{ paciente.nombre }} {{ paciente.apellido }}
                  </li>
                </ul>
                <span v-else>No hay pacientes asignados</span>
              </td>
              <td class="py-3 px-4 flex space-x-2">
                <button @click="abrirModalActualizarGrupoPorId(grupo.id_grupo)" class="bg-yellow-500 text-white px-3 py-1 rounded">
                  Editar
                </button>
                <button @click="borrarGrupo(grupo.id_grupo)" class="bg-red-500 text-white px-3 py-1 rounded">
                  Borrar
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Tarjetas en moviles -->
      <div class="md:hidden mt-4 space-y-4">
        <div v-for="grupo in grupos" :key="grupo.id_grupo" class="bg-white p-4 rounded-lg shadow-md">
          <h3 class="text-lg font-bold text-gray-800">{{ grupo.nombre }}</h3>
          <p class="text-sm text-gray-600">{{ grupo.descripcion }}</p>
          <p class="text-sm"><strong>Fecha Inicio:</strong> {{ grupo.fecha_inicio }}</p>
          <p class="text-sm"><strong>Estado:</strong> {{ grupo.estado }}</p>
          <div>
            <p class="text-sm font-semibold">Pacientes:</p>
            <ul v-if="Array.isArray(grupo.pacientes) && grupo.pacientes.length">
              <li v-for="paciente in grupo.pacientes" :key="paciente.id_paciente" class="text-sm">
                {{ paciente.nombre }} {{ paciente.apellido }}
              </li>
            </ul>
            <span v-else class="text-sm">No hay pacientes asignados</span>
          </div>
          <div class="flex space-x-2 mt-2">
            <button @click="abrirModalActualizarGrupoPorId(grupo.id_grupo)" class="w-full px-3 py-1 rounded bg-yellow-500 text-white">
              Editar
            </button>
            <button @click="borrarGrupo(grupo.id_grupo)" class="w-full px-3 py-1 rounded bg-red-500 text-white">
              Borrar
            </button>
          </div>
        </div>
      </div>

      <!-- Formulario -->
      <div class="mt-8 max-w-full  mx-auto bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold text-gray-900 mb-4 text-center">Crear Grupo</h2>

        <form @submit.prevent="enviarGrupo" class="space-y-4">
          <input v-model="form.nombre" type="text" class="w-full p-2 border rounded-md focus:ring focus:ring-green-400 text-sm" placeholder="Nombre del Grupo" required />
          <input v-model="form.descripcion" type="text" class="w-full p-2 border rounded-md focus:ring focus:ring-green-400 text-sm" placeholder="Descripción" />
          <input v-model="form.fecha_inicio" type="date" class="w-full p-2 border rounded-md focus:ring focus:ring-green-400 text-sm" required />
          
          <select v-model="form.estado" class="w-full p-2 border rounded-md focus:ring focus:ring-green-400 text-sm">
            <option value="activo">Activo</option>
            <option value="inactivo">Inactivo</option>
          </select>

          <div>
            <label class="block font-semibold text-sm text-gray-700">Seleccionar Terapeutas</label>
            <select v-model="form.terapeutas" multiple class="w-full p-2 border rounded-md focus:ring focus:ring-green-400 text-sm">
              <option v-for="terapeuta in terapeutas" :key="terapeuta.id_terapeuta" :value="terapeuta.id_terapeuta">
                {{ terapeuta.nombre }} {{ terapeuta.apellido }}
              </option>
            </select>
          </div>

          <div>
            <label class="block font-semibold text-sm text-gray-700">Seleccionar Pacientes</label>
            <select v-model="form.pacientes" multiple class="w-full p-2 border rounded-md focus:ring focus:ring-green-400 text-sm">
              <option v-for="paciente in pacientes" :key="paciente.id_paciente" :value="paciente.id_paciente">
                {{ paciente.nombre }} {{ paciente.apellido }}
              </option>
            </select>
          </div>

          <button type="submit" class="mx-auto block px-4 py-2 rounded-md bg-gradient-to-r from-slate-800 via-slate-800 to-slate-800 hover:bg-gradient-to-br text-white">
            Guardar Grupo
          </button>
        </form>
      </div>
    </div>
  </DashboardLayout>
</template>
