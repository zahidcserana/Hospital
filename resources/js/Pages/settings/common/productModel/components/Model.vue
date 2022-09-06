<template>
    <div class="hospital-create">
        <slot name="header"></slot>

        <div class="row card-box">
            <div class="col-lg-8 offset-lg-2">
                <form>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Name</label>
                                <input
                                    class="form-control"
                                    type="text"
                                    v-model="form.name"
                                />
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Product Category</label>
                                <select class="form-control"  v-model="form.product_category_id">
                                    <option value="" selected>Select Product Category</option>
                                    <option  v-for="category in data.categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Product Brand</label>
                                <select class="form-control"  v-model="form.product_brand_id">
                                    <option value="" selected>Select Product Brand</option>
                                    <option  v-for="category in data.brands" :key="category.id" :value="category.id">{{ category.name }}</option>
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label> Status</label><br />
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
import Alert from '@/Pages/Component/Alert'
export default {
    components: {
        Alert
    },
    props: ['data', 'editMode'],
    data () {
        return {
            form: {
                id: undefined,
                name: null,
                product_category_id: '',
                product_brand_id: '',
                description: '',
                status: 'ACTIVE'
            }
        }
    },
    created () {
        if (this.editMode) {
            this.form.id = this.data.id
            this.form.name = this.data.name
            this.form.product_category_id = this.data.product_category_id
            this.form.product_brand_id = this.data.product_brand_id
            this.form.description = this.data.description
            this.form.status = this.data.status
        }
    },
    methods: {
        reset: function () {
            this.form = {
                name: null,
                product_category_id: '',
                product_brand_id: '',
                description: '',
                status: 'ACTIVE'
            }
        },
        save: function (data) {
            this.$inertia.post('/product-models', data)
            this.reset()
        },
        update: function (data) {
            data._method = 'PUT'
            this.$inertia.post('/product-models/' + data.id, data)
        }
    }
}
</script>
