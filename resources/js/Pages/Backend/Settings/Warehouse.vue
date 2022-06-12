<template>
    <BaseLayout title="Warehouses">
        <div class="content">
            <div class="content-heading pt-3 mb-3">
                Warehouses
                <div class="float-right">
                    <button v-if="selected.length > 0" type="button" class="btn btn-sm btn-danger" @click="deleteSelected">
                        <i class="si si-trash"></i>
                        Hapus {{ selected.length }} data
                    </button>
                </div>
            </div>
            <div class="row gutter-tiny">
                <div class="col-lg-4">
                    <div class="block block-shadow block-bordered block-rounded">
                        <div class="block-header block-header-default">
                            <h3 class="block-title" id="form-title">{{ title }}</h3>
                        </div>
                        <div class="block-content">
                            <form @submit.prevent="submit">
                                <div class="form-group">
                                    <label class="col-form-label" for="field-name">Name</label>
                                    <input type="text" v-bind:class="{'form-control':true, 'is-invalid' : errors.name}"
                                        id="field-name" v-model="form.name">
                                    <div v-if="errors.name" class="invalid-feedback font-size-sm">{{ errors.name[0] }}</div>
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label" for="field-status">Status</label>
                                    <b-form-radio-group id="field-status" v-model="form.status" name="status">
                                        <b-form-radio value="1">Active</b-form-radio>
                                        <b-form-radio value="0">Inactive</b-form-radio>
                                    </b-form-radio-group>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-noborder btn-block"><i class="si si-check mr-1"></i>Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="block block-rounded block-shadow block-bordered d-md-block d-none mb-10">
                        <div class="block-content p-2">
                            <div class="row justify-content-between">
                                <div class="col-4">
                                    <div class="has-search">
                                        <i class="fa fa-search"></i>
                                        <input type="search" class="form-control" id="search-data-list" v-model="search"
                                            @keyup="doSearch()" @change="doSearch()">
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
                                            <button type="button" class="btn btn-alt-secondary mx-1" @click.prevent="prevPage"
                                                :disabled="dataList.prev_page_url ? false : true">
                                            <i class="fa fa-chevron-left fa-fw"></i>
                                            </button>
                                            <button class="btn btn-alt-secondary mx-1" type="button" @click.prevent="nextPage"
                                                :disabled="dataList.next_page_url ? false : true">
                                            <i class="fa fa-chevron-right fa-fw"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="block block-rounded block-shadow block-bordered mb-5">
                        <div class="block-content p-0">
                            <table class="table table-striped table-vcenter table-hover mb-0 js-table-sections" id="data-list">
                                <thead>
                                    <tr>
                                        <th width="2%">
                                            <b-form-checkbox id="selectAll" name="selectAll" v-model="selectAll"></b-form-checkbox>
                                        </th>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th width="15%">Aksi</th>
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
                                                <b-form-checkbox
                                                    :id="'data-'+data.id"
                                                    v-model="selected"
                                                    :name="'data-'+data.id"
                                                    :value="data.id"
                                                    >
                                                </b-form-checkbox>
                                            </td>
                                            <td>{{ data.name }}</td>
                                            <td>
                                                <span class="badge badge-primary" v-if="data.status == 1">Active</span>
                                                <span class="badge badge-danger" v-else>Inactive</span>
                                            </td>
                                            <td>
                                                <button class="btn btn-secondary btn-sm" v-b-tooltip.hover title="Ubah" @click="edit(data)">
                                                    <i class="si si-note"></i>
                                                </button>
                                                <button class="btn btn-secondary btn-sm" v-b-tooltip.hover title="Hapus" @click="remove(data)">
                                                    <i class="si si-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    <template v-else>
                                        <tr v-if="!Object.values(dataList.data).length">
                                            <td colspan="4" class="text-center">
                                                Data Kosong
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- END Default Elements -->
                </div>
            </div>
        </div>
    </BaseLayout>
</template>

<script>
import BaseLayout from '@/Layouts/Backend/Authenticated.vue';
import _ from 'lodash';
import moment from 'moment';

export default {
    components: {
        BaseLayout,
    },
    data() {
        return {
            loading: false,
            selected: [],
            selectAll: false,
            search: this.route().params.search == '' ||  this.route().params.search == undefined ? '' : this.route().params.search,
            page: this.route().params.page == '' ||  this.route().params.page == undefined ? 1 : this.route().params.page,
            form: {
                'id': null,
                'name': null,
                'status': 1,
            },
            title : 'Add New Warehouse',
            editMode : false,
        }
    },
    watch: {
        search: function () {
            this.page = 1;
            this.doSearch()
        },
        selectAll: function(val){
            this.selected = [];
            if(val){
                this.dataList.data.forEach((value, index) => {
                    this.selected.push(value.id)
                });
            }
        }
    },
    props: {
        errors: Object,
        dataList: Object,
    },
    methods: {
        nextPage: function() {
            if(this.page < this.dataList.total){
                this.page++;
                this.$inertia.get(this.route('admin.settings.warehouse.index', {
                    search: this.search,
                    page : this.page
                }));
            }
        },
        prevPage: function() {
            if(this.page > 1){
                this.page--;
                this.$inertia.get(this.route('admin.settings.warehouse.index', {
                    search: this.search,
                    page : this.page
                }));
            }
        },
        doSearch: _.throttle(function () {
            this.$inertia.get(this.route('admin.settings.warehouse.index', {
                search: this.search
            }))
        }, 200),
        reset() {
            this.form = {
                id: null,
                name: null,
                status: 1,
            };
        },
        submit: function () {
            this.$swal.fire({
                title: 'Please wait...',
                text: '',
                imageUrl: window._asset + 'media/loading.gif',
                showConfirmButton: false,
                allowOutsideClick: false,
            });
            let form = this.$inertia.form(this.form);
            let url = this.editMode ? this.route("admin.settings.warehouse.update") : this.route(
                "admin.settings.warehouse.store");
            form.post(url, {
                preserveScroll: true,
                onSuccess: () => {
                    this.$swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: `Data saved successfully!`,
                        showConfirmButton: false,
                    });
                    this.reset();
                },
                onError:(error) => {
                    this.$swal.close();
                },
            });
        },
        edit: function (data) {
            this.form.id = data.id;
            this.form.name = data.name;
            this.form.status = data.status;
            this.editMode = true;
            this.title = 'Update Warehouse';
        },
        remove: function (data) {
            this.$swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: `You won't be able to revert this!`,
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete it',
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete(this.route('admin.settings.warehouse.delete', data.id), {
                        preserveScroll: true
                    })
                    this.reset();
                }
            })
        },
        format_date(value) {
            if (value) {
                return moment(String(value)).format('DD MMM YYYY')
            }
        },
        select() {
            this.selected = [];
            if (!this.selectAll) {
                this.dataList.data.forEach((value, index) => {
                    this.selected.push(value.id)

                });
            }
        },
        deleteSelected(){

        }
    }
}
</script>
