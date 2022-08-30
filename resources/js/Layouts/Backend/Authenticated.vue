<template>
    <div id="page-container" class="sidebar-o enable-page-overlay side-scroll main-content-boxed side-trans-enabled" v-bind:class="{'sidebar-mini': pos}">
        
        <Head :title="title ? `${title} - Admin | ${ $page.props.settings.app_name }` : ' Admin | ' + $page.props.settings.app_name">
            <slot />
        </Head>
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
import BaseSidebar from './partials/Sidebar.vue'
import BaseHeader from './partials/Header'
import BaseFooter from './partials/Footer'
import { Head } from '@inertiajs/inertia-vue'

export default {
    name: 'BaseLayout',
    components: {
        BaseHeader,
        BaseFooter,
        BaseSidebar,
        Head
    },
    props: {
        title: String,
        pos : Boolean,
    },
    data() {
        return {
            showingNavigationDropdown: false,
        }
    },
}
</script>
