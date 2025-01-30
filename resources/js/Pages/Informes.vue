<script setup>
import { ref, computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import DashboardLayout from '@/Components/DashboardLayout.vue'
import { defineProps } from 'vue'

const { informes, pacientes, terapeutas } = defineProps({
  informes: { type: Array, required: true },
  pacientes: { type: Array, required: true },
  terapeutas: { type: Array, required: true }
})

const form = ref({
  fecha_informe: '',
  resumen: '',
  id_paciente: null,
  id_terapeuta: null
})

const modalActualizarInforme = ref(false)
const current_informe_id = ref(null)

const enviarInforme = () => {
  if (!form.value.id_paciente || !form.value.id_terapeuta) {
    alert('Debes seleccionar un paciente y un terapeuta antes de enviar el informe.')
    return
  }

  Inertia.post('/informes', form.value, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      alert('Informe creado exitosamente.')
      form.value = { fecha_informe: '', resumen: '', id_paciente: null, id_terapeuta: null }
    },
    onError: (errors) => {
      console.error('Errores en validación:', errors)
    }
  })
}

const abrirModalActualizarInforme = (id_informe) => {
  const informe = informes.find(i => i.id_informe === id_informe)
  if (informe) {
    form.value = { ...informe }
    modalActualizarInforme.value = true
    current_informe_id.value = id_informe
  }
}

const actualizarInforme = () => {
  Inertia.put(`/informes/${current_informe_id.value}`, form.value, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      alert('Informe actualizado exitosamente.')
      form.value = { fecha_informe: '', resumen: '', id_paciente: null, id_terapeuta: null }
      modalActualizarInforme.value = false
    }
  })
}

const borrarInforme = (id_informe) => {
  if (!confirm('¿Estás seguro de borrar este informe?')) return

  Inertia.delete(`/informes/${id_informe}`, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => alert('Informe eliminado exitosamente.')
  })
}



</script>

<template>
  <DashboardLayout>
    <div class="p-6 bg-gray-100 min-h-screen">
      <h1 class="text-2xl font-bold mb-4 text-center">Informes</h1>

      <!-- Formulario -->
      <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold text-gray-900 mb-4 text-center">Crear Informe</h2>

        <form @submit.prevent="enviarInforme" class="space-y-4">
          <input v-model="form.fecha_informe" type="date" class="w-full p-2 border rounded-md focus:ring focus:ring-green-400 text-sm" required />

          <textarea 
            v-model="form.resumen" 
            class="w-full p-2 border rounded-md focus:ring focus:ring-green-400 text-sm" 
            placeholder="Resumen del informe" 
            required
            @input="form.resumen = form.resumen.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim().replace(/[^a-z\s]/g, '')"
          ></textarea>

          <select v-model="form.id_paciente" class="w-full p-2 border rounded-md focus:ring focus:ring-green-400 text-sm" required>
            <option value="" disabled>Selecciona un paciente</option>
            <option v-for="paciente in pacientes" :key="paciente.id_paciente" :value="paciente.id_paciente">
              {{ paciente.nombre }} {{ paciente.apellido }}
            </option>
          </select>

          <select v-model="form.id_terapeuta" class="w-full p-2 border rounded-md focus:ring focus:ring-green-400 text-sm" required>
            <option value="" disabled>Selecciona un terapeuta</option>
            <option v-for="terapeuta in terapeutas" :key="terapeuta.id_terapeuta" :value="terapeuta.id_terapeuta">
              {{ terapeuta.nombre }} {{ terapeuta.apellido }}
            </option>
          </select>

          <button type="submit" class="w-full px-4 py-2 rounded-md bg-gradient-to-r from-slate-800 via-slate-800 to-slate-800 hover:bg-gradient-to-br text-white">
            Guardar Informe
          </button>
        </form>
      </div>







      <!-- Tabla en escritorio -->
      <div class="hidden md:block overflow-x-auto mt-8">
        <table class="min-w-full bg-white border shadow-md rounded-lg">
          <thead class="bg-[#315A4F] text-white">
            <tr>
              <th class="py-3 px-4">Fecha</th>
              <th class="py-3 px-4">Resumen</th>
              <th class="py-3 px-4">Paciente</th>
              <th class="py-3 px-4">Terapeuta</th>
              <th class="py-3 px-4">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="informe in informes" :key="informe.id_informe" class="border-b hover:bg-gray-50">
              <td class="py-3 px-4">{{ informe.fecha_informe }}</td>
              <td class="py-3 px-4">{{ informe.resumen }}</td>
              <td class="py-3 px-4">{{ informe.paciente?.nombre || 'Desconocido' }}</td>
              <td class="py-3 px-4">{{ informe.terapeuta?.nombre || 'Desconocido' }}</td>
              <td class="py-3 px-4 flex space-x-2">
                <button @click="abrirModalActualizarInforme(informe.id_informe)" class="bg-yellow-500 text-white px-3 py-1 rounded">
                  Editar
                </button>
                <button @click="borrarInforme(informe.id_informe)" class="bg-red-500 text-white px-3 py-1 rounded">
                  Borrar
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Tarjetas responsive -->
      <div class="md:hidden mt-4 space-y-4">
        <div v-for="informe in informes" :key="informe.id_informe" class="bg-white p-4 rounded-lg shadow-md">
          <h3 class="text-lg font-bold text-gray-800">Fecha: {{ informe.fecha_informe }}</h3>
          <p class="text-sm"><strong>Resumen:</strong> {{ informe.resumen }}</p>
          <p class="text-sm"><strong>Paciente:</strong> {{ informe.paciente?.nombre || 'Desconocido' }}</p>
          <p class="text-sm"><strong>Terapeuta:</strong> {{ informe.terapeuta?.nombre || 'Desconocido' }}</p>
          <div class="flex space-x-2 mt-2">
            <button @click="abrirModalActualizarInforme(informe.id_informe)" class="w-full px-3 py-1 rounded bg-yellow-500 text-white">
              Editar
            </button>
            <button @click="borrarInforme(informe.id_informe)" class="w-full px-3 py-1 rounded bg-red-500 text-white">
              Borrar
            </button>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

