<template>
    <div class="patient-create">
        <slot name="header"></slot>

        <div class="row  card-box">
            <div class="col-lg-8 offset-lg-2">
                <div class="card-box">
                    <form>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Patient <span class="text-red">*</span></label>

                                    <Select2
                                        v-model="form.patient_id"
                                        placeholder="Select Patient"
                                        :options="param.patients"  />

<!--                                    <select class="form-control select"  v-model="form.patient_id">-->
<!--                                        <option value="" selected>&#45;&#45;Select Patient&#45;&#45;</option>-->
<!--                                        <option  v-for="row in param.patients" :key="row.id" :value="row.id">{{ row.name }}</option>-->
<!--                                    </select>-->

                                    <div class="text-danger" v-if="errors.patient_id">{{ errors.patient_id[0] }}</div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label> Consultant <span class="text-red">*</span></label>

                                    <Select2
                                        v-model="form.doctor_id"
                                        placeholder="Select Consultant"
                                        :options="param.doctors"  />
                                    <div class="text-danger" v-if="errors.doctor_id">{{ errors.doctor_id[0] }}</div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label> Referred Personal</label>

                                    <Select2
                                        v-model="form.referred_personal_id"
                                        placeholder="Select Referred Personal"
                                        :options="param.doctors"  />
                                    <div class="text-danger" v-if="errors.referred_personal_id">{{ errors.referred_personal_id[0] }}</div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Referred By <span class="text-red">*</span></label>
                                    <input class="form-control" type="text" v-model="form.referred_by" />
                                    <div class="text-danger" v-if="errors.referred_by">{{ errors.referred_by[0] }}</div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Bed No. <span class="text-red">*</span></label>
                                    <select class="form-control select"  v-model="form.room">
                                        <option value="" selected>--Select Bed--</option>
                                        <option  v-for="row in param.rooms" :key="row.id" :value="row.room_number">{{ row.room_number }}</option>
                                    </select>
                                    <div class="text-danger" v-if="errors.room">{{ errors.room[0] }}</div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Admitted From</label>
                                    <select class="form-control select" v-model="form.admitted_from">
                                        <option value="">----select----</option>
                                        <option value="OPD">OPD</option>
                                        <option value="Emergency">Emergency</option>
                                    </select>
                                    <div class="text-danger" v-if="errors.admitted_from">{{ errors.admitted_from[0] }}</div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Description</label>
                                    <input class="form-control" type="text" v-model="form.description" />
                                    <div class="text-danger" v-if="errors.description">{{ errors.description[0] }}</div>

                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Admission Date <span class="text-red">*</span></label>
                                    <VueCtkDateTimePicker v-model="form.admission_date" format="YYYY-MM-DD hh:mm a" />
                                    <div class="text-danger" v-if="errors.admission_date">{{ errors.admission_date[0] }}</div>

                                </div>
                            </div>

                            <div class="col-sm-6" v-if="form.id > 0">
                                <div class="form-group">
                                    <label>Release Date</label>
                                    <p v-if="param.data.bill && param.data.bill.total_due_amount <= 0">
                                        <VueCtkDateTimePicker v-model="form.release_date" format="YYYY-MM-DD hh:mm a" />
                                    </p>
                                    <p v-if="param.data.bill && param.data.bill.total_due_amount > 0">
                                        <VueCtkDateTimePicker disabled v-model="form.release_date" format="YYYY-MM-DD hh:mm a" />
                                    </p>
<!--                                    <datetime v-model="form.release_date" type="datetime" input-class="form-control" format="yyyy-MM-dd hh:mm a"></datetime>-->
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Under Package <span class="text-red">*</span></label>
                                    <select class="form-control select" v-model="form.under_package">
                                        <option value="">----select----</option>
                                        <option value="YES">Yes</option>
                                        <option value="NO">No</option>
                                    </select>
                                    <div class="text-danger" v-if="errors.under_package">{{ errors.under_package[0] }}</div>

                                </div>
                            </div>

                            <div class="col-sm-6" v-if="form.under_package=='YES'">
                                <div class="form-group">
                                    <label>Package  <span class="text-red">*</span></label>
                                    <select class="form-control select" v-model="form.package_id"  @change="onSelectBill()">
                                        <option  value="">----select----</option>
                                        <option  v-for="row in param.packages" :key="row.id" :value="row.id">{{ row.name }} - {{row.tariff}}</option>
                                    </select>
                                    <div class="text-danger" v-if="errors.package_id">{{ errors.package_id[0] }}</div>
                                </div>
                            </div>

                            <div class="col-sm-6"  v-if="!form.id">
                                <div class="form-group">
                                    <label>Advance Payment Amount</label>
                                    <input class="form-control" type="text" v-model="form.advance_payment_amount" />
                                    <div class="text-danger" v-if="errors.advance_payment_amount">{{ errors.advance_payment_amount[0] }}</div>
                                    <div class="text-danger" v-if="errors.payment_amount">Payment amount must be equal to package price!</div>
                                </div>
                            </div>


                            <div class="col-sm-6"   v-if="!form.id">
                                <div class="form-group">
                                    <label>Payment Type </label>
                                    <select class="form-control select" v-model="form.payment_type">
                                        <option value="">----select----</option>
                                        <option value="Cash">Cash</option>
                                        <option value="Card">Card</option>
                                    </select>
                                    <div class="text-danger" v-if="errors.payment_type">{{ errors.payment_type[0] }}</div>

                                </div>
                            </div>


                            <div class="col-sm-6"    v-if="!form.id">
                                <div class="form-group">
                                    <label>Status <span class="text-red">*</span></label>
                                    <select class="form-control select" v-model="form.status">
                                        <option value="Admission">Admission</option>
                                        <option value="Release">Release</option>
                                    </select>
                                    <div class="text-danger" v-if="errors.status">{{ errors.status[0] }}</div>

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
    </div>
</template>

<script>
import Alert from '@/Pages/Component/Alert'
import moment from "moment";
import Select2 from 'v-select2-component';

export default {
    components: {
        Alert,
        Select2
    },
    props: ['param', 'editMode', 'errors'],
    data () {
        return {
            package: '',
            form: {
                id: undefined,
                patient_id: '',
                doctor_id: '',
                payment_type: 'Cash',
                under_package: 'NO',
                package_id: '',
                floor: null,
                room: '',
                bed: '',
                description: null,
                admission_date: moment(),
                release_date: null,
                status: 'Admission',
            }
        }
    },
    created () {
        if (this.editMode) {
            this.form = this.param.data
        }
    },
    methods: {
        onSelectBill: function(){
            var p = this.param.packages.find(item => item.id == this.form.package_id);
            this.package = p;
        },
        reset: function () {
            this.form = {}
        },
        save: function (data) {
            // console.log(data)
            data['admission_date'] = moment(data.admission_date).format('YYYY-MM-DD HH:mm:ss')
            if(data.release_date){
                data['release_date'] = moment(data.release_date).format('YYYY-MM-DD HH:mm:ss')
            }
            console.log(data)
            this.$inertia.post('/patientAdmissions', data)
        },
        update: function (data) {
            data['admission_date'] = moment(data.admission_date).format('YYYY-MM-DD HH:mm:ss')
            if(data.release_date){
                data['release_date'] = moment(data.release_date).format('YYYY-MM-DD HH:mm:ss')
            }
            data._method = 'PUT'
            this.$inertia.post('/patientAdmissions/' + data.id, data)
        }
    }
}
</script>
