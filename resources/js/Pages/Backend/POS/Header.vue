<template>
    <!-- Header -->
    <header id="page-header">
        <!-- Header Content -->
        <div class="content-header pos">
            <!-- Left Section -->
            <div class="content-header-section">
                <!-- Logo -->
                <div class="content-header-item height-50">
                    <a :href="route('admin.sale.pos.index')">
                        <img :src="asset($page.props.settings.app_logo)" class="img-fluid h-100">
                    </a>
                </div>
                <!-- END Logo -->
            </div>
            <!-- END Left Section -->
            

            <!-- Right Section -->
            <div class="content-header-section">
                
                <button type="button" class="btn btn-outline-primary mx-1 btn-sm" @click="openReport">
                    <i class="fa fa-cash-register"></i>
                    Cash Register
                </button>

                <button type="button" class="btn btn-outline-primary mx-1 btn-sm" @click="openReport">
                    <i class="fa fa-cash-register"></i>
                    Sales Report (Excel)
                </button>

                <button type="button" class="btn btn-outline-primary mx-1 btn-sm" @click="openReport">
                    <i class="fi fi-rs-time-twenty-four"></i>
                    Recent Transaction
                </button>

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

        <report-modal ref="posReport" :register="register"/>

    </header>
    <!-- END Header -->
</template>

<script>
    import reportModal from './ReportModal.vue';
    import { Link } from '@inertiajs/inertia-vue';
    export default {
        name: 'pos-header',
        components: {
            Link,
            reportModal
        },
        props: {
            register: Object
        },
        methods : {
            openReport(value){
                this.$refs.posReport.openModal(value);
            },

            closeRegister(){

            }
        }
    }

</script>
