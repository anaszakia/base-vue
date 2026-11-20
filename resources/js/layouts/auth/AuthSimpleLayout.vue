<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import AppearanceTabs from '@/components/AppearanceTabs.vue';
import { home } from '@/routes';

const page = usePage();
const name = page.props.name;

defineProps<{
    title?: string;
    description?: string;
    variant?: 'default' | 'split';
}>();
</script>

<template>
    <div class="min-h-svh bg-background p-3 sm:p-6 lg:p-10">
        <div
            v-if="variant === 'split'"
            class="mx-auto grid min-h-[calc(100svh-1.5rem)] w-full max-w-7xl overflow-hidden rounded-3xl border bg-card shadow-2xl shadow-black/5 sm:min-h-[calc(100svh-3rem)] lg:min-h-[calc(100svh-5rem)] lg:grid-cols-2"
        >
            <section
                class="relative flex min-h-72 flex-col justify-between overflow-hidden bg-emerald-700 p-8 text-white sm:p-12 lg:p-16 dark:bg-zinc-950"
            >
                <Link
                    :href="home()"
                    class="relative z-10 flex w-fit items-center gap-3 text-sm font-semibold tracking-wide"
                >
                    <span
                        class="flex size-10 items-center justify-center rounded-xl border border-white/30 bg-white/90"
                    >
                        <AppLogoIcon
                            class="size-6 fill-current text-zinc-900"
                        />
                    </span>
                    {{ name }}
                </Link>

                <div class="relative z-10 max-w-xl py-12">
                    <p
                        class="mb-5 text-xs font-semibold tracking-[0.24em] text-emerald-100 uppercase"
                    >
                        AKUN ANDA
                    </p>
                    <h1
                        class="text-4xl leading-tight font-semibold tracking-tight sm:text-5xl"
                    >
                        {{ title }}
                    </h1>
                    <p class="mt-6 max-w-md text-base leading-7 text-white/90">
                        {{ description }}
                    </p>
                </div>

                <p class="relative z-10 text-xs text-white/75">
                    Akses aman ke akun Anda
                </p>
            </section>

            <section
                class="flex items-center justify-center px-6 py-10 sm:px-12 lg:px-14"
            >
                <div class="flex w-full max-w-md flex-col gap-8">
                    <div class="flex justify-end">
                        <AppearanceTabs locale="id" />
                    </div>
                    <slot />
                </div>
            </section>
        </div>

        <div
            v-else
            class="flex min-h-[calc(100svh-1.5rem)] flex-col items-center justify-center gap-6 sm:min-h-[calc(100svh-3rem)] lg:min-h-[calc(100svh-5rem)]"
        >
            <div class="w-full max-w-sm">
                <div class="flex flex-col gap-8">
                    <div class="flex flex-col items-center gap-4">
                        <Link
                            :href="home()"
                            class="flex flex-col items-center gap-2 font-medium"
                        >
                            <div
                                class="mb-1 flex h-9 w-9 items-center justify-center rounded-md"
                            >
                                <AppLogoIcon
                                    class="size-9 fill-current text-[var(--foreground)] dark:text-white"
                                />
                            </div>
                            <span class="sr-only">{{ title }}</span>
                        </Link>
                        <div class="space-y-2 text-center">
                            <h1 class="text-xl font-medium">{{ title }}</h1>
                            <p
                                class="text-center text-sm text-muted-foreground"
                            >
                                {{ description }}
                            </p>
                        </div>
                    </div>
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
