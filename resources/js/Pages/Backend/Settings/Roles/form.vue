<template>
    <BaseLayout>
        <div class="content">
            <form @submit.prevent="submit">
                <div class="content-heading pt-0 mb-3">
                    {{ title }}
                    <button type="submit" class="btn btn-primary float-right mr-5 btn-sm">
                        <i class="si si-paper-plane mr-1"></i>
                        Save
                    </button>
                </div>
                <div class="block block-rounded">
                    <div class="block-content block-content-full">
                        <div class="form-group row">
                            <label class="col-3 col-form-label" for="field-name">Name</label>
                            <div class="col-6">
                                <input type="text" v-bind:class="{'form-control':true, 'is-invalid' : errors.name}" id="field-name" v-model="form.name" >
                                <div v-if="errors.name" class="invalid-feedback font-size-sm">{{ errors.name[0] }}</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-3 mb-15" v-for="(data, index) in modules" :key="index">
                                <b-form-checkbox size="lg" :value="data.id" v-model="moduleSelected" @input.native="handleClick(data, $event)">
                                    {{ toUpperCase(data.name) }}
                                </b-form-checkbox>
                                <div class="ml-10">
                                    <b-form-checkbox v-for="(sf, idx) in data.permission" :key="idx" :value="sf.id" v-model="form.permissions" class="sub-filter ml-2">
                                           {{ toUpperCase(sf.name) }}
                                    </b-form-checkbox>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </BaseLayout>
</template>

<script>
import BaseLayout from '@/Layouts/Backend/Authenticated.vue';

export default {
    props: {
        modules: Array,
        errors: Object,
        data : Object,
        editMode: Boolean,
    },
    components: {
        BaseLayout,
    },
    data() {
        return {
            form: {
                id : null,
                name: null,
                permissions : [],
            },
            moduleSelected : [],
            title : 'Create New Staff Role',
        }
    },
    methods : {
        submit : function(){
            var form = this.$inertia.form(this.form);
            var url = this.editMode ? this.route('admin.settings.roles.update') : this.route('admin.settings.roles.store');
            form.post(url, {
                preserveScroll: true,
                onSuccess: () => {
                    return this.$swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: `Staff Roles Saved Successfully!`,
                        showCancelButton: true,
                        confirmButtonText: 'Add Another',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.$inertia.visit(this.route("admin.settings.roles.create"));
                        }
                    });
                },
            });
        },
        editModeActive(){
            this.form.id = this.data.id;
            this.form.name = this.data.name;
            // this.permissions = this.data.permissions;
            if(this.data.permissions.length >0){
                this.data.permissions.forEach(value => {
                    this.form.permissions.push(value.id);
                });
            }
            this.title = "Edit Staff Role";
        },
        
        handleClick(modules, e) {
            if(e.target.checked){
                modules.permission.forEach((e, i) =>{
                    this.form.permissions.push(e.id);
                });
            }else{
                let d = this.form.permissions.filter((b) => !modules.permission.some((c) => b === c.id));
                this.form.permissions = d;
            }
        }
    },
    mounted() {
        if(this.editMode){
            this.editModeActive();
        }
    },
}
</script>
