<template>
    <div class="hospital-create">
        <slot name="header"></slot>

        <div class="row">
            <div class="col-md-12">
                <div class="card-box">
                    <form v-if="!param.requisition_category" action="/requisition-new" method="get">
                        <input type="hidden" name="type" :value="param.type">
                        <div class="row filter-row">
                            <div class="col-sm-3 col-md-2">
                                <div class="form-group form-focus">
                                    <label class="focus-label">Requisition Category</label>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <Select2 class="form-group form-focus"  name="category_id" required placeholder="Select Category" :options="param.categories"/>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <button type="submit" class="btn btn-success btn-block">Next</button>
                            </div>
                        </div>
                    </form>
                    <div v-else class="row">
                        <div class="col-3"> <h4>Requisition Category: <span>{{ param.requisition_category.name }}</span></h4> </div>

                        <form v-on:submit.prevent v-if="param.requisition_category && param.data.status==='INITIATING'" class="form-inline col-9">
                            <label class="sr-only" for="inlineFormInputName2">Product</label>
                            <Select2 class="form-group form-focus col-5"  v-model="itemForm.product_id" placeholder="Select Product" :options="param.products"/>

                            <label class="sr-only" for="inlineFormInputGroupUsername2">Quantity</label>
                            <div class="input-group col-4">
                                <input type="text" class="form-control" placeholder="Quantity" v-model="itemForm.expected_quantity">
                            </div>

                            <button type="button" class="btn btn-primary col-3" wire:click.prevent="addItem()" @click="addItem(param.data, itemForm)">Add To Cart</button>
                        </form>
                        <div v-else class="form-inline col-9">
                            <span class="col-3"><label>Requistion No: <b>{{ param.data.requisition_no }}</b></label></span>
                            <div class="col-9" v-if="isStatus('PROCESSING') || param.image">
                                <div class="form-group">
                                    <label>Attachment &nbsp;
                                    </label>
                                    &nbsp;
                                    <input v-if="isStatus('PROCESSING')" class="form-control" type="file" @change="onFileChange"/>
                                    &nbsp;
                                    <a v-if="param.image" :href="param.image.source" target="_blank">View</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="param.requisition_category && param.data" class="row">
            <div class="col-md-12 col-sm-12">
                <div class="table-responsive">
                    <table class="table table-hover table-white">
                        <thead>
                            <tr>
                                <th style="width: 20px">SL</th>
                                <th>Item Name</th>
                                <th v-if="param.editConfig.priceOpen">TP</th>
                                <th v-if="param.editConfig.priceOpen">MRP</th>
                                <th v-if="param.editConfig.priceOpen">Batch No</th>
                                <th v-if="param.editConfig.priceOpen">Mfg Date</th>
                                <th v-if="param.editConfig.priceOpen">Exp Date</th>
                                <th>Expected Qty</th>
                                <th v-if="param.editConfig.approvedQtyOpen">Approved Qty</th>
                                <th v-if="param.editConfig.deliveredQtyOpen">Delivered Qty</th>
                                <th v-if="param.editConfig.receiveOpen">Received Qty</th>
                                <th v-if="param.editConfig.returnOpen">Return Qty</th>
                                <th v-if="param.editConfig.returnOpen">Damage Qty</th>
                                <th>Remarks</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(row,index) in param.data.items" :key="index" :class="[row.status=='CANCELED'? 'canceled':'']" :title="[row.status=='CANCELED'? 'Removed this item.':'']">
                                <td>{{ index + 1 }}</td>
                                <td>
                                    {{ row.product.name }} ({{ row.product.code }})
                                </td>
                                <td v-if="param.editConfig.priceOpen">
                                    <input :disabled="param.editConfig.isDelivered" class="form-control" style="width:100px" v-model="row.tp">
                                </td>
                                <td v-if="param.editConfig.priceOpen">
                                    <input :disabled="param.editConfig.isDelivered" class="form-control" style="width:100px" v-model="row.mrp">
                                </td>
                                <td v-if="param.editConfig.priceOpen">
                                    <input :disabled="param.editConfig.isDelivered" class="form-control" style="width:100px" v-model="row.batch_no">
                                </td>
                                <td v-if="param.editConfig.priceOpen">
                                    <date-picker :disabled="param.editConfig.isDelivered" v-model="row.mfg_date" valueType="format"></date-picker>
                                </td>
                                <td v-if="param.editConfig.priceOpen">
                                    <date-picker :disabled="param.editConfig.isDelivered" v-model="row.exp_date" valueType="format"></date-picker>
                                </td>
                                <td>
                                    <input :disabled="param.data.status != 'INITIATING'" class="form-control" style="width:100px" v-model="row.expected_quantity">
                                </td>
                                <td v-if="param.editConfig.approvedQtyOpen">
                                    <input :disabled="!isStatus('APPROVED_BY_GM')" class="form-control" style="width:100px" v-model="row.approved_quantity">
                                </td>
                                <td v-if="param.editConfig.deliveredQtyOpen">
                                    <input :disabled="param.editConfig.isDelivered || isCanceled(row)" class="form-control" style="width:100px" v-model="row.delivered_quantity">
                                </td>
                                <td v-if="param.editConfig.receiveOpen">
                                    <input :disabled="param.data.status=='RECEIVED' || isCanceled(row)" class="form-control" style="width:100px" v-model="row.received_quantity">
                                </td>
                                <td v-if="param.editConfig.returnOpen">
                                    <input class="form-control" style="width:100px" type="number" v-model="row.return_quantity">
                                </td>
                                <td v-if="param.editConfig.returnOpen">
                                    <input class="form-control" style="width:100px" type="number" v-model="row.damaged_quantity">
                                </td>
                                 <td>
                                    <input :disabled="param.data.status != 'INITIATING'" class="form-control" maxlength="500" v-model="row.remarks">
                                </td>
                                <td>
                                    <a v-if="param.editConfig.deleteOpen" href="javascript:void(0)" @click="deleteRow(row)" class="text-danger font-18" title="Remove"><i class="fa fa-trash-o"></i></a>
                                    &nbsp;
                                    <div v-if="param.editConfig.cancelOpen">
                                        <span class="custom-badge status-orange" v-if="row.status==='CANCELED'">{{ row.status }}</span>
                                        <a v-else href="javascript:void(0)" @click="cancelRow(row)" class="text-danger font-18" title="Remove"><i class="fa fa-trash-o"></i></a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <hr>
                <form class="mt-5" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label>Expected Date(EDA)</label><br>
                                <date-picker v-if="param.data.status==='INITIATING'" v-model="form.expected_date" valueType="format"></date-picker>
                                <span v-else><b>{{ param.data.expected_date }}</b></span>
                            </div>
                        </div>
                        <div v-if="param.editConfig.priceOpen" class="col-sm-2">
                            <div class="form-group">
                                <label>Supplier</label>
                                <select :disabled="param.editConfig.isDelivered" class="form-control select"  v-model="form.supplier_id">
                                    <option value="" selected>-- Select Supplier --</option>
                                    <option  v-for="row in param.suppliers" :key="row.id" :value="row.id">{{ row.name }}</option>
                                </select>
                            </div>
                        </div>
                        <div v-if="param.purchaseTypes && param.data.type == 1 && param.editConfig.approvedQtyOpen" class="col-sm-2">
                            <div class="form-group">
                                <label>Purchase Type</label>
                                <select :disabled="!param.editConfig.processingOpen" class="form-control select"  v-model="form.purchase_type">
                                    <option  v-for="(value, key, index) in param.purchaseTypes" :key="index" :value="key">{{value }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Remarks</label>
                                <textarea rows="6" class="form-control" v-model="form.remarks"></textarea>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="row">
                     <div v-if="param.data.nextStatus && param.total_item > 0" class="col-md-3">
                        <button type="button" @click="submitStatus(param.data.id, param.data.nextStatus.key)" class="btn btn-success btn-block"> {{ param.data.nextStatus.value }} </button>
                    </div>
                    <div v-else class="col-3">
                        <h4>Requisition Status: <b>{{ param.data.status }}</b></h4>
                    </div>
                    <div class="col-3">
                        <button type="button" @click="submitRequisition(param.data.id)" class="btn btn-info btn-block"> Save </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Alert from '@/Pages/Component/Alert'
import Select2 from 'v-select2-component';

export default {
    components: {
        Alert,
        Select2
    },
    props: ['param', 'editMode'],
    data () {
        return {
            image: null,
            itemForm: {
                requisition_id: '',
                product_id: '',
                expected_quantity: null,
                requisition_category_id: null
            },
            form: {
                status: undefined,
                expected_date: undefined,
                supplier_id: '',
                remarks: null,
                purchase_type: undefined
            }
        }
    },
    created () {
        if (this.editMode) {
            this.form.expected_date = this.param.data.expected_date
            this.form.remarks = this.param.data.remarks
            this.form.supplier_id = this.param.data.supplier_id
            this.form.purchase_type = this.param.data.purchase_type
        }
    },
    methods: {
        reset: function () {
            this.itemForm = {
                product_id: '',
                expected_quantity: null,
            }
        },
        save: function (data) {
            this.$inertia.post('/requisitions', data)
            this.reset()
        },
        addItem: function (paramData, data) {
            this.itemForm.requisition_id = paramData.id
            this.itemForm.requisition_category_id = paramData.requisition_category_id
            this.$inertia.post('/requisition-item-add', this.itemForm)
            this.reset()
        },
        cancelRow: function (data) {
            if (!confirm('Are you sure want to remove?')) return;
            data._method = 'PUT';
            this.$inertia.post('/requisition-item-cancel/' + data.id, data)
        },
        deleteRow: function (data) {
            if (!confirm('Are you sure want to remove?')) return;
            data._method = 'DELETE';
            this.$inertia.post('/requisition-item-delete/' + data.id, data)
        },
        submitRequisition: function (id) {
            let formData = new FormData();

            this.form._method = 'PUT'
            this.form.id = id
            this.form.items = this.param.data.items

            formData.append('form', this.form);
            formData.append('file', this.image);

            this.$inertia.post('/requisitions/' + id, this.form)
        },
        submitStatus: function (id, status) {
            this.form._method = 'PUT'
            this.form.id = id
            this.form.status = status
            this.form.items = this.param.data.items

            this.$inertia.post('/requisitions/' + id, this.form)
        },
        isStatus: function (status) {
            if (this.param.data.status === status) {
                return true
            }

            return false
        },
        isCanceled: function (row) {
            if (row.status === 'CANCELED') {
                return true
            }

            return false
        },
        onFileChange: function (e) {
            this.image = e.target.files[0]

            var data = new FormData()
            data.append('file', this.image || '')

            this.$inertia.post('/requisition-upload-image/' + this.param.data.id, data)
        },
    }
}
</script>

<style>
.form-focus .focus-label {
    font-size: 16px;
    font-weight: 400;
    opacity: 0.4;
    pointer-events: none;
    position: absolute;
    -webkit-transform: translate3d(0, 22px, 0) scale(1);
    -ms-transform: translate3d(0, 22px, 0) scale(1);
    -o-transform: translate3d(0, 22px, 0) scale(1);
    transform: translate3d(0, 22px, 0) scale(1);
    transform-origin: left top;
    transition: 240ms;
    left: 12px;
    top: -8px;
    z-index: 1;
    color: black;
    font-weight: bold;
}
.select2.select2-container.select2-container--default {
    width: 100%!important;
}
.canceled {
    background: #eca6a6;
}
</style>