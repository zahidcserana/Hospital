<template>
    <div class="hospital-create">
        <slot name="header"></slot>
        <div class="row card-box">
            <div class="col-lg-8 offset-lg-2">
                <form>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label> Patient</label>

                                <Select2
                                    v-model="form.patient_id"
                                    placeholder="Select Patient"
                                    :options="data.patients"/>

                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label> Billing Type</label>
                                <select class="form-control select"  v-model="form.billing_type"   @change="getUnpaidBills()">
                                    <option value="" selected>Select Billing Type</option>
                                    <option  v-for="row in billing_types" :key="row"  :value="row">{{ row }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label> Bill  </label>
                                <select class="form-control select"  v-model="form.billing_id"  @change="onSelectBill()">
                                    <option value="" selected>Select Bill</option>
                                    <option  v-for="row in bills" :key="row.id"  :value="row.id">{{ row.bill_no }}</option>
                                </select>
                                <div class="card bg-light mb-3" v-if="bill">
                                    <div class="card-body">
                                        <h5 class="card-title">Bill Details</h5>
                                        <p class="card-text">Bill No.: {{bill.bill_no}}</p>
                                        <p class="card-text">Bill Amount: {{bill.total_bill_amount}}</p>
                                        <p class="card-text">Discount Amount: {{bill.total_discount_amount}}</p>
                                        <p class="card-text">Paid Amount: {{bill.total_paid_amount}}</p>
                                        <p class="card-text bold">Due Amount: {{bill.total_due_amount}}</p>
                                        <p class="card-text">Date: {{bill.created_at | formatDateTime}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Payment Amount</label>
                                <input class="form-control" type="number" v-model="form.payment_amount"/>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label> Payment Type</label>
                                <select class="form-control select"  v-model="form.payment_method" >
                                    <option value="" selected>Select Payment Type</option>
                                    <option  v-for="row in payment_types" :key="row"  :value="row">{{ row }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label> Collection Type</label>
                                <select class="form-control select"  v-model="form.collection_type" >
                                    <option value="Regular">Regular</option>
                                    <option value="Advance">Advance</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label> Remarks</label>
                                <textarea
                                    v-model="form.remarks"
                                    cols="30"
                                    rows="4"
                                    class="form-control"
                                ></textarea>
                            </div>
                        </div>


                        <div class="col-sm-6">
                            <div class="form-group">
                                <label> Status</label>
                                <select class="form-control select"  v-model="form.status" >
                                    <option value="" selected>Payment Status</option>
                                    <option  v-for="row in status" :key="row"  :value="row">{{ row }}</option>
                                </select>
                            </div>
                        </div>

                    </div>



                    <div class="m-t-20 text-center">
                        <button
                            type="button"
                            class="btn btn-primary submit-btn"
                            wire:click.prevent="store()"
                            v-show="!editMode"
                            :disabled="form_disable"
                            @click="save(form)"
                        >
                            Save
                        </button>
                        <button
                            type="button"
                            class="btn btn-primary submit-btn"
                            wire:click.prevent="store()"
                            v-show="editMode"
                            :disabled="form_disable"
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
import {HTTP} from '@/utils/http-common'
import Select2 from 'v-select2-component';

export default {
    components: {
        Alert, Select2
    },
        props: ['data', 'errors', 'editMode'],
        data() {
            return {
                form_disable: false,
                form: {
                    id: undefined,
                    patient_id: this.data.patient_id ? this.data.patient_id : '',
                    collection_type: 'Regular',
                    billing_type: '',
                    billing_id: '',
                    payment_method: 'CASH',
                    payment_date: new Date(),
                    status: 'PARTIAL',
                },
                bills: [],
                bill: '',
                billing_types: ['OPD', 'IPD'],
                payment_types: ['CASH', 'CARD', 'MBANKING'],
                status: ['FULL', 'PARTIAL'],
            }
        },
        created() {
            if (this.editMode) {
                this.form = this.data;
                this.getUnpaidBills();
            }
        },
        methods: {
            onSelectBill: function(){
                var bill = this.bills.find(item => item.id == this.form.billing_id);
                this.bill = bill;
            },
            reset: function () {
                this.form = {
                    id: undefined,
                    patient_id: this.data.patient_id ? this.data.patient_id : '',
                    collection_type: 'Regular',
                    billing_type: '',
                    billing_id: '',
                    payment_method: 'CASH',
                    payment_date: new Date(),
                    status: 'PARTIAL',
                }
                this.form_disable = false;
                this.bill = '';
            },
            save: function (data) {
                this.form_disable = true;
                this.$inertia.post('/payments', data)
                this.reset();
            },
            edit: function (data) {
                this.form = Object.assign({}, data);
                this.editMode = true;
                this.openModal();
            },
            update: function (data) {
                this.form_disable = true;
                data._method = 'PUT';
                this.$inertia.post('/payments/' + data.id, data)
            },
            getUnpaidBills: function (){
                this.billing_id = '';
                this.bill = '';
                HTTP.get('/patients/'+this.form.patient_id+'/unpaidBills/'+this.form.billing_type)
                    .then((response)=>{
                        this.bills = response.data.bills
                    })
            }
        }
    }
</script>
