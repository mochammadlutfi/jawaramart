<template>
    <!-- Sidebar -->
    <nav id="sidebar" aria-label="Main Navigation">
        <slot>
            <!-- Sidebar Content -->
            <div class="sidebar-content">
                <!-- Side Header -->
                <div class="content-header content-header-fullrow px-15">

                    <!-- Normal Mode -->
                    <div class="content-header-section text-center align-parent sidebar-mini-hidden">
                        <!-- Logo -->
                        <div class="content-header-item">
                            <a :href="route('admin.dashboard')">
                                <img :src="asset($page.props.settings.app_logo)" class="img-fluid h-100">
                            </a>
                        </div>
                        <!-- END Logo -->
                    </div>
                    <!-- END Normal Mode -->
                </div>
                <!-- END Side Header -->


                <!-- Sidebar Scrolling -->
                <simplebar class="js-sidebar-scroll">
                    <!-- Side Navigation -->
                    <div class="content-side">
                        <base-navigation :nodes="$page.props.menu"></base-navigation>
                    </div>
                    <!-- END Side Navigation -->
                </simplebar>
                <!-- END Sidebar Scrolling -->
            </div>
            <!-- Sidebar Content -->
        </slot>
    </nav>
    <!-- END Sidebar -->
</template>

<script>
    import simplebar from 'simplebar-vue'
    import menuList from '../menu';

    export default {
        name: 'BaseSidebar',
        props: {
            classes: String,
        },
        components: {
            simplebar
        },
        data() {
            return {
                navigation: menuList.main
            }
        }
    }

</script>
