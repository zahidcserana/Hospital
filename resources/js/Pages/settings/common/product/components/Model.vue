<template>
    <div class="hospital-create">
        <slot name="header"></slot>
        <div class="row  card-box">
            <div class="col-lg-8 offset-lg-2">
                <form>
                    <div class="row">

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label> Name</label>
                                <input class="form-control" type="text" v-model="form.name"/>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label> Generic Name</label>
                                <input class="form-control" type="text" v-model="form.generic_name"/>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label> Measurement Unit</label>
                                <input class="form-control" type="text" v-model="form.measurement_unit"/>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Category</label>
                                <select class="form-control select"  v-model="form.product_category_id">
                                    <option value="" selected>Select Category</option>
                                    <option  v-for="row in data.categories" :key="row.id" :value="row.id">{{ row.name }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Brand</label>
                                <select class="form-control select"  v-model="form.product_brand_id">
                                    <option value="" selected>Select Brand</option>
                                    <option  v-for="row in data.brands" :key="row.id" :value="row.id">{{ row.name }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Model</label>
                                <select class="form-control select"  v-model="form.product_model_id">
                                    <option value="" selected>Select Model</option>
                                    <option  v-for="row in data.models" :key="row.id" :value="row.id">{{ row.name }}</option>
                                </select>
                            </div>
                        </div>


                        <div class="col-sm-6">
                            <div class="form-group">
                                <label> Barcode No.</label>
                                <input class="form-control" type="text" v-model="form.barcode_no"/>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label> Description</label>
                                <textarea
                                    v-model="form.description"
                                    cols="30"
                                    rows="4"
                                    class="form-control"
                                ></textarea>
                            </div>
                        </div>
                    </div>


                    <div class="col-sm-6">
                        <div class="form-group">
                            <label> Status</label><br/>
                            <div class="form-check form-check-inline">
                                <input
                                    v-model="form.status"
                                    class="form-check-input"
                                    type="radio"
                                    name="status"
                                    value="ACTIVE"
                                />
                                <label
                                    class="form-check-label"
                                    for="product_active"
                                >
                                    Active
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input
                                    v-model="form.status"
                                    class="form-check-input"
                                    type="radio"
                                    name="status"
                                    value="INACTIVE"
                                />
                                <label
                                    class="form-check-label"
                                    for="product_inactive"
                                >
                                    Inactive
                                </label>
                            </div>
                        </div>
                    </div>


                    <div class="m-t-20 text-center">
                        <button
                            type="button"
                            class="btn btn-primary submit-btn"
                            wire:click.prevent="store()"
                            v-show="!editMode"
                            @click="save(form)"
                        >
                            Save
                        </button>
                        <button
                            type="button"
                            class="btn btn-primary submit-btn"
                            wire:click.prevent="store()"
                            v-show="editMode"
                            @click="update(form)"
                        >
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
 import Alert from "@/Pages/Component/Alert";

 export default {
     components: {
         Alert
     },
        props: ['data', 'errors', 'editMode'],
        data() {
            return {
                isOpen: false,
                form: {
                    id: undefined,
                    name: null,
                    barcode_no: null,
                    generic_name: null,
                    measurement_unit: null,
                    description: null,
                    product_category_id: '',
                    product_brand_id: '',
                    product_model_id: '',
                    status: 'ACTIVE',
                },
            }
        },
        created() {
            if (this.editMode) {
                this.form.id = this.data.id
                this.form.barcode_no = this.data.barcode_no
                this.form.name = this.data.name
                this.form.generic_name = this.data.generic_name
                this.form.measurement_unit = this.data.measurement_unit
                this.form.description = this.data.description
                this.form.product_category_id = this.data.product_category_id
                this.form.product_brand_id = this.data.product_brand_id
                this.form.product_model_id = this.data.product_model_id
                this.form.status = this.data.status
            }
        },
        methods: {
            reset: function () {
                this.form = {
                    name: null,
                    barcode_no: null,
                    generic_name: null,
                    measurement_unit: null,
                    description: null,
                    product_category_id: '',
                    product_brand_id: '',
                    product_model_id: '',
                    status: 'ACTIVE',
                }
            },
            save: function (data) {
                this.$inertia.post('/products', data)
            },
            edit: function (data) {
                this.form = Object.assign({}, data);
                this.editMode = true;
                this.openModal();
            },
            update: function (data) {
                data._method = 'PUT';
                this.$inertia.post('/products/' + data.id, data)
            }
        }
    }
</script>
