<template>
    <BaseLayout>
        <div class="content content-full">
            <div class="row justify-content-center py-30 my-30">
                <div class="col-md-4 col-xl-4 align-items-center">
                    <div class="text-center">
                        <h2 class="font-weight-bold mb-0">Lupa Password</h2>
                        <p>Masukan email untuk mengganti password</p>
                    </div>
                    <div v-if="$page.props.flash.message" class="alert">
                        {{ $page.props.flash.message }}
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
                            <button type="submit" class="btn btn-primary btn-block">
                                Kirim Link Reset Password
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
            }),
            loading : false,
        }
    },

    methods: {
        submit() {
            this.form.post(this.route('password.email'), {
                onFinish: () => this.form.reset('password'),
            })
        }
    }
 }

</script>
