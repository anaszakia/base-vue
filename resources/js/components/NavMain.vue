<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronRight } from '@lucide/vue';
import DynamicIcon from '@/components/DynamicIcon.vue';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { NavGroup, NavItem } from '@/types';

defineProps<{
    groups: NavGroup[];
}>();

const { isCurrentUrl } = useCurrentUrl();

function hasActiveChild(item: NavItem): boolean {
    if (!item.children || item.children.length === 0) {
        return false;
    }
    return item.children.some((child) => isCurrentUrl(child.href));
}
</script>

<template>
    <template v-for="group in groups" :key="group.category">
        <SidebarGroup class="px-2 py-0">
            <SidebarGroupLabel>{{ group.category }}</SidebarGroupLabel>
            <SidebarMenu>
                <template v-for="item in group.items" :key="item.title">
                    <!-- Dropdown / Collapsible Sub-menu -->
                    <Collapsible
                        v-if="item.children && item.children.length > 0"
                        as-child
                        :default-open="hasActiveChild(item)"
                        class="group/collapsible"
                    >
                        <SidebarMenuItem>
                            <CollapsibleTrigger as-child>
                                <SidebarMenuButton
                                    :tooltip="item.title"
                                    :is-active="hasActiveChild(item)"
                                >
                                    <DynamicIcon
                                        v-if="typeof item.icon === 'string'"
                                        :name="item.icon"
                                        class="h-4 w-4"
                                    />
                                    <component
                                        :is="item.icon"
                                        v-else-if="item.icon"
                                        class="h-4 w-4"
                                    />
                                    <span>{{ item.title }}</span>
                                    <ChevronRight
                                        class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90"
                                    />
                                </SidebarMenuButton>
                            </CollapsibleTrigger>
                            <CollapsibleContent>
                                <SidebarMenuSub>
                                    <SidebarMenuSubItem
                                        v-for="subItem in item.children"
                                        :key="subItem.title"
                                    >
                                        <SidebarMenuSubButton
                                            as-child
                                            :is-active="isCurrentUrl(subItem.href)"
                                        >
                                            <Link :href="subItem.href">
                                                <DynamicIcon
                                                    v-if="typeof subItem.icon === 'string'"
                                                    :name="subItem.icon"
                                                    class="h-4 w-4"
                                                />
                                                <component
                                                    :is="subItem.icon"
                                                    v-else-if="subItem.icon"
                                                    class="h-4 w-4"
                                                />
                                                <span>{{ subItem.title }}</span>
                                            </Link>
                                        </SidebarMenuSubButton>
                                    </SidebarMenuSubItem>
                                </SidebarMenuSub>
                        </CollapsibleContent>
                    </SidebarMenuItem>
                </Collapsible>

                <!-- Single Menu Item -->
                <SidebarMenuItem v-else>
                    <SidebarMenuButton
                        as-child
                        :is-active="isCurrentUrl(item.href)"
                        :tooltip="item.title"
                    >
                        <Link :href="item.href">
                            <DynamicIcon
                                v-if="typeof item.icon === 'string'"
                                :name="item.icon"
                                class="h-4 w-4"
                            />
                            <component
                                :is="item.icon"
                                v-else-if="item.icon"
                                class="h-4 w-4"
                            />
                            <span>{{ item.title }}</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </template>
        </SidebarMenu>
    </SidebarGroup>
    </template>
</template>
