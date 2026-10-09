<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    href: {
        type: String,
        default: '#'
    },
    active: {
        type: Boolean,
        default: false
    },
    onClick: Function
})
</script>

<template>
  <li class="h-full flex items-center">
    <Link
      :href="href"
      :class="[
        'h-full px-2 xl:px-3.5 2xl:px-4 py-5 flex items-center justify-center border-b-2 transition-all duration-200 text-xs xl:text-sm 2xl:text-[15px] font-bold tracking-tight whitespace-nowrap gap-1 focus:outline-none',
        active 
          ? 'border-primary text-primary font-extrabold' 
          : 'border-transparent text-slate-800 hover:text-primary hover:border-primary/30'
      ]"
      @click="onClick"
    >
      <slot/>
    </Link>
  </li>
</template>

