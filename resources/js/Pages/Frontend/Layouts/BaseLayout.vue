<template>
    <div id="page-container" class="side-scroll page-header-fixed main-content-boxed bg-white">

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
import BaseSidebar from './partials/Sidebar'
import BaseHeader from './partials/Header'
import BaseFooter from './partials/Footer'
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
