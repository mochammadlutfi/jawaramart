<template>
    <BaseLayout title="Product Categories">
        <div class="content">
            <div class="content-heading pt-0 mb-3">
                Product Categories
            </div>
            <b-row>
                <b-col cols="4">
                    <div class="block">
                        <div class="block-content pb-15">
                            <button class="btn btn-primary btn-block add-root-category" @click="addParent">Create Parent Category</button>
                            <button class="btn btn-primary btn-block add-sub-category" :disabled="!addChildButton" @click="addChild">Create Sub Category</button>
                            <div class="mt-10 mb-10">
                                <a href="#" class="collapse-all">Collapse All</a> |
                                <a href="#" class="expand-all">Expand All</a>
                            </div>
                            <v-jstree :data="listData" ref="tree">
                                <template slot-scope="_">
                                    <div style="display: inherit; width: 100%" @click.ctrl="customItemClickWithCtrl" @click.exact="itemClick(_.vm, _.model, $event)">
                                    <i :class="_.vm.themeIconClasses" role="presentation" v-if="!_.model.loading"></i>
                                    {{_.model.text}}
                                    </div>
                                </template>
                            </v-jstree>

                        </div>
                    </div>
                </b-col>
                <b-col cols="8">
                    <form @submit.prevent="submit">
                        <div class="block">
                            <div class="block-header block-header-default">
                                <h3 class="block-title" id="form-title">Tambah kategori</h3>
                                <button type="button" v-show="editMode" class="btn btn-danger float-right mr-5 hide" @click="deleteData">
                                    <i class="si si-trash mr-1"></i>
                                    Hapus
                                </button>
                                <button type="submit" class="btn btn-primary float-right">
                                    <i class="si si-paper-plane mr-1"></i>
                                    Save
                                </button>
                            </div>
                            <div class="block-content">
                                <input type="hidden" class="form-control"  name="id" v-model="form.id">
                                <input type="hidden" class="form-control"  name="parent_id" v-model="form.parent_id">
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label" for="field-title">Name</label>
                                    <div class="col-lg-7">
                                        <input type="text" v-bind:class="{'form-control':true, 'is-invalid' : errors.name}"  id="field-title" name="title" v-model="form.name">
                                        <div v-if="errors.name" class="invalid-feedback">
                                            {{ errors.name[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label" for="field-nama">Thumbnail</label>
                                    <div class="col-lg-7">
                                        <div class="col-6 pl-0">
                                            <ImageUpload 
                                            :image="thumbnail != null ? asset(thumbnail) : thumbnail" :ratio="1/1"
                                            @done="(image) => form.image = image"
                                            @removeImage="(image) => form.image = image" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </b-col>
            </b-row>
        </div>
    </BaseLayout>
</template>

<script>
// import treeview from "vue3-treeview";
import VJstree from 'vue-jstree'
import BaseLayout from '../Layouts/Authenticated.vue';
import ImageUpload from '@/components/SingleImageUpload.vue'

export default {
    components: {
        BaseLayout,
        VJstree,
        ImageUpload
    },
    data: function () {
        return {
            selected: null,
            form: {
                id : null,
                parent_id : null,
                name: null,
                image : null
            },
            thumbnail : null,
            editMode : false,
            addChildButton : false,
        };
    },
    props: {
        dataList: Array,
        errors: Object,
    },
    computed:{ 
        listData(){ 
            return JSON.parse(JSON.stringify(this.$page.props.dataList)) 
        }
    },
    watch: {
        listData : function() {
            this.$refs.tree.initializeData(this.listData);
        }
    },
    methods: {
        itemClick (node) {
            this.form = {
                id : node.model.id,
                parent_id : node.model.parent_id,
                name: node.model.text,
                image : node.model.thumbnail
            }
            this.thumbnail = node.model.thumbnail;
            this.editMode = true;
            if(node.model.level == 2){
                this.addChildButton = false;
            }else{
                this.addChildButton = true;
            }
        },
        addParent(){
            this.reset();
        },
        addChild(){
            this.form.parent_id =  this.form.id;
            this.form.id = null;
            this.form.name = null;
            this.form.image = null;
            this.editMode = false;
        },
        reset: function () {
            this.form = {
                id : null,
                parent_id : null,
                name: null,
                image : null
            }
            this.thumbnail = null;
            this.editMode = false;
            this.addChildButton = false;
            this.$refs.tree.initializeLoading();
        },
        submit() {
            let data = this.form;
            if(!this.editMode){
                this.$inertia.post(this.route('admin.product.category.store'), data, {
                    preserveScroll: true 
                })
            }else{
                this.$inertia.post(this.route('admin.product.category.update'), data, {
                    preserveScroll: true 
                })
            }
            this.reset();
        },
        deleteData() {
            let data = this.form;
            this.$swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: `You won't be able to revert this!`,
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete it',
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete(this.route('admin.product.category.delete', data.id), { 
                        preserveScroll: true 
                    })
                    this.reset();
                }
            })
        },

    }
}
</script>
