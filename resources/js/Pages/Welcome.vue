<script setup>
import {Head, Link} from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head title="Home"/>

    <div
        class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white"
    >
        <div v-if="canLogin" class="sm:fixed sm:top-0 sm:right-0 p-6 text-end">
            <Link
                v-if="$page.props.auth.user"
                :href="route('dashboard')"
                class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
            >Dashboard
            </Link
            >

<!--            <template v-else>
                <Link
                    :href="route('login')"
                    class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                >Log in
                </Link
                >

                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="ms-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                >Register
                </Link
                >
            </template>-->
        </div>

        <div class="max-w-7xl mx-auto p-6 lg:p-8">
            <div class="flex justify-center">
                <svg t="1732070856795" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="5365" width="128" height="128"><path d="M960.9 405.7l-448 323.4-448-323.4 448-323.4z" fill="#5B79FB" p-id="5366"></path><path d="M187.3 139.1h651.2v795.4H187.3z" fill="#E5ECFF" p-id="5367"></path><path d="M335 222h355.7v73.9H335z" fill="#FF7E71" p-id="5368"></path><path d="M258.3 358h509.3v37H258.3zM258.3 420.7h509.3v37H258.3zM258.3 483.4h509.3v37H258.3zM258.3 546.1h509.3v37H258.3z" fill="#FFFFFF" p-id="5369"></path><path d="M64.9 934.5h896V405.7l-448 323.4-448-323.4z" fill="#5B79FB" p-id="5370"></path><path d="M64.9 934.5h896L594.6 670.1H431.2z" fill="#83A4FF" p-id="5371"></path></svg>
            </div>

            <div class="mt-16">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
                    <a
                        :href="route('pickup')"
                        class="scale-100 p-6 bg-white dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500"
                    >
                        <div>
                            <div
                                class="h-16 w-16 bg-red-50 dark:bg-red-800/20 flex items-center justify-center rounded-full"
                            >
                                <svg t="1728978420580" class="icon" viewBox="0 0 1038 1024" version="1.1"
                                     xmlns="http://www.w3.org/2000/svg" p-id="4312" width="32" height="32">
                                    <path
                                        d="M680.688 229.01c0-11.255-9.107-20.362-20.363-20.362H37.657c-11.256 0-20.364 9.107-20.364 20.363v350.168h663.498V229.01zM833.26 628.91c-42.262 0-76.542 34.28-76.542 76.542s34.28 76.541 76.542 76.541 76.542-34.28 76.542-76.541-34.28-76.542-76.542-76.542zM222.36 628.91c-42.262 0-76.542 34.28-76.542 76.542s34.28 76.541 76.542 76.541c42.261 0 76.541-34.28 76.541-76.541 0-42.262-34.28-76.542-76.541-76.542zM174.368 609.775H17.293v75.211c0 11.256 9.108 20.363 20.364 20.363h77.565c0-41.647 24.15-77.871 59.146-95.574z"
                                        p-id="4313" fill="#ef4444"></path>
                                    <path
                                        d="M1027.07 557.587L899.978 348.121c-5.833-9.619-19.75-17.396-31.005-17.396h-137.12c-11.257 0-20.364 9.21-20.364 20.364v258.686H270.352c34.996 17.6 59.145 53.824 59.145 95.574h396.625c0-59.043 47.992-107.137 107.138-107.137 59.043 0 107.138 48.094 107.138 107.137h76.746c11.256 0 20.363-9.21 20.363-20.363V595.45c0.103-11.154-4.604-28.243-10.437-37.862z m-87.593-49.936l-177.233-19.852c-5.526-0.614-10.13-5.73-10.13-11.358v-94.654c0-5.628 4.604-10.233 10.232-10.233h93.835c5.629 0 12.587 3.889 15.554 8.698l72.654 119.724c2.865 4.81 0.613 8.289-4.912 7.675z"
                                        p-id="4314" fill="#ef4444"></path>
                                </svg>
                            </div>

                            <h2 class="mt-6 text-xl font-semibold text-gray-900 dark:text-white">Pickup</h2>

                        </div>

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            class="self-center shrink-0 stroke-red-500 w-6 h-6 mx-6"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75"
                            />
                        </svg>
                    </a>
                    <a
                        :href="route('delivery')"
                        class="scale-100 p-6 bg-white dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500"
                    >
                        <div>
                            <div
                                class="h-16 w-16 bg-red-50 dark:bg-red-800/20 flex items-center justify-center rounded-full"
                            >
                                <svg t="1728978603431" class="icon" viewBox="0 0 1024 1024" version="1.1"
                                     xmlns="http://www.w3.org/2000/svg" p-id="10573" width="32" height="32">
                                    <path
                                        d="M937.6 160H432c-51.2 0-89.6 38.4-89.6 89.6v-4.8H132.8c-20.8 0-38.4 12.8-46.4 30.4L4.8 438.4C0 448 0 456 0 465.6v252.8c0 25.6 17.6 43.2 43.2 43.2h33.6c12.8 59.2 68.8 102.4 128 102.4 64 0 115.2-43.2 128-102.4H662.4c12.8 59.2 68.8 102.4 128 102.4s115.2-43.2 128-102.4h46.4c30.4 0 56-25.6 56-56V251.2c6.4-51.2-32-91.2-83.2-91.2z m-784 172.8h188.8v86.4H112l41.6-86.4z m180.8 342.4c-20.8-46.4-68.8-81.6-124.8-81.6s-102.4 33.6-124.8 81.6V504h257.6v171.2h-8z m-124.8 102.4c-25.6 0-46.4-20.8-46.4-46.4 0-25.6 20.8-46.4 46.4-46.4s46.4 20.8 46.4 46.4c1.6 25.6-20.8 46.4-46.4 46.4z m587.2 0c-25.6 0-46.4-20.8-46.4-46.4 0-25.6 20.8-46.4 46.4-46.4s46.4 20.8 46.4 46.4c0 25.6-20.8 46.4-46.4 46.4z m145.6-102.4h-25.6c-20.8-46.4-68.8-81.6-124.8-81.6s-102.4 33.6-124.8 81.6h-240V251.2s0-4.8 4.8-4.8h510.4s4.8 0 4.8 4.8v424h-4.8z"
                                        p-id="10574" fill="#ef4444"></path>
                                </svg>
                            </div>

                            <h2 class="mt-6 text-xl font-semibold text-gray-900 dark:text-white">Delivery</h2>
                        </div>

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            class="self-center shrink-0 stroke-red-500 w-6 h-6 mx-6"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75"
                            />
                        </svg>
                    </a>
                    <a
                        :href="route('appointment.show')"
                        class="scale-100 p-6 bg-white dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500"
                    >
                        <div>
                            <div
                                class="h-16 w-16 bg-red-50 dark:bg-red-800/20 flex items-center justify-center rounded-full"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    class="w-7 h-7 stroke-red-500"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z"
                                    />
                                </svg>
                            </div>

                            <h2 class="mt-6 text-xl font-semibold text-gray-900 dark:text-white">My Appointments</h2>
                        </div>

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            class="self-center shrink-0 stroke-red-500 w-6 h-6 mx-6"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75"
                            />
                        </svg>
                    </a>
                    <a
                        :href="route('broker')"
                        class="scale-100 p-6 bg-white dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500"
                    >
                        <div>
                            <div
                                class="h-16 w-16 bg-red-50 dark:bg-red-800/20 flex items-center justify-center rounded-full"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    class="w-7 h-7 stroke-red-500"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z"
                                    />
                                </svg>
                            </div>

                            <h2 class="mt-6 text-xl font-semibold text-gray-900 dark:text-white">Broker</h2>
                        </div>

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            class="self-center shrink-0 stroke-red-500 w-6 h-6 mx-6"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75"
                            />
                        </svg>
                    </a>
                </div>

            </div>


            <div class="mt-16">
                <el-divider/>
            </div>
            <div class="flex justify-center mt-16 px-6 sm:items-center">
                <div class="text-center text-sm text-gray-500 dark:text-gray-400 sm:text-end sm:ms-0">
                    Copyright © 2024 Evolution Electric Vehicles
                </div>
            </div>
        </div>
    </div>
</template>

<style>
.bg-dots-darker {
    background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,0,0.07)'/%3E%3C/svg%3E");
}

@media (prefers-color-scheme: dark) {
    .dark\:bg-dots-lighter {
        background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E");
    }
}
</style>
