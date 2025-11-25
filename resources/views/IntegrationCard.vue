<template>
    <div class="card card-custom card-stretch h-100">
        <div class="card-body d-flex flex-column">
            <div class="d-flex align-items-center mb-5">
                <div v-if="integration.logo" class="symbol symbol-50px me-5">
                    <img :src="integration.logo" :alt="integration.name" class="object-fit-contain"
                        style="padding: 4px" />
                </div>
                <div v-else class="symbol symbol-50px me-5">
                    <div class="symbol-label bg-light-primary">
                        <i class="ki-duotone ki-puzzle fs-2x text-primary">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                        </i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <h3 class="text-gray-800 fw-bold mb-1">
                        <a v-if="integration.url && integration.url !== '#'" :href="integration.url" target="_blank"
                            rel="noopener noreferrer" class="text-gray-800 text-hover-primary text-decoration-none">
                            {{ integration.name }}
                        </a>
                        <span v-else>{{ integration.name }}</span>
                    </h3>
                    <p class="text-gray-600 fs-7 mb-0">{{ integration.description }}</p>
                </div>
            </div>
            <div class="mt-auto">
                <a v-if="integration.available && !integration.connected && integration.connectUrl && integration.connectUrl !== '#'"
                    :href="integration.connectUrl" target="_blank" rel="noopener noreferrer"
                    class="btn btn-primary w-100">
                    Verbind
                </a>
                <button v-else-if="integration.available && integration.connected" class="btn btn-primary w-100"
                    @click="$emit('click', integration.id)">
                    Beheren
                </button>
                <button v-else class="btn btn-secondary w-100" disabled>
                    Binnenkort beschikbaar
                </button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent, type PropType } from "vue";

export interface IIntegration {
    id: string;
    name: string;
    description: string;
    logo: string;
    url: string;
    available: boolean;
    connected: boolean;
    connectUrl?: string;
}
export default defineComponent({
    name: "integration-card",
    props: {
        integration: {
            type: Object as PropType<IIntegration>,
            required: true,
        },
    },
    emits: ["click"],
});
</script>
