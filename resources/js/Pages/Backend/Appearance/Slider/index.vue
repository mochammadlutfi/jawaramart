<template>
    <BaseLayout title="Slider List">
        <div class="content">
            <div class="content-heading pt-0 mb-3">
                Sliders
                <div class="float-right">
                    <button v-if="selected.length > 0" type="button" class="btn btn-sm btn-danger" >
                        <i class="si si-trash"></i>
                        Delete {{ selected.length }} Selected
                    </button>
                    <button class="btn btn-sm btn-secondary" type="button" @click="openModal">
                        <i class="si si-plus"></i>
                        Create
                    </button>
                </div>
            </div>
            <div class="block block-rounded block-shadow block-bordered d-md-block d-none mb-10">
                <div class="block-content p-2">
                    <div class="row justify-content-between">
                        <div class="col-4">
                            <div class="has-search">
                                <i class="fa fa-search"></i>
                                <input type="search" class="form-control" id="search-data-list" v-model="search" @keyup="doSearch()" @change="doSearch()">
                            </div>
                        </div>
                        <div class="col-4">
                        </div>
                        <div class="col-4">
                            <div class="d-flex float-right">
                                <div class="my-auto px-3">
                                    <span>{{ dataList.from }}-{{ dataList.to }}/{{ dataList.total }}</span>
                                </div>
                                <div class="pt-25 pl-0">
                                    <Link :href="dataList.prev_page_url ? dataList.prev_page_url : '#'" as="button" class="btn btn-alt-secondary mx-1" type="button"
                                    :disabled="dataList.prev_page_url ? false : true">
                                        <i class="fa fa-chevron-left fa-fw"></i>
                                    </Link>
                                    <Link :href="dataList.next_page_url ? dataList.next_page_url : '#'" as="button" class="btn btn-alt-secondary mx-1" type="button"
                                    :disabled="dataList.next_page_url ? false : true">
                                        <i class="fa fa-chevron-right fa-fw"></i>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="block block-rounded block-shadow-2 block-bordered mb-5">
                <div class="block-content px-0 py-0">
                    <table class="table table-striped table-vcenter table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th width="2%">
                                    <div class="custom-control custom-checkbox mb-5">
                                        <input class="custom-control-input" type="checkbox" id="checkAll" v-model="selectAll" @click="select">
                                        <label class="custom-control-label" for="checkAll"></label>
                                    </div>
                                </th>
                                <th>Image</th>
                                <th>Link</th>
                                <th width="15%">State</th>
                                <th>Created At</th>
                                <th width="10%"></th>
                            </tr>
                        </thead>
                        <tbody v-if="loading">
                            <tr>
                                <td colspan="5">
                                    <div class="text-center py-50">
                                        <div class="spinner-border text-primary wh-50" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <template v-if="Object.values(dataList.data).length">
                                <tr v-for="data in dataList.data" :key="data.id">
                                    <td>
                                        <label class="custom-control custom-checkbox">
                                            <input class="custom-control-input" :id="data.id" type="checkbox" :value="data.id" v-model="selected">
                                            <label class="custom-control-label" :for="data.id"></label>
                                        </label>
                                    </td> 
                                    <td> <img class="media-object" :src="data.image_url" style="max-width:150px"></td>
                                    <td>{{ data.url }}</td>
                                    <td>
                                        <span class="badge badge-primary" v-if="data.state == 1">Active</span>
                                        <span class="badge badge-danger" v-else>Inactive</span>
                                        </td>
                                    <td>{{ format_date(data.created_at) }}</td>
                                    <th>
                                        <b-button variant="secondary" v-b-tooltip.hover.nofade.top="'Edit'"  @click="edit(data)" size="sm">
                                            <i class="si si-note"></i>
                                        </b-button>
                                        <b-button variant="secondary" v-b-tooltip.hover.nofade.top="'Delete'"  @click="destroy(data)" size="sm">
                                            <i class="si si-trash"></i>
                                        </b-button>
                                    </th>
                                </tr>
                            </template>
                            <template v-else>
                                <tr v-if="!Object.values(dataList.data).length">
                                    <td>Data Kosong</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <b-modal v-model="modalShow" size="md" rounded body-class="p-0" centered hide-footer hide-header>
            <div class="block block-rounded  block-transparent mb-0">
                <form @submit.prevent="submit">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">{{ title }}</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" @click="reset">
                                <i class="fa fa-fw fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content font-size-sm">
                        <div class="form-group ">
                            <label for="field-link">Link</label>
                            <input type="text" v-bind:class="{'form-control':true, 'is-invalid' : errors.link}"
                                id="field-link" v-model="form.link">
                            <div v-if="errors.link" class="invalid-feedback font-size-sm">{{ errors.link[0] }}</div>
                        </div>

                        <b-form-group label="State" v-slot="{ ariaDescribedby }">
                            <b-form-radio-group id="field-state" v-model="form.state"
                                :aria-describedby="ariaDescribedby" name="state">
                                <b-form-radio value="1">Active</b-form-radio>
                                <b-form-radio value="0">Inactive</b-form-radio>
                            </b-form-radio-group>
                        </b-form-group>
                        <div class="form-group">
                            <label for="field-name">Image</label>
                            <div class="row">
                                <div class="col-12">
                                    <ImageUpload
                                        :image="(typeof form.image === 'string') ? asset(form.image) : null"
                                        :imageWidth="1200" 
                                        :imageHeight="400"
                                        :height="152"
                                        :ratio="12/4"
                                        @done="(image) => form.image = image"
                                        @removeImage="(image) => form.image = null" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="block-content block-content-full text-right border-top">
                        <b-button variant="alt-danger" class="mr-1" @click="reset">cancel</b-button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </b-modal>
    </BaseLayout>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue';
import BaseLayout from '@/Layouts/Backend/Authenticated.vue';
import ImageUpload from '@/components/SingleImageUpload.vue';
import moment from 'moment';
import _ from 'lodash';
export default {
    components: {
        BaseLayout,
        Link,
        ImageUpload
    },
    data(){
        return {
            loading : false,
            selected: [],
            selectAll: false,
            search : this.route().params.search == '' ? '' : this.route().params.search,
            form : {
                image : null,
                state : 1,
                link : null,
            },
            title: 'Add New Slider',
            modalShow: false,
            editMode: false,
        } 
    },
    mounted() {
    },
    watch: {
        search: function () {
            this.doSearch() 
        }
    },
    props: {
        dataList: Object,
        errors : Object,
    },
    methods :{
        reset() {
            this.form = {
                id: null,
                link: null,
                image: null,
                state: 1,
            };
            this.modalShow = false;
            this.editMode = true;
        },
        openModal() {
            this.form.id = null;
            this.form.link = null;
            this.form.image = null;
            this.form.state = 1;
            this.editMode = false;
            this.modalShow = true;
        },
        submit: function () {
            let form = this.$inertia.form(this.form)
            let url = this.editMode ? this.route("admin.appearance.slider.update") : this.route(
                "admin.appearance.slider.store");
            form.post(url, {
                preserveScroll: true,
                onSuccess: () => {
                    this.$swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: `Slider Saved Successfully!`,
                    });
                    this.reset();
                },
            });
        },
        edit: function (data) {
            this.form.id = data.id;
            this.form.link = data.url;
            this.form.image = data.image;
            this.form.state = data.state;
            this.editMode = true;
            this.modalShow = true;
        },
        format_date(value){
            if (value) {
            return moment(String(value)).format('DD MMM YYYY')
            }
        },
        doSearch : _.throttle(function(){
            this.$inertia.get(this.route('admin.appearance.slider.index', { search : this.search }), {
                preserveScroll : true,
            })
        }, 200),
        select() {
			this.selected = [];
			if (!this.selectAll) {
                this.dataList.data.forEach((value, index) => {
                    this.selected.push(value.id)
                    
                });
			}
		},
        destroy: function (data) {
            this.$swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: `You won't be able to revert this!`,
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete it',
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete(this.route('admin.appearance.slider.delete', data.id), {
                        preserveScroll: true,
                        onSuccess: () => {
                            this.$swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: `Data deleted successfully!`,
                                showConfirmButton : false,
                            });
                            this.reset();
                        },
                    })
                    this.reset();
                }
            })
        },
    }
}
</script>
