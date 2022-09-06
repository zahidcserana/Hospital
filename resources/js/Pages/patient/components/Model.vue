<template>
    <div class="patient-create">
        <slot name="header"></slot>

        <div class="row  card-box">
            <div class="col-lg-8 offset-lg-2">
                    <form>
                        <div class="row">


                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Patient Name <span class="text-red">*</span></label>
                                    <input class="form-control" type="text" v-model="form.name"/>
                                    <div class="text-danger" v-if="errors.name">{{ errors.name[0] }}</div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Patient Father Name</label>
                                    <input class="form-control" type="text" v-model="form.father_name"/>
                                    <div class="text-danger" v-if="errors.father_name">{{ errors.father_name[0] }}</div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Patient Mother Name</label>
                                    <input class="form-control" type="text" v-model="form.mother_name"/>
                                    <div class="text-danger" v-if="errors.mother_name">{{ errors.mother_name[0] }}</div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Patient Husband Name</label>
                                    <input class="form-control" type="text" v-model="form.husband_name"/>
                                    <div class="text-danger" v-if="errors.husband_name">{{ errors.husband_name[0] }}</div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Patient Religion</label>
                                    <input class="form-control" type="text" v-model="form.religion"/>
                                    <div class="text-danger" v-if="errors.religion">{{ errors.religion[0] }}</div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Mobile <span class="text-red">*</span></label>
                                    <input class="form-control" type="text" v-model="form.mobile" />
                                    <div class="text-danger" v-if="errors.mobile">{{ errors.mobile[0] }}</div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input class="form-control" type="email" v-model="form.email" />
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Address <span class="text-red">*</span></label>
                                    <input class="form-control" type="text" v-model="form.address" />
                                    <div class="text-danger" v-if="errors.address">{{ errors.address[0] }}</div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Gender <span class="text-red">*</span></label>
                                    <select class="form-control select" v-model="form.gender">
                                        <option value="MALE">MALE</option>
                                        <option value="FEMALE">FEMALE</option>
                                        <option value="OTHER">OTHER</option>
                                    </select>
                                </div>
                                <div class="text-danger" v-if="errors.gender">{{ errors.gender[0] }}</div>

                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Occupation</label>
                                    <select class="form-control select" v-model="form.occupation">
                                        <option value="Business">Business</option>
                                        <option value="Service">Service</option>
                                        <option value="Doctor">Doctor</option>
                                        <option value="Engineer">Engineer</option>
                                        <option value="Agriculture">Agriculture</option>
                                        <option value="Housewife">Housewife</option>
                                        <option value="Student">Student</option>
                                        <option value="Teacher">Teacher</option>
                                        <option value="Others">Others</option>
                                    </select>
                                    <div class="text-danger" v-if="errors.occupation">{{ errors.occupation[0] }}</div>

                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Blood Group</label>
                                   <select class="form-control select" v-model="form.blood_group">
                                        <option value="Unknown">Unknown</option>
                                        <option value="A+">A+</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B-">B-</option>
                                        <option value="O+">O+</option>
                                        <option value="O-">O-</option>
                                        <option value="AB+">AB+</option>
                                        <option value="AB-">AB-</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Date of Birth</label>
                                    <datetime v-model="form.dob" type="date" input-class="form-control" format="yyyy-MM-dd" @close="getAge()"></datetime>

                                    <div class="text-danger" v-if="errors.dob">{{ errors.dob[0] }}</div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Age</label>
                                    <input class="form-control" min="0" type="number" v-model="form.age" @keyup="getBirthDate"/>

                                    <div class="text-danger" v-if="errors.age">{{ errors.age[0] }}</div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Corporate Client</label>
                                    <select class="form-control select" v-model="form.corporate_client_id">
                                        <option value="" selected>Select Corporate Client</option>
                                        <option  v-for="row in data.clients" :key="row.id" :value="row.id"> {{row.company_name}}</option>

                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-6" v-if="form.corporate_client_id > 0">
                                <div class="form-group">
                                    <label>Employee ID</label>
                                    <input class="form-control" type="text" v-model="form.employee_id" />
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Emergency Contact Person Name <span class="text-red">*</span></label>
                                    <input class="form-control" type="text" v-model="form.contact_person_name" />
                                    <div class="text-danger" v-if="errors.contact_person_name">{{ errors.contact_person_name[0] }}</div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Emergency Contact Person Mobile <span class="text-red">*</span> </label>
                                    <input class="form-control" type="text" v-model="form.contact_person_mobile" />
                                    <div class="text-danger" v-if="errors.contact_person_mobile">{{ errors.contact_person_mobile[0] }}</div>

                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Emergency Contact Person Relation <span class="text-red">*</span></label>
                                    <input class="form-control" type="text" v-model="form.contact_person_relation" />
                                    <div class="text-danger" v-if="errors.contact_person_relation">{{ errors.contact_person_relation[0] }}</div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Contact Person Address</label>
                                    <input class="form-control" type="text" v-model="form.contact_person_address" />
                                </div>
                            </div>


                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label> Profile Picture</label>
                                    <input class="form-control" type="file" @change="onFileChange"/>
                                </div>
                                <div v-if="form.photo">
                                    <img :src="form.photo" alt="" style="height: 80px; width: 80px;">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Status <span class="text-red">*</span></label><br />
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
import Alert from '@/Pages/Component/Alert'
import { Datetime } from 'vue-datetime';
import moment from "moment";

export default {
    components: {
        Alert,
        datetime: Datetime
    },
    props: ['data', 'editMode', 'errors'],
    data () {
        return {
            photo: null,
            form: {
                id: undefined,
                name: null,
                code: this.data.code,
                age: null,
                father_name: null,
                mother_name: null,
                husband_name: null,
                religion: null,
                mobile: null,
                email: null,
                address: null,
                gender: 'MALE',
                dob: null,
                blood_group: 'Unknown',
                nid: null,
                occupation: 'Business',
                contact_person_name: null,
                contact_person_mobile: null,
                contact_person_relation: null,
                contact_person_address: null,
                corporate_client_id: '',
                hospital_id: null,
                doctor_id: null,
                status: 'ACTIVE'
            }
        }
    },
    created () {
        if (this.editMode) {
            this.form = this.data
        }
    },
    methods: {
        getBirthDate: function() {
            var date = new Date()
            date.setMonth(date.getMonth() + 1)
            date.setFullYear(date.getFullYear() - this.form.age - 1)
            let result = ((date.getFullYear() + '-' + date.getMonth() ) + '-' + (date.getDate()))
            this.form.dob = moment(result).format('YYYY-MM-DD')
        },
        getAge: function() {
            let date2 = new Date(moment(this.form.dob).format('YYYY-MM-DD'))
            let date1 = new Date()
            const diffTime = Math.abs(date2 - date1)
            const diffYears = Math.floor(diffTime / (1000 * 60 * 60 * 24 * 365))
            this.form.age = diffYears
        },
        onFileChange: function (e) {
            this.photo = e.target.files[0]
        },
        reset: function () {
            this.form = {}
        },
        save: function (params) {
            var data = new FormData()
            data.append('name', params.name || '')
            data.append('father_name', params.father_name || '')
            data.append('mother_name', params.mother_name || '')
            data.append('husband_name', params.husband_name || '')
            data.append('religion', params.religion || '')
            data.append('code', params.code || '')
            data.append('dob', data.dob ? moment(data.dob).format('YYYY-MM-DD') : '')
            data.append('mobile', params.mobile || '')
            data.append('age', params.age || '')
            data.append('email', params.email || '')
            data.append('address', params.address || '')
            data.append('gender', params.gender || '')
            data.append('blood_group', params.blood_group || '')
            data.append('occupation', params.occupation || '')
            data.append('contact_person_name', params.contact_person_name || '')
            data.append('contact_person_mobile', params.contact_person_mobile || '')
            data.append('contact_person_relation', params.contact_person_relation || '')
            data.append('contact_person_address', params.contact_person_address || '')
            data.append('corporate_client_id', params.corporate_client_id || '')
            data.append('hospital_id', params.hospital_id || '')
            data.append('doctor_id', params.doctor_id || '')
            data.append('status', params.status || '')
            data.append('photo', this.photo || '')

            this.$inertia.post('/patients', data)
        },
        update: function (params) {
            var data = new FormData()
            data.append('id', params.id || '')
            data.append('name', params.name || '')
            data.append('father_name', params.father_name || '')
            data.append('mother_name', params.mother_name || '')
            data.append('code', params.code || '')
            data.append('dob', moment(params.dob).format('YYYY-MM-DD'))
            data.append('age', params.age || '')
            data.append('mobile', params.mobile || '')
            data.append('email', params.email || '')
            data.append('address', params.address || '')
            data.append('gender', params.gender || '')
            data.append('blood_group', params.blood_group || '')
            data.append('occupation', params.occupation || '')
            data.append('contact_person_name', params.contact_person_name || '')
            data.append('contact_person_mobile', params.contact_person_mobile || '')
            data.append('contact_person_relation', params.contact_person_relation || '')
            data.append('contact_person_address', params.contact_person_address || '')
            data.append('corporate_client_id', params.corporate_client_id || '')
            data.append('hospital_id', params.hospital_id || '')
            data.append('doctor_id', params.doctor_id || '')
            data.append('status', params.status || '')
            data.append('photo', this.photo || '')
            data.append('_method', 'PUT')

            this.$inertia.post('/patients/' + params.id, data)
        }
    }
}
</script>
