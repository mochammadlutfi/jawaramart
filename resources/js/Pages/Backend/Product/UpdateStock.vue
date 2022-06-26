<template>
    <b-modal id="product-update-stock" ref="product-update-stock" size="md" content-class="rounded" body-class="p-0" no-close-on-backdrop hide-footer hide-header>
        <div class="block block-rounded block-transparent mb-0">
            <div class="block-header block-header-default">
                <h3 class="block-title">Update Stock
                    <!-- {{  }} -->
                    <template v-if="product">
                         {{ product.name }}
                    </template>
                </h3>
                <div class="block-options">
                    <button type="button" class="btn-block-option" @click="closeModal">
                        <i class="fa fa-fw fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="block-content block-content-full" v-if="product">
                <div class="border-bottom mb-10 mx-0 pb-10 row">
                    <div class="col-2">
                        <img class="img-fluid" :src="product.main_image">
                    </div>
                    <div class="col-10">
                        <div class="font-size-h6 mb-0">{{ product.name }}</div>
                        <div>{{ product.category.name }}</div>
                    </div>
                </div>
                <template v-if="product.variant.length > 1">
                </template>
                <template v-else>
                    <div class="form-group">
                        <label for="field-stock">Current Stock</label>
                        <input type="number" id="field-stock" class="form-control" v-model="line[0].stock">
                    </div>
                </template>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-noborder" @click="closeModal">
                <i class="fa fa-times"></i> Cancel
            </button>
            <button type="button" class="btn btn-primary btn-noborder" @click="updateStock">
                <i class="fa fa-check"></i> Save
            </button>
        </div>
    </b-modal>
</template>
<script>
export default {
    name : 'product-update-stock',
    data(){
        return {
            product : null,
            line : [],
        }
    },
    methods : {
        openModal(value){
            this.product = value;

            value.variant.forEach((value, index) => {
                let line = {
                    product_id : value.product_id,
                    variant_id : value.id,
                    stock : value.current_stock,
                }
                this.line.push(line);
            });
            this.$bvModal.show('product-update-stock');
        },
        closeModal(){
            this.product = null;
            this.line = [];
            this.$bvModal.hide('product-update-stock');
        },
        updateStock(){
            let data = {
                line : this.line
            };

            let form = this.$inertia.form(data)
            let url = this.route("admin.product.update_stock");
            this.$swal.fire({
                title: 'Please Wait...',
                text: '',
                imageUrl: window._asset + 'media/loading.gif',
                showConfirmButton: false,
                allowOutsideClick: false,
            });
            form.post(url, {
                preserveScroll: true,
                resetOnSuccess: false,
                onSuccess: () => {
                    this.$swal.close();
                    this.closeModal();
                    return this.$swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: `Product stock has been successfully updated`,
                        showCancelButton: false,
                        showConfirmButton: false,
                    });
                },
                onError :() => {
                    this.$swal.close();
                }
            });
        }
    }
}
</script>