<template>
    <BaseLayout>
        <div class="content content-full">
            <div class="row justify-content-center py-30 my-30">
                <div class="col-md-4 col-xl-4 align-items-center">
                    <div class="text-center">
                        <h2 class="font-weight-bold mb-0">Daftar Sekarang</h2>
                        <p>Sudah punya akun? <a :href="route('login')">Masuk</a></p>
                    </div>
                    <form @submit.prevent="submit">
                        <div class="form-group">
                            <label class="form-label" for="field-name">Nama Lengkap
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" v-bind:class="{'form-control':true, 'is-invalid' : errors.name }" id="field-username" v-model="form.name" autofocus>
                            <div v-if="errors.name" class="invalid-feedback">
                                {{ errors.name[0] }}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="field-email">Alamat Email
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" v-bind:class="{'form-control':true, 'is-invalid' : errors.email}" id="field-username" v-model="form.email" autofocus>
                            <div v-if="errors.email" class="invalid-feedback">
                                {{ errors.email[0] }}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="field-password">Password
                                <span class="text-danger">*</span>
                            </label>
                            <PasswordInput v-model="form.password" :error="errors.password" id="field-password"/>
                            <div v-if="errors.password" class="text-danger text-sm">
                                {{ errors.password[0] }}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="field-password_confirmation">Konfirmasi Password
                                <span class="text-danger">*</span>
                            </label>
                            <PasswordInput v-model="form.password_confirmation" :error="errors.password_confirmation" id="field-password_confirmation"/>
                            <div v-if="errors.password_confirmation" class="invalid-feedback">
                                {{ errors.password_confirmation[0] }}
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block" :disabled="loading">
                                Daftar Sekarang
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
            form: {
                name : null,
                email : null,
                password: null,
                password_confirmation: null,
            },
            loading : false,
        }
    },

    methods: {
        submit() {
            let form = this.$inertia.form(this.form);
            let url = this.route("register");
            form.post(url, {
                preserveScroll: true,
                onProgress: () => {
                    this.loading = true;
                },
                onSuccess: () => {
                    this.loading = false;
                },
                onError:(error) => {
                    this.$swal.close();
                },
            });
        }
    }
 }

</script>
