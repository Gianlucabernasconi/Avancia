<script setup>

import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-[#315A4F] pt-6 sm:justify-center sm:pt-0"
    >
        <div>
           
            <Link href="/login">
                <svg 
    class="mr-2 h-20 w-20 fill-current" 
    viewBox="0 0 24 24" 
    fill="none" 
    xmlns="http://www.w3.org/2000/svg"
    aria-hidden="true"
    focusable="false"
  >
    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
    <g id="SVGRepo_iconCarrier"> 
      <path fill-rule="evenodd" clip-rule="evenodd" d="M12 22C7.58172 22 4 18.3541 4 13.8567C4 9.39453 6.55332 4.18759 10.5371 2.32555C11.0014 2.10852 11.5007 2 12 2V22Z" fill="#ffffff"></path> 
      <path opacity="0.3" d="M13.4629 2.32555C12.9986 2.10852 12.4993 2 12 2V9.00006L16.4317 4.56841C15.5689 3.62059 14.5713 2.84361 13.4629 2.32555Z" fill="#ffffff"></path> 
      <path opacity="0.4" d="M12 9.00007V14.5001L18.6142 7.88578C18.0421 6.6656 17.3062 5.52923 16.4317 4.56842L12 9.00007Z" fill="#ffffff"></path> 
      <path opacity="0.6" d="M12 19.5L19.811 11.689C19.5911 10.3986 19.1838 9.10061 18.6142 7.88574L12 14.5V19.5Z" fill="#ffffff"></path> 
      <path opacity="0.7" d="M19.811 11.689L12 19.5V22C16.4183 22 20 18.3541 20 13.8567C20 13.1475 19.9355 12.4194 19.811 11.689Z" fill="#ffffff"></path> 
    </g>
  </svg>
  
            </Link>
           
        </div>
        <h1 class="mb-5 font-bold text-2xl  text-white ">Avancia</h1>

        <div
            class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg dark:bg-gray-800"
        >
            <slot />
        </div>
    </div>
</template>
