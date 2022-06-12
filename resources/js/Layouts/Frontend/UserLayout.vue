<template>
    <div id="page-container" class="side-scroll page-header-fixed main-content-boxed bg-white">
        
        <!-- Header -->
        <base-header :title="title">
            <slot name="header"></slot>
        </base-header>
        <!-- END Header -->

        <!-- Main Container -->
        <main id="main-container">
            <div class="content content-full">
                <div class="d-flex align-items-start">
                    <div class="user-sidenav-wrap">
                        <div class="bg-primary p-4 text-white text-xl-center">
                            <span class="avatar avatar-md mb-3">
                                <img :src="$page.props.auth.user.avatar_url" class="img-avatar">
                            </span>
                            <h4 class="fw-600 h5 my-10 text-white">{{ $page.props.auth.user.name }}</h4>
                        </div>

                        <div class="content-side content-side-full">
                            <UserMenu/>
                        </div>
                    </div>
                    <div class="user-panel">
                        <slot />
                    </div>
                </div>
            </div>
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
import BaseSidebar from '@/layouts/frontend/partials/Sidebar'
import BaseHeader from '@/layouts/frontend/partials/Header'
import BaseFooter from '@/layouts/frontend/partials/Footer'
import { Link } from '@inertiajs/inertia-vue';
import UserMenu from './partials/UserMenu.vue';
// import mobile from '@/Utils/mobile';
export default {
    name: 'BaseLayout',
    components: {
        BaseHeader,
        BaseFooter,
        BaseSidebar,
        Link,
        UserMenu
    },
    props : ['routeBack', 'title'],
    data() {
        return {
            showingNavigationDropdown: false,
        }
    },
    methods : {
    }
}
</script>
