<script setup>
import DashboardLayout from '@/Components/DashboardLayout.vue';
import { defineProps, ref, computed } from 'vue';
import { Inertia } from '@inertiajs/inertia';

const { terapeutas } = defineProps({

    terapeutas: {
        type: Array,
        required: true
    }

});

const form = ref({
    nombre: '',
    apellido: '',
    password: '',
    email: '',
    role: 'terapeuta'



});


// Funcion para crear terapeuta
const crearTerapeuta = () => {
  if (!validarFormulario()) return;

  const data = { ...form.value, role: 'terapeuta' };

  Inertia.post('/terapeutas', data, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      alert('Terapeuta creado exitosamente.');
      form.value = {
       nombre: '',
       apellido: '',
       email: '',
       password: '',
       role: 'terapeuta' };
    },
    onError: (errors) => {
      console.error('Errores:', errors);
    },
  });
};



// Buscar Terapeuta
const nombreBuscado = ref('');
const terapeutasFiltrados = computed(() => {
  if (!nombreBuscado.value.trim()) {
    return [];
  }
  return terapeutas.filter((terapeuta) =>
    terapeuta.nombre?.toLowerCase().includes(nombreBuscado.value.trim().toLowerCase())
  );
});

// Terapeuta por ID
const id_seleccionado = ref(null);
function idSeleccionado(terapeuta) {
  id_seleccionado.value = terapeuta.id_terapeuta;
}

const terapeutaEncontrado = computed(() =>
  terapeutas.find((p) => p.id_terapeuta === id_seleccionado.value)
);


//Borrar terapeuta 
const borrarTerapeuta = () => {
  if (id_seleccionado.value) {
    Inertia.delete(`/terapeutas/${id_seleccionado.value}`, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        alert('Terapeuta eliminado exitosamente.');
      },
      onError: (errors) => {
        console.error('Errores:', errors);
      },
    });
  } else {
    console.error('No se ha seleccionado un terapeuta para eliminar.');
  }
};

const actualizarTerapeuta = () => {
  if (!validarFormulario()) return;

  Inertia.put(`/terapeutas/${id_seleccionado.value}`, form.value, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      alert('Terapeuta actualizado exitosamente.');
      form.value = {
           nombre: '',
           apellido: '',
           email: '',
           password: '',
           role: 'terapeuta' };
      id_seleccionado.value = null;
      cerrarModalActualizarTerapeuta();
    },
    onError: (errors) => {
      console.error('Errores:', errors);
    },
  });
};

const modalActualizarTerapeuta = ref(false);

function abrirModalActualizarTerapeuta(){
  form.value = { ...terapeutaEncontrado.value };
  modalActualizarTerapeuta.value = true;
}

function cerrarModalActualizarTerapeuta(){
  modalActualizarTerapeuta.value = false;
}


const errores = ref({});


const validarFormulario = () => {
  errores.value = {}; 

 
  if (!/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/.test(form.value.nombre) || form.value.nombre.trim().length < 2) {
    errores.value.nombre = 'El nombre debe contener solo letras y al menos 2 caracteres.';
  }
  if (!/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/.test(form.value.apellido) || form.value.apellido.trim().length < 2) {
    errores.value.apellido = 'El apellido debe contener solo letras y al menos 2 caracteres.';
  }

  
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.value.email)) {
    errores.value.email = 'Ingrese un correo válido.';
  }

  
  if (form.value.password.length < 6) {
    errores.value.password = 'La contraseña debe tener al menos 6 caracteres.';
  }

  return Object.keys(errores.value).length === 0;
};




</script>


<template>
  <DashboardLayout>
    <div class="p-6 bg-gray-100 min-h-screen flex flex-col items-center">
      <div class="w-full max-w-xl bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-xl font-bold text-gray-900 mb-4 text-center">Crear Terapeuta</h2>

        <form @submit.prevent="crearTerapeuta" class="space-y-4">
          <div>
            <label for="nombre" class="block text-gray-700 font-medium text-sm md:text-base">Nombre</label>
            <input type="text" id="nombre" v-model="form.nombre" class="w-full p-2 border rounded-md focus:ring focus:ring-green-400 text-sm" />
            <span v-if="errores.nombre" class="text-red-500 text-sm">{{ errores.nombre }}</span>
          </div>

          <div>
            <label for="apellido" class="block text-gray-700 font-medium text-sm md:text-base">Apellido</label>
            <input type="text" id="apellido" v-model="form.apellido" class="w-full p-2 border rounded-md focus:ring focus:ring-green-400 text-sm" />
            <span v-if="errores.apellido" class="text-red-500 text-sm">{{ errores.apellido }}</span>
          </div>

          <div>
            <label for="correo" class="block text-gray-700 font-medium text-sm md:text-base">Correo</label>
            <input type="email" id="correo" v-model="form.email" class="w-full p-2 border rounded-md focus:ring focus:ring-green-400 text-sm" />
            <span v-if="errores.email" class="text-red-500 text-sm">{{ errores.email }}</span>
          </div>

          <div>
            <label for="contraseña" class="block text-gray-700 font-medium text-sm md:text-base">Contraseña</label>
            <input type="password" id="contraseña" v-model="form.password" class="w-full p-2 border rounded-md focus:ring focus:ring-green-400 text-sm" />
            <span v-if="errores.password" class="text-red-500 text-sm">{{ errores.password }}</span>
          </div>

          <button type="submit" class="w-full px-4 py-2 rounded-md bg-gradient-to-r from-slate-800 via-slate-800 to-slate-800 hover:bg-gradient-to-br text-white">
            Crear Terapeuta
          </button>
        </form>
      </div>

      <div class="w-full max-w-xl mt-8">
        <h2 class="text-xl font-bold text-gray-900 mb-4 text-center">Lista de Terapeutas</h2>

        <div class="mt-6">
          <label for="nombrebuscado" class="block text-gray-700 font-semibold text-sm md:text-base">Buscar terapeuta:</label>
          <input v-model="nombreBuscado" type="search" id="nombrebuscado" class="w-full p-2 border rounded-md focus:ring focus:ring-green-400 text-sm" placeholder="Escribe el nombre..." />
        </div>

        <div class="mt-4 space-y-2">
          <div v-for="terapeuta in terapeutasFiltrados" :key="terapeuta.id_terapeuta" 
            @click="idSeleccionado(terapeuta)" 
            class="bg-white p-3 rounded-lg shadow flex justify-between items-center text-sm sm:text-base cursor-pointer hover:bg-gray-100 transition">
            <span class="text-green-700 hover:underline">
              {{ terapeuta.nombre }} {{ terapeuta.apellido }}
            </span>
          </div>
        </div>
      </div>

      <div class="w-full max-w-xl mt-8 text-center">
        <span class="text-gray-700 font-medium">Terapeuta seleccionado: {{ terapeutaEncontrado?.nombre || 'Ninguno' }}</span>
      </div>

      <div class="flex flex-col sm:flex-row gap-4 mt-6 w-full max-w-xl">
        
        <button @click="abrirModalActualizarTerapeuta" class="mx-auto block w-full sm:w-auto px-4 py-2 rounded-md bg-orange-500 text-white hover:bg-orange-600">
          Actualizar Terapeuta
        </button>

        <button @click="borrarTerapeuta" class="mx-auto block w-full sm:w-auto px-4 py-2 rounded-md bg-red-500 text-white hover:bg-red-600">
          Borrar Terapeuta
        </button>
      </div>

      <div v-if="modalActualizarTerapeuta" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center p-4">
        <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-lg">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Actualizar Terapeuta</h2>
          <form @submit.prevent="actualizarTerapeuta" class="space-y-4">
            <div>
              <label for="nombre" class="block text-gray-700 font-medium text-sm md:text-base">Nombre</label>
              <input type="text" id="nombre" v-model="form.nombre" class="w-full p-2 border rounded-md focus:ring focus:ring-green-400 text-sm" />
              <span v-if="errores.nombre" class="text-red-500 text-sm">{{ errores.nombre }}</span>
            </div>

            <div>
              <label for="apellido" class="block text-gray-700 font-medium text-sm md:text-base">Apellido</label>
              <input type="text" id="apellido" v-model="form.apellido" class="w-full p-2 border rounded-md focus:ring focus:ring-green-400 text-sm" />
              <span v-if="errores.apellido" class="text-red-500 text-sm">{{ errores.apellido }}</span>
            </div>

            <div>
              <label for="correo" class="block text-gray-700 font-medium text-sm md:text-base">Correo</label>
              <input type="email" id="correo" v-model="form.email" class="w-full p-2 border rounded-md focus:ring focus:ring-green-400 text-sm" />
              <span v-if="errores.email" class="text-red-500 text-sm">{{ errores.email }}</span>
            </div>

            <div>
              <label for="password" class="block text-gray-700 font-medium text-sm md:text-base">password</label>
              <input type="password" id="password" v-model="form.password" class="w-full p-2 border rounded-md focus:ring focus:ring-green-400 text-sm" />
              <span v-if="errores.password" class="text-red-500 text-sm">{{ errores.password }}</span>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 mt-4">
              <button type="button" @click="cerrarModalActualizarTerapeuta" class="w-full sm:w-auto px-4 py-2 rounded-md bg-red-500 text-white hover:bg-red-600">
                Cancelar
              </button>
              <button type="submit" class="w-full sm:w-auto px-4 py-2 rounded-md bg-gradient-to-r from-slate-800 via-slate-800 to-slate-800 hover:bg-gradient-to-br text-white">
                Actualizar Terapeuta
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>
