<template>
    <BaseLayout>
        <div class="content content-full">
            <div class="row justify-content-center py-30 my-30">
                <div class="col-md-4 col-xl-4 align-items-center">
                    <div class="text-center">
                        <h2 class="font-weight-bold mb-0">Masuk</h2>
                        <p>Belum punya akun? <a :href="route('register')">Daftar</a> di sini</p>
                    </div>
                    <form @submit.prevent="submit">
                        <div class="form-group">
                            <label class="form-label" for="field_login-email">Alamat Email
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" v-bind:class="{'form-control':true, 'is-invalid' : errors.email}" id="login-username" v-model="form.email" autofocus>
                            <div v-if="errors.email" class="invalid-feedback">
                                {{ errors.email[0] }}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="field-password">Password Baru
                                <span class="text-danger">*</span>
                            </label>
                            <PasswordInput v-model="form.password" :error="errors.password" id="field-password"/>
                            <div v-if="errors.password" class="text-danger text-sm">
                                {{ errors.password[0] }}
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <div class="col-sm-6 d-sm-flex align-items-center">
                                <div class="custom-control custom-checkbox mr-auto ml-0 mb-0">
                                    <input type="checkbox" class="custom-control-input" id="login-remember-me" name="remember">
                                    <label class="custom-control-label" for="login-remember-me">Ingat Saya</label>
                                </div>
                            </div>
                            <div class="col-sm-6 text-sm-right">
                                <a class="font-weight-bold" :href="route('password.request')">
                                    Lupa Password?
                                </a>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block" :disabled="loading">
                                <b-spinner small v-if="loading"></b-spinner>
                                Masuk Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </BaseLayout>
</template>

<script>
import BaseLayout from "@/layouts/frontend/BaseLayout";
import PasswordInput from '@/Components/Form/PasswordInput';
export default {
    components: {
        BaseLayout,
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
            this.loading = true;
            this.form.post(this.route('login'), {
                onFinish: () => {
                    this.form.reset('password');
                    this.loading = false;
                },
            })
        }
    }
 }

</script>
