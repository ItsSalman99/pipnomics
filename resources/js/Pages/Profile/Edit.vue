<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import UserStatusCard from './Partials/UserStatusCard.vue';
import UserActivityLog from './Partials/UserActivityLog.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    activities: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <Head title="Account Profile & Activity" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-extrabold tracking-tight text-white flex items-center space-x-2">
                        <span>Account & Security Settings</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">Manage profile credentials, presence status, and review security activities.</p>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <!-- User Presence & Telemetry Card -->
                <div class="bg-slate-900 border border-slate-800 p-6 shadow-xl sm:rounded-2xl">
                    <UserStatusCard />
                </div>

                <!-- User Activity History & Audit Logs -->
                <div class="bg-slate-900 border border-slate-800 p-6 shadow-xl sm:rounded-2xl">
                    <UserActivityLog :activities="activities" />
                </div>

                <!-- Profile Information -->
                <div class="bg-slate-900 border border-slate-800 p-6 shadow-xl sm:rounded-2xl">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-2xl"
                    />
                </div>

                <!-- Update Password -->
                <div class="bg-slate-900 border border-slate-800 p-6 shadow-xl sm:rounded-2xl">
                    <UpdatePasswordForm class="max-w-2xl" />
                </div>

                <!-- Delete Account -->
                <div class="bg-slate-900 border border-slate-800 p-6 shadow-xl sm:rounded-2xl">
                    <DeleteUserForm class="max-w-2xl" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
