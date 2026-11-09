<script setup>
import { computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    users: Array,
    establecimientos: Array,
    flash: Object,
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    establecimiento_id: '',
});

const establecimientoOptions = computed(() =>
    (props.establecimientos ?? []).map((establecimiento) => ({
        value: String(establecimiento.id),
        label: establecimiento.codigo
            ? `${establecimiento.codigo} - ${establecimiento.nombre}`
            : establecimiento.nombre,
    })),
);

const createUser = () => {
    form.post(route('admin.users.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <AppLayout title="Usuarios">
        <Head title="Usuarios" />

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Administración de usuarios
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    v-if="flash?.success"
                    class="mb-6 rounded-md bg-green-50 p-4 text-sm text-green-700"
                >
                    {{ flash.success }}
                </div>

                <FormSection @submitted="createUser">
                    <template #title>
                        Crear usuario
                    </template>

                    <template #description>
                        Solo los administradores pueden crear cuentas. El usuario podrá iniciar sesión con el correo y la contraseña definidos aquí.
                    </template>

                    <template #form>
                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="name" value="Nombre" />
                            <TextInput
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="mt-1 block w-full"
                                required
                                autocomplete="name"
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="email" value="Correo electrónico" />
                            <TextInput
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="mt-1 block w-full"
                                required
                                autocomplete="off"
                            />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="establecimiento_id" value="Establecimiento" />
                            <SelectInput
                                id="establecimiento_id"
                                v-model="form.establecimiento_id"
                                :options="establecimientoOptions"
                                class="mt-1 block w-full"
                                required
                                placeholder="Seleccione un establecimiento"
                            />
                            <InputError class="mt-2" :message="form.errors.establecimiento_id" />
                        </div>

                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="password" value="Contraseña" />
                            <TextInput
                                id="password"
                                v-model="form.password"
                                type="password"
                                class="mt-1 block w-full"
                                required
                                autocomplete="new-password"
                            />
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="password_confirmation" value="Confirmar contraseña" />
                            <TextInput
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                type="password"
                                class="mt-1 block w-full"
                                required
                                autocomplete="new-password"
                            />
                            <InputError class="mt-2" :message="form.errors.password_confirmation" />
                        </div>
                    </template>

                    <template #actions>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Crear usuario
                        </PrimaryButton>
                    </template>
                </FormSection>

                <div class="mt-10 overflow-hidden bg-white shadow sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Nombre
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Correo
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Establecimiento
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Rol
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="user in users" :key="user.id">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ user.name }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                    {{ user.email }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                    {{ user.establecimiento?.nombre ?? 'Sin establecimiento' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <span
                                        class="rounded-full px-2 py-1 text-xs font-semibold"
                                        :class="user.is_admin ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-600'"
                                    >
                                        {{ user.is_admin ? 'Administrador' : 'Usuario' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
