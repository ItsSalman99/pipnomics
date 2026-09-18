<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
  name: '',
  description: '',
});

const submit = () => {
  form.post(route('groups.store'));
};
</script>

<template>
  <Head title="Create Forum Group" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-2xl font-bold tracking-tight text-white">Create a New Group</h2>
      <p class="text-xs text-slate-400 mt-1">Initialize a new discussion thread and build your trading community forum.</p>
    </template>
    
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-3xl mx-auto">
      <div class="bg-slate-900 border border-slate-800/80 rounded-xl p-6 shadow-lg">
        <form @submit.prevent="submit" class="space-y-5">
          <div>
            <InputLabel for="name" value="Group Name" class="text-xs font-bold text-slate-300 uppercase tracking-wider mb-2" />
            <TextInput 
              id="name" 
              type="text" 
              class="mt-1 block w-full bg-slate-950 text-slate-200 border-slate-800 focus:border-cyan-500/50 focus:ring-cyan-500/50 rounded-lg text-sm" 
              v-model="form.name" 
              required 
              autofocus 
              placeholder="e.g. Gold Price Action Discussions"
            />
            <InputError class="mt-2" :message="form.errors.name" />
          </div>
          
          <div>
            <InputLabel for="description" value="Description / Rules" class="text-xs font-bold text-slate-300 uppercase tracking-wider mb-2" />
            <textarea 
              id="description" 
              class="bg-slate-950 text-slate-200 border-slate-800 focus:border-cyan-500/50 focus:ring-cyan-500/50 rounded-lg shadow-sm mt-1 block w-full text-sm leading-relaxed" 
              v-model="form.description" 
              rows="6"
              placeholder="Describe what currency pairs or analysis topics this forum is for..."
            ></textarea>
            <InputError class="mt-2" :message="form.errors.description" />
          </div>
          
          <div class="flex items-center justify-end pt-4 border-t border-slate-950/30">
            <PrimaryButton 
              :class="{ 'opacity-25': form.processing }" 
              :disabled="form.processing"
              class="bg-gradient-to-r from-emerald-500 to-cyan-500 hover:from-emerald-400 hover:to-cyan-400 text-slate-955 px-5 py-2.5 rounded-lg font-bold text-xs shadow-md border-0 uppercase tracking-wider hover:scale-[1.02] transition"
            >
              Create Group
            </PrimaryButton>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
