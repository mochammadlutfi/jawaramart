<template>
    <div id="page-container" class="side-scroll page-header-fixed main-content-boxed bg-white front">
        <!-- <template v-if="$isMobile()">
            <mobile-header>
                <slot name="header"></slot>
            </mobile-header> -->
        <!-- </template>
        <template v-else> -->
            <!-- Header -->
            <base-header :back="routeBack" :title="title">
                <slot name="header"></slot>
            </base-header>
            <!-- END Header -->
        <!-- </template> -->


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
<style>

@media only screen and (max-width: 768px) {
#page-container.page-header-fixed.front #main-container {
    padding-top: 50px !important;
}
}
</style>


<script>

import BaseSidebar from '@/layouts/frontend/partials/Sidebar'
import BaseHeader from '@/layouts/frontend/partials/Header'
import BaseFooter from '@/layouts/frontend/partials/Footer'
import { Link } from '@inertiajs/inertia-vue';

// import MobileHeader from '@/layouts/frontend/mobile/Header'
export default {
    name: 'BaseLayout',
    components: {
        BaseHeader,
        BaseFooter,
        BaseSidebar,
        Link,
    },
    props : ['routeBack', 'title'],
    data() {
        return {
            showingNavigationDropdown: false,
        }
    },
}
</script>
