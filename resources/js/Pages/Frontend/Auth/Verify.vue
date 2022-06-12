<template>
    <BaseLayout>
        <div class="content content-full">
            <div class="row justify-content-center py-30 my-30">
                <div class="col-md-6 col-xl-6 align-items-center">
                    <div class="text-center mb-lg-5">
                        <h2 class="font-size-h2 text-center">Verifikasi Email Kamu</h2>
                        <h4 class="font-size-h4 text-center">Verifikasi email kamu untuk menyelesaikan pendaftaran</h4>
                        <img :src="asset('media/email.svg')" width="300px"/>
                    </div>
                    <div class="font-size-h5">Hai {{ $page.props.auth.user.name }}!</div>
                    <p class="nice-copy mb-2">Kami sudah mengirim email ke <b>{{ $page.props.auth.user.email }}</b> berserta link untuk verifikasi akun kamu.
                        Apabila tidak ditemukan, periksa folder spam email<br>
                    </p>
                    <Link :href="route('verification.resend')" method="post" as="button" class="btn btn-primary btn-block">
                        <span class="font-size-sm font-w500">Kirim Ulang Email</span>
                    </Link>
                </div>
            </div>
        </div>
    </BaseLayout>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue';
import BaseLayout from "@/layouts/frontend/BaseLayout";
export default {
    components: {
        BaseLayout,
        Link,
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
                onSuccess: () => {
                    this.$swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: `Data saved successfully!`,
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
