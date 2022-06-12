<template>
    <UserLayout>
        <div>
            <div class="content-heading pt-3 mb-3">
                Ubah Password
            </div>
            <div class="row">
                <div class="col-md-6 col-xl-6">
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
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </UserLayout>
</template>
<script>
import UserLayout from "@/Layouts/Frontend/UserLayout";
import PasswordInput from '@/Components/Form/PasswordInput';
export default {
    components :{
        UserLayout,
        PasswordInput
    },
    props: {
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
    methods: {
        reset(){
            this.form.password = null;
            this.form.new_password = null;
            this.form.new_password_confirm = null;
        },
        submit: function () {
            this.$swal.fire({
                title: 'Tunggu Sebentar...',
                text: '',
                imageUrl: window._asset + 'media/loading.gif',
                showConfirmButton: false,
                allowOutsideClick: false,
            });
            let form = this.$inertia.form(this.form);
            let url = this.route("user.settings.password.update");
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
    },

}
</script>