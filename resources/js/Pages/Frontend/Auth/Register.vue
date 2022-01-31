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
                            <label class="form-label" for="reg-name">Nama Lengkap
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="name" id="reg-name" placeholder="Masukan Nama Lengkap">
                            <div class="invalid-feedback" id="reg_error-name"></div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="reg-email">Alamat Email
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="email" id="reg-email" placeholder="Masukan Alamat Email">
                            <div class="invalid-feedback" id="reg_error-email"></div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="reg-password">Password
                                <span class="text-danger">*</span>
                            </label>
                            <input type="password" class="form-control" name="password" id="reg-password" placeholder="Masukan Password">
                            <div class="invalid-feedback" id="reg_error-password"></div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="reg-password_confirmation">Konfirmasi Password
                                <span class="text-danger">*</span>
                            </label>
                            <input type="password" class="form-control" name="password_confirmation" id="reg-password_confirmation" placeholder="Masukan Konfirmasi Password">
                            <div class="invalid-feedback" id="reg_error-password_confirmation"></div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block">
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
export default {
    components: {
        BaseLayout,
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
            this.form.post(this.route('login'), {
                onFinish: () => this.form.reset('password'),
            })
        }
    }
 }

</script>
