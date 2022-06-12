<template>
    
    <div id="page-container" class="main-content-boxed">

        <!-- Main Container -->
        <main id="main-container">
            <!-- Page Content -->
            <div class="bg-white">
                <div class="hero-static content content-full">

                    <!-- Sign In Form -->
                    <div class="row justify-content-center px-5">
                        <div class="col-sm-8 col-md-6 col-xl-4">
                            
                            <!-- Header -->
                            <div class="py-30 px-5 text-center">
                                <a :href="route('admin.dashboard')">
                                    <img :src="asset($page.props.settings.app_logo)" class="img-fluid w-75"/>
                                </a>
                                <h1 class="h2 font-w700 mt-50 mb-10">Welcome to Your Dashboard</h1>
                                <h2 class="h4 font-w400 text-muted mb-0">Please sign in</h2>
                            </div>
                            <!-- END Header -->

                            
                            <form @submit.prevent="submit">
                                <div class="form-group">
                                    <label for="login-username">Email / Username</label>
                                    <input type="text" v-bind:class="{'form-control':true, 'is-invalid' : errors.email}" id="login-username" v-model="form.email" autofocus>
                                    <div v-if="errors.email" class="invalid-feedback">
                                        {{ errors.email[0] }}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="login-password">Password</label>
                                    <PasswordInput v-model="form.password" :error="errors.password" id="field-password"/>
                                    <div v-if="errors.password" class="text-danger text-sm">
                                        {{ errors.password[0] }}
                                    </div>
                                </div>
                                <b-button type="submit" variant="primary" class="btn-noborder" block>
                                    Login
                                </b-button>
                            </form>
                        </div>
                    </div>
                    <!-- END Sign In Form -->
                </div>
            </div>
            <!-- END Page Content -->
        </main>
        <!-- END Main Container -->
    </div>
    <!-- END Page Container -->
</template>

<script>
import AppMeta from '@/components/AppMeta.vue';
import PasswordInput from '@/Components/Form/PasswordInput';
export default {
    components:{
        AppMeta,
        PasswordInput
    },
    props: {
        canResetPassword: Boolean,
        status: String,
        errors: Object,
    },
    data() {
        return {
            form: this.$inertia.form({
                email: '',
                password: '',
                remember: false
            }),
            loading : false,
        }
    },

    methods: {
        submit() {
            this.form.post(this.route('admin.login'), {
                onFinish: () => this.form.reset('password'),
            })
        }
    }
}

</script>

<style>

</style>
