<template>
    <BaseLayout>
        <div class="content content-full">
            <div class="row justify-content-center py-30 my-30">
                <div class="col-md-4 col-xl-4 align-items-center">
                    <div class="text-center">
                        <h2 class="font-weight-bold mb-0">Ubah Password</h2>
                        <p>Masukkan password baru untuk keamanan akun Anda</p>
                    </div>
                    <form @submit.prevent="submit">
                        <div class="form-group">
                            <label class="form-label" for="field-password">Password Baru
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
                            <div v-if="errors.password_confirmation" class="text-danger">
                                {{ errors.password_confirmation[0] }}
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block" :disabled="loading">
                                Konfirmasi
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
        token: String,
        errors: Object,
    },
    data() {
        return {
            form : {
                token : '',
                email: '',
                password: '',
                password_confirmation: '',
            },
            loading : false,
        }
    },
    mounted(){
        this.form.token = this.token;
        this.form.email = this.route().params.email;
    },
    methods: {
        submit() {
            this.$swal.fire({
                title: 'Please wait...',
                text: '',
                imageUrl: window._asset + 'media/loading.gif',
                showConfirmButton: false,
                allowOutsideClick: false,
            });
            let form = this.$inertia.form(this.form);
            let url = this.route("password.update");
            form.post(url, {
                preserveScroll: true,
                onSuccess: () => {
                    this.$swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: `Password kamu berhasil diperbaharui!`,
                        showConfirmButton: false,
                    });
                },
                onError:(error) => {
                    this.$swal.close();
                },
            });
        }
    }
 }

</script>
