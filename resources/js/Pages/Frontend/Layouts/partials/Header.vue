<template>
    <!-- Header -->
    <header id="page-header">
        <!-- Header Content -->
        <div class="content-header">
            <!-- Left Section -->
            <div class="content-header-section">
                <!-- Toggle Sidebar -->
                <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                <button type="button" class="btn btn-circle btn-dual-secondary" data-toggle="layout"
                    data-action="sidebar_toggle">
                    <i class="fa fa-bars"></i>
                </button>
                <!-- END Toggle Sidebar -->

                <!-- Open Search Section -->
                <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                <button type="button" class="btn btn-circle btn-dual-secondary" data-toggle="layout"
                    data-action="header_search_on">
                    <i class="fa fa-search"></i>
                </button>
                <!-- END Open Search Section -->
            </div>
            <!-- END Left Section -->

            <!-- Right Section -->
            <div class="content-header-section">
                

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
