
<template>
    <div class="hospital-create">
        <slot name="header"></slot>
        <div class="row card-box">
            <div class="col-lg-12">
                <form>
                    <div class="row">

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label> Patient</label>

                                <Select2
                                    :disabled=" refund == 1? true : false" class="col-sm-4" v-model="form.patient_id" placeholder="Select Patient" :options="data.patients"  @change="onSelectGetItems()"/>

<!--                                <select class="form-control select"  v-model="form.patient_id" @change="onSelectGetItems()">-->
<!--                                    <option value="" selected>Select Patient</option>-->
<!--                                    <option  v-for="row in data.patients" :key="row.id" :value="row.id">{{ row.name }}</option>-->
<!--                                </select>-->
                                <p class="text-success" v-if="patient">
                                    <span v-if="patient.corporate_client">Corporate Client: <b>{{patient.corporate_client.company_name}}</b></span>
                                </p>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="col-sm-6"> Consultant</label>
                                <inertia-link :href="route('doctors.index')">Create doctor</inertia-link>

                                <Select2
                                    :disabled=" refund == 1? true : false" v-model="form.doctor_id" placeholder="Select Doctor" :options="data.doctors"  />
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="col-sm-6"> Referred Personal</label>
                                <Select2
                                    :disabled=" refund == 1? true : false" v-model="form.referred_personal_id" placeholder="Select Referred Personal" :options="data.doctors"  />
                            </div>
                        </div>

                        <div class="col-sm-3"  v-if="refund != 1">
                            <div class="form-group">
                                <label> Item Type</label>
                                <Select2 style="width: 220px !important;" v-model="form.item_type"  :options="item_types"   @change="getServices()"/>
<!--                                <select class="form-control select"  v-model="form.item_type"  >-->
<!--                                    <option  v-for="row in item_types" :key="row"  :value="row">{{ row }}</option>-->
<!--                                </select>-->
                            </div>
                        </div>

                        <div class="col-sm-4"  v-if="refund != 1">
                            <div class="form-group">
                                <label> Item</label>
                                <Select2  v-model="form.item_id"  placeholder="Select Item"  :options="items"  @change="onSetItem()"/>
<!--                                <select class="form-control select"  v-model="form.item_id">-->
<!--                                    <option value="" selected>Select Item</option>-->
<!--                                    <option  v-for="row in items" :key="row.id"  :value="row.id">{{ row.code }}-{{ row.name }}</option>-->
<!--                                </select>-->
                            </div>
                        </div>

                        <div class="col-sm-2" v-if="item && item.is_for_pharmacy == 1">
                            <div class="form-group">
                                <label> Pharmacy Invoice Number</label>
                                <input type="text" class="form-control"  v-model="form.pharmacy_invoice_number">                            </div>
                        </div>

                        <div class="col-sm-2" v-if="item && item.is_for_pharmacy == 1">
                            <div class="form-group">
                                <label> Pharmacy Bill Amount</label>
                                <input type="number" class="form-control" min="1"  v-model="form.pharmacy_bill_amount">                            </div>
                        </div>

                        <div class="col-sm-2"  v-if="item && ( (item.is_for_pharmacy && item.is_for_pharmacy == 0) || (!item.is_for_pharmacy) )">
                            <div class="form-group">
                                <label> Item Quantity</label>
                                <input type="number" class="form-control" min="1" v-model="form.item_qty">
                            </div>
                        </div>

                        <div class="col-sm-2"  v-if="refund != 1">
                            <div class="form-group">
                                <label> &nbsp;</label><br/>
                                <button
                                    type="button"
                                    class="btn btn-primary submit-btn"
                                    wire:click.prevent="store()"
                                    @click="addItem()">Add</button>
                            </div>
                        </div>
                    </div>
                </form>
                <table class="table table-default">
                    <thead>
                    <tr>
                        <th>Type</th>
                        <th>Service Code</th>
                        <th>Service</th>
                        <th class="text-center">Tariff</th>
                        <th>Quantity</th>
                        <th>Discount Amount</th>
                        <th class="text-center">Sub Total</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr  v-for="row in form.items" :key="row.id">
                        <td><strong>{{ row.item_type }}</strong></td>
                        <td><strong>{{row.item ? row.item.code : null}}</strong></td>

                        <td > <strong>{{ row.item_name }}</strong> </td>

                        <td class="text-center"><strong>{{ row.unit_tarrif }}</strong></td>

                        <td  class="text-center">
                            <input  :disabled="row.item.is_for_pharmacy == 1 || refund == 1 ? true : false"  type="number" class="form-control" min="1" v-model="row.item_qty" @change="updateItem(row)">
                        </td>

                        <td  class="text-center">
                            <input  :disabled="row.item.is_for_pharmacy == 1 || refund == 1 ? true : false"   type="number" class="form-control" min="0" v-model="row.discount" @change="updateItem(row)">
                        </td>

                        <td class="text-center"><strong>{{ (row.unit_tarrif * row.item_qty) - row.discount }}</strong></td>
                        <td>
                            <button type="button"
                                    class="btn btn-danger"
                                    wire:click.prevent="store()"
                                    :disabled="refund == 1? true : false"
                                    @click="removeItem(row)">
                                <span class="fa fa-trash"></span>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>   </td>
                        <td>   </td>
                        <td>   </td>
                        <td>   </td>
                        <td>   </td>
                        <td><h5>Discount</h5></td>
                        <td class="text-right"><h5><strong>{{ getTotalDiscount() }}</strong></h5></td>
                    </tr>
                    <tr>
                        <td>   </td>
                        <td>   </td>
                        <td>   </td>
                        <td>   </td>
                        <td>   </td>
                        <td><h5>Sub Total</h5></td>
                        <td class="text-right"><h5><strong>{{ getTotal() }}</strong></h5></td>
                    </tr>
                    <tr>
                        <td>   </td>
                        <td>   </td>
                        <td>   </td>
                        <td>   </td>
                        <td>   </td>
                        <td> Special Discount Type </td>
                        <td>
                            <select class="form-control select"  v-model="form.discount_type"  :disabled=" refund == 1? true : false">
                                <option  v-for="row in discount_types" :key="row"  :value="row">{{ row }}</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>   </td>
                        <td>   </td>
                        <td>   </td>
                        <td>   </td>
                        <td>   </td>
                        <td> Special Discount Value </td>
                        <td>
                            <input class="form-control"  :disabled=" refund == 1? true : false" min="0" type="number" v-model="form.discount_value" />
                        </td>
                    </tr>
                    <tr>
                        <td>   </td>
                        <td>   </td>
                        <td>   </td>
                        <td>   </td>
                        <td>   </td>
                        <td> <h5>Special Discount Amount</h5> </td>
                        <td class="text-right"><h5><strong>{{ getSpecialDiscount() }}</strong></h5></td>
                    </tr>
                    <tr>
                        <td>   </td>
                        <td>   </td>
                        <td>   </td>
                        <td>   </td>
                        <td>   </td>
                        <td> <h3>Total</h3> </td>
                        <td class="text-right"><h5><strong>{{ getGrandTotal() }}</strong></h5></td>
                    </tr>
                    <tr  v-if="refund == 1">
                        <td>   </td>
                        <td>   </td>
                        <td>   </td>
                        <td>   </td>
                        <td>   </td>
                        <td> <h5>Refund Amount</h5> </td>
                        <td class="text-right"><input  :disabled="approve == 1? true : false"  type="number" min="0" class="form-control" :max="form.total_bill_amount" v-model="form.refund_amount"></td>
                    </tr>
                    <tr  v-if="refund == 1 && form.refund_status != 0 ">
                        <td>   </td>
                        <td>   </td>
                        <td>   </td>
                        <td>   </td>
                        <td>   </td>
                        <td> <h5>Refund Approve Status</h5> </td>
                        <td class="text-right"><strong>{{ form.refund_status == 1 ? "Approved" : form.refund_status == 2 ? 'Rejected' : null}}</strong></td>
                    </tr>
                    </tbody>
                </table>
                <div class="row">

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label> Payment Status</label>
                            <select class="form-control select"  v-model="form.payment_status" :disabled=" refund == 1? true : false">
                                <option   v-for="row in payment_status" :key="row"  :value="row">{{ row }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label> Payment Amount</label>
                            <input  :disabled=" refund == 1? true : false" type="number" min="0" class="form-control"  v-model="form.payment_amount">
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label> Payment Method</label>
                            <select class="form-control select"  v-model="form.payment_method" :disabled=" refund == 1? true : false">
                                <option  v-for="row in payment_methods" :key="row"  :value="row">{{ row }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label> Bill Status</label>
                            <select class="form-control select"  v-model="form.status" :disabled=" refund == 1? true : false">
                                <option  v-for="row in status" :key="row"  :value="row">{{ row }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label> Remarks</label>
                            <textarea  :disabled=" refund == 1? true : false"
                                v-model="form.remarks"
                                cols="30"
                                rows="2"
                                class="form-control"
                            ></textarea>
                        </div>
                    </div>


                    <div class="col-sm-3"  v-if="refund == 1">
                        <div class="form-group">
                            <label> Refund Remarks</label>
                            <textarea
                                :disabled="approve == 1? true : false"
                                v-model="form.refund_remarks"
                                cols="30"
                                rows="2"
                                class="form-control"
                            ></textarea>
                        </div>
                    </div>

                </div>
                <div class="m-t-20 text-center">
                    <button
                        type="button"
                        class="btn btn-success submit-btn"
                        wire:click.prevent="store()"
                        :disabled="form_disable"
                        v-show="!editMode"
                        @click="save(form)"
                    >
                        Save
                    </button>
                    <button
                        type="button"
                        class="btn btn-success submit-btn"
                        wire:click.prevent="store()"
                        :disabled="form_disable"
                        v-show="editMode &&  !approve"
                        @click="update(form)"
                    >
                        {{ refund == 1 ? "Add Refund" : "Update"}}
                    </button>
                </div>
                <div class="m-t-20 text-center" v-if="($page.auth.user.role == 'FD' || $page.auth.user.role == 'Admin'  || $page.auth.user.email == 'abu.sayed@afc.com.bd' ) && form.refund_amount>0 && refund==1 && approve ==1">
                    <button
                        type="button"
                        class="btn btn-success submit-btn"
                        wire:click.prevent="store()"
                        :disabled="form_disable"
                        @click="approveRefund(form)"
                    >
                        Approve Refund
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink'
import Alert from '@/Pages/Component/Alert'
import {HTTP} from '@/utils/http-common'
import {error_message, success_message} from "@/utils/alert";
import Select2 from 'v-select2-component';

export default {
    components: {
        Alert,
        JetResponsiveNavLink,
        Select2
    },
        props: ['data', 'errors', 'editMode', 'refund', 'approve'],
        data() {
            return {
                form_disable: false,
                form: {
                    id: 0,
                    patient_id: this.data.patient_id ? this.data.patient_id : '',
                    item_type: 'SERVICE',
                    payment_status: 'DUE',
                    payment_method: 'CASH',
                    status: 'INCOMPLETE',
                    discount_type: 'Fixed',
                    discount_value: 0,
                    discount_amount: 0,
                    total_paid_amount: 0,
                    total_due_amount: 0,
                    pharmacy_invoice_number: '',
                    pharmacy_bill_amount: 0,
                    billing_id: '',
                    item_id: '',
                    doctor_id: '',
                    referred_personal_id: '',
                    item_qty: 1,
                    items: []
                },
                items: [],
                item: '',
                patient: '',
                item_types: ['SERVICE', 'PACKAGE', 'LABTEST', 'MEDICINE', 'OTHERS'],
                billing_types: ['OPD', 'IPD'],
                payment_status: ['DUE', 'PAID'],
                payment_methods: ['CASH', 'CARD', 'BANKTRANSFER', 'CHEQUE'],
                status: ['COMPLETE', 'INCOMPLETE'],
                discount_types: ['Percentage', 'Fixed'],
            }
        },
        created() {
            if (this.editMode) {
                this.form = this.data;
                this.form.item_type = 'SERVICE'
                this.form.item_id = ''
                this.form.item_qty = 1
                this.getServices();
            }
            this.getServices()
        },
        methods: {
            approveRefund: function (data) {
                this.$inertia.post('/opdBillings/' + data.id + '/approve', data)
            },
            onSetItem: function(){
                this.item = this.items.find(item => item.id == this.form.item_id);
            },
            onSelectGetItems: function(){
                this.patient = this.data.patients.find(item => item.id == this.form.patient_id);
                if(this.form.patient_id === '') {
                    return 0
                }
                this.getItems();
            },
            getItems: function () {
                HTTP.get('/opdBillings/'+this.form.patient_id+'/billItems?bill_id='+this.form.id)
                    .then((response)=>{
                        this.form.items = response.data.items;
                    })
            },
            save: function (data) {
                if(this.form.items.length <= 0){
                    return 0;
                }
                this.form_disable = true;
                this.$inertia.post('/opdBillings', this.form)
            },
            edit: function (data) {
                this.form = Object.assign({}, data);
                this.editMode = true;
                // this.getItems();
            },
            update: function (data) {
                this.form_disable = true;
                data._method = 'PUT';
                this.$inertia.post('/opdBillings/' + data.id, data)
            },
            updateItem: function (data) {
                data['patient_id'] = this.form.patient_id
                data['doctor_id'] = this.form.doctor_id
                data['bill_id'] = this.form.id
                HTTP.post('opdBillings/updateItem', data)
                    .then((response)=>{
                        this.getItems();
                        console.log(response)
                    }).catch(function (error) {
                    error_message(error.response.data.errors)
                })
            },
            getServices: function (){
                HTTP.get('/opdBillings/getServices/'+this.form.item_type)
                    .then((response)=>{
                        this.items = response.data.items
                    })
            },
            addItem: function (){
                if(this.form.item_id == '' || this.form.item_qty == ''){
                    return;
                }

                var new_item = this.items.find(item => item.id == this.form.item_id)
                var item_data = {
                    bill_id: this.form.id,
                    pharmacy_invoice_number: this.form.pharmacy_invoice_number,
                    pharmacy_bill_amount: this.form.pharmacy_bill_amount,
                    is_for_pharmacy: new_item.is_for_pharmacy,
                    patient_id: this.form.patient_id,
                    doctor_id: this.form.doctor_id,
                    referred_personal_id: this.form.referred_personal_id,
                    item_type: this.form.item_type,
                    item_id: this.form.item_id,
                    item_name: new_item.name,
                    item_code: new_item.code,
                    item_qty: this.form.item_qty,
                    unit_tarrif: new_item.tariff,
                    discount: 0,
                }

                HTTP.post('opdBillings/addItem', item_data)
                    .then((response)=>{
                        console.log(response)
                        success_message((response.data.message));
                        this.getItems();
                    }).catch(function (error) {
                    error_message(error.response.data.errors)
                })

            },
            removeItem: function(item){
                if(item.id){
                    if (!confirm('Are you sure want to remove?')) return;
                    this.$inertia.post('/opdBillings/removeItem/' + item.id, item)
                }
                var index = this.form.items.indexOf(item)
                this.form.items.splice(index, 1)
            },

            getTotalDiscount: function(){
                return this.form.items.reduce(
                    ( accumulator, currentValue ) => accumulator + parseFloat(currentValue.discount),
                    0
                )
            },
            getSpecialDiscount: function(){
                let total = this.getTotal();
                if (this.form.discount_type === "Percentage"){
                    return (total * this.form.discount_value) / 100;
                }else{
                    return this.form.discount_value;
                }
            },
            getTotal: function(){
                return this.form.items.reduce(
                    ( accumulator, currentValue ) => accumulator +
                        ((currentValue.item_qty * currentValue.unit_tarrif) - currentValue.discount),
                    0
                )
            },
            getGrandTotal: function(){
                return  this.getTotal() - this.getSpecialDiscount();
            }
        }
    }
</script>
