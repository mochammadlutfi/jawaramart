<template>
    <div id="page-container" class="sidebar-o enable-page-overlay side-scroll main-content-boxed side-trans-enabled">

        <!-- Sidebar -->
        <base-sidebar>
            <slot name="sidebar"></slot>
        </base-sidebar>
        <!-- END Sidebar -->

        <!-- Header -->
        <base-header>
            <slot name="header"></slot>
        </base-header>
        <!-- END Header -->

        <!-- Main Container -->
        <main id="main-container">
            <slot />
        </main>
        <!-- END Main Container -->

        <!-- Footer -->
        <base-footer class="bg-body-light">
            <slot name="footer"></slot>
        </base-footer>
        <!-- END Footer -->
    </div>
</template>

<script>
import BaseSidebar from '@/layouts/partials/Sidebar'
import BaseHeader from '@/layouts/partials/Header'
import BaseFooter from '@/layouts/partials/Footer'
import { Link } from '@inertiajs/inertia-vue';

export default {
    name: 'BaseLayout',
    components: {
        BaseHeader,
        BaseFooter,
        BaseSidebar,
        Link
    },
    data() {
        return {
            showingNavigationDropdown: false,
        }
    },
}
</script>
