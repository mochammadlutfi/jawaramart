<template>
    <BaseLayout :title="title">
        <div class="content">
            <div class="content-heading pt-0 mb-3">
                Ubah Password
            </div>
            <div class="block block-bordered block-shadow block-rounded">
                <div class="block-content">
                    <div class="col-md-6">
                        <form @submit.prevent="submit">
                            <div class="form-group">
                                <label for="reset-password">Password Saat Ini</label>
                                <password-input :value="form.password" :error="errors.password" @input="(value) => form.password = value" id="reset-password"/>
                                <div v-if="errors.password" class="text-danger text-sm">
                                    {{ errors.password[0] }}
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="reset-new_password">Password Baru</label>
                                <password-input :value="form.new_password" :error="errors.new_password" @input="(value) => form.new_password = value" id="reset-new_password"/>
                                <div v-if="errors.new_password" class="text-danger text-sm">
                                    {{ errors.new_password[0] }}
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="field-new_password_confirm">Konfirmasi Password Baru</label>
                                <password-input :value="form.new_password_confirm" :error="errors.new_password_confirm" @input="(value) => form.new_password_confirm = value" id="reset-new_password_confirm"/>
                                <div v-if="errors.new_password_confirm" class="text-danger text-sm">
                                    {{ errors.new_password_confirm[0] }}
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-block">
                                    Change Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </BaseLayout>
</template>
<script>
import BaseLayout from '@/Layouts/Backend/Authenticated.vue';
import PasswordInput from '@/Components/Form/PasswordInput';
export default {
    components: {
        BaseLayout,
        PasswordInput,
    },
    props : {
        errors : Object,
    },
    data(){
        return {
            form : {
                password : null,
                new_password : null,
                new_password_confirm : null,
            }
        }
    },
    methods : {
        submit: function () {
            this.$swal.fire({
                title: 'Tunggu Sebentar...',
                text: '',
                imageUrl: window._asset + 'media/loading.gif',
                showConfirmButton: false,
                allowOutsideClick: false,
            });
            let form = this.$inertia.form(this.form);
            let url = this.route("admin.password.update");
            form.post(url, {
                preserveScroll: true,
                onSuccess: () => {
                    this.$swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: `Password Berhasil Diperbahrui!`,
                        showConfirmButton: false,
                        timer : 1500,
                    });
                    this.reset();
                },
                onError:(error) => {
                    this.$swal.close();
                },
            });
        },
    }
}
</script>
