<template>
    <UserLayout>
        <div>
            <div class="content-heading pt-0 mb-3">
                Ubah Email
            </div>
            <div class="block block-rounded block-shadow block-bordered">
                <div class="block-content">
                    <div class="row">
                        <div class="col-lg-6">
                            <form v-if="!validate" @submit.prevent="submitAuth">
                                <div class="form-group">
                                    <label for="field-email">Alamat Email Saat Ini</label>
                                    <div class="d-flex">
                                        <div class="mr-4">{{ data.email }}</div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="field-password">Password</label>
                                    <PasswordInput v-model="auth.password" :error="authError.password" id="field-password"/>
                                    <div v-if="authError.password" class="text-danger text-sm">
                                        {{ authError.password[0] }}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        Konfirmasi
                                    </button>
                                </div>
                            </form>
                            <form v-else @submit.prevent="submit">
                                <div class="form-group">
                                    <label for="field-email">Alamat Email Saat Ini</label>
                                    <div class="d-flex">
                                        <div class="mr-4">{{ data.email }}</div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="field-email">Alamat Email Baru</label>
                                    <input type="text" v-bind:class="{'form-control':true, 'is-invalid' : errors.email}" id="field-email" v-model="form.email" >
                                    <div v-if="errors.email" class="invalid-feedback">
                                        {{ errors.email[0] }}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        Konfirmasi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>

<script>
import UserLayout from "@/Layouts/Frontend/UserLayout";
import PasswordInput from '@/Components/Form/PasswordInput';
import axios from 'axios';
export default {
    components: {
        UserLayout,
        PasswordInput,
    },
    props : {
        data : Object,
        errors : Object,
    },
    data(){
        return {
            form : {
                email : null,
            },
            auth : {
                password : null,
            },
            authError : {},
            validate : false
        }
    },
    mounted(){
    },
    methods : {
        submit: function () {
            this.$swal.fire({
                title: 'Please wait...',
                text: '',
                imageUrl: window._asset + 'media/loading.gif',
                showConfirmButton: false,
                allowOutsideClick: false,
            });
            let form = this.$inertia.form(this.form);
            let url = this.route("user.settings.email.update");
            form.post(url, {
                preserveScroll: true,
                onSuccess: () => {
                    this.$swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: `Email Berhasil Diperbahrui!`,
                        showConfirmButton: false,
                        timer : 1500,
                    });
                },
                onError:(error) => {
                    this.$swal.close();
                },
            });
        },
        submitAuth: function () {
            axios.post(route('user.settings.validate'), this.auth)
            .then(res => {
                const data = res.data;
                if(data.success){
                    this.validate = true;
                    this.authError = {};
                }else{
                    this.validate = false;
                    this.authError = data.message;
                }
            }).catch(err => {
                console.log(err)
            })
        },
        setEmail(){
            this.auth.email = this.data.email;
            this.form.email = this.data.email;
        }
    }
 }

</script>
