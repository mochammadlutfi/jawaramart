<template>
    <!-- Header -->
    <header id="page-header">
        <!-- Header Content -->
        <div class="content-header">
            <!-- Left Section -->
            <div class="content-header-section">
                <!-- Toggle Sidebar -->
                <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                
                <!-- <base-layout-modifier action="sidebarMiniToggle" size="sm" variant="dual" class="mr-2 d-none d-lg-inline-block">
                    <i class="fa fa-fw fa-ellipsis-v"></i>
                </base-layout-modifier> -->
                <!-- END Toggle Sidebar -->

                <!-- Open Search Section -->
                <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                <!-- <button type="button" class="btn btn-circle btn-dual-secondary" data-toggle="layout"
                    data-action="header_search_on">
                    <i class="fa fa-search"></i>
                </button> -->
                <!-- END Open Search Section -->

                <!-- Layout Options (used just for demonstration) -->
                <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                <!-- END Layout Options -->
            </div>
            <!-- END Left Section -->
            

            <!-- Right Section -->
            <div class="content-header-section">
                <a class="btn btn-outline-primary btn-sm" :href="route('admin.sale.pos.create')" v-if="hasPermission('Sale Order', 'POS')">
                    <i class="fas fa-cash-register mr-1"></i>
                    POS
                </a>

                <b-dropdown variant="dual link" class="d-inline-block ml-2" menu-class="p-0 border-0 dropdown-menu-md" right no-caret ref="oneDropdownDefaultUser">
                    <template #button-content>
                        <div class="d-flex align-items-center">
                            <img class="img-avatar img-avatar32" :src="$page.props.auth.staff.avatar_url" alt="Header Avatar" style="width: 30px;">
                            <span class="d-none d-sm-inline-block ml-2">{{ $page.props.auth.staff.name }}</span>
                            <i class="fa fa-fw fa-angle-down d-none d-sm-inline-block ml-1 mt-1"></i>
                        </div>
                    </template>
                    <div class="p-3 text-center bg-primary rounded-top">
                        <img class="img-avatar img-avatar48 img-avatar-thumb" :src="$page.props.auth.staff.avatar_url" alt="">
                        <p class="mt-2 mb-0 text-white font-w500">{{ $page.props.auth.staff.name }}</p>
                    </div>
                    <div class="p-2">
                        <Link class="dropdown-item d-flex align-items-center justify-content-between" :href="route('admin.profile')">
                            <span class="font-size-sm font-w500">Profile</span>
                        </Link>
                        <Link :href="route('admin.logout')" method="post" as="button" class="dropdown-item d-flex align-items-center justify-content-between">
                            <span class="font-size-sm font-w500">Log Out</span>
                        </Link >
                    </div>
                </b-dropdown>

            </div>
            <!-- END Right Section -->
        </div>
        <!-- END Header Content -->
    </header>
    <!-- END Header -->
</template>

<script>
    import { Link } from '@inertiajs/inertia-vue';
    export default {
        name: 'BaseHeader',
        components: {
            Link,
        },
        props: {
            user: Object
        },
    }

</script>
