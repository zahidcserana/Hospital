<template>
    <div class="hospital-create">
        <slot name="header"></slot>

        <div class="row card-box">
            <div class="col-lg-8 offset-lg-2">
                <form>
                    <div class="row">

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Doctor Code</label>
                                <input class="form-control" readonly type="text" v-model="form.code"/>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>{{ form.type != 'REFERRED PERSONAL' ? 'Doctor' : null }} Name <span class="text-red">*</span></label>
                                <input class="form-control" type="text" v-model="form.name"/>
                                <div class="text-danger" v-if="errors.name">{{ errors.name[0] }}</div>
                            </div>
                        </div>


                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Type <span class="text-red">*</span></label>
                                <select class="form-control select"  v-model="form.type">
                                    <option value="" selected>Select Type</option>
                                    <option  v-for="row in data.types" :key="row" :value="row">{{ row }}</option>
                                </select>
                                <div class="text-danger" v-if="errors.type">{{ errors.type[0] }}</div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label> Mobile <span class="text-red">*</span></label>
                                <input class="form-control" type="text" v-model="form.mobile"/>
                                <div class="text-danger" v-if="errors.mobile">{{ errors.mobile[0] }}</div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label> Email</label>
                                <input class="form-control" type="email" v-model="form.email"/>
                            </div>
                        </div>


                        <div class="col-sm-6" v-if="form.type == 'INHOUSE'">
                            <div class="form-group">
                                <label>Hospital <span class="text-red">*</span></label>
                                <select class="form-control select"  v-model="form.hospital_id">
                                    <option value="" selected>Select Hospital</option>
                                    <option  v-for="hospital in data.hospitals" :key="hospital.id" :value="hospital.id">{{ hospital.name }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Department <span class="text-red">*</span></label>
                                <select class="form-control select"  v-model="form.department_id">
                                    <option value="" selected>Select Department</option>
                                    <option  v-for="department in data.departments" :key="department.id"  :value="department.id">{{ department.name }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Designation <span class="text-red">*</span></label>
                                <select class="form-control select"  v-model="form.designation_id">
                                    <option value="" selected>Select Department</option>
                                    <option  v-for="row in data.designations" :key="row.id"  :value="row.id">{{ row.title }}</option>
                                </select>
                                <div class="text-danger" v-if="errors.designation_id">{{ errors.designation_id[0] }}</div>
                            </div>
                        </div>

                        <div class="col-sm-6" v-if="form.type != 'REFERRED PERSONAL'">
                            <div class="form-group">
                                <label>Speciality <span class="text-red">*</span></label>
                                <select class="form-control select"  v-model="form.doctors_specialities_id">
                                    <option value="" selected>Select Department</option>
                                    <option  v-for="row in data.specialities" :key="row.id"  :value="row.id">{{ row.name }}</option>
                                </select>
                                <div class="text-danger" v-if="errors.doctors_specialities_id">{{ errors.doctors_specialities_id[0] }}</div>

                            </div>
                        </div>


                        <div class="col-sm-6"  v-if="form.type != 'REFERRED PERSONAL'">
                            <div class="form-group">
                                <label> BMDC Reg No</label>
                                <input class="form-control" type="text" v-model="form.bmdc_id"/>
                            </div>
                        </div>

                        <div class="col-sm-6" v-if="form.type != 'INHOUSE'">
                            <div class="form-group">
                                <label> Institute Name</label>
                                <input class="form-control" type="text" v-model="form.institute_name"/>
                            </div>
                        </div>

                        <div class="col-sm-6" v-if="form.type != 'INHOUSE'">
                            <div class="form-group">
                                <label>Chamber Address</label>
                                <textarea
                                    v-model="form.chamber_address"
                                    cols="30"
                                    rows="4"
                                    class="form-control"
                                ></textarea>
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
                            <label>Doctor Status <span class="text-red">*</span></label><br/>
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
                            class="btn btn-success submit-btn"
                            wire:click.prevent="store()"
                            v-show="!editMode"
                            @click="save(form)"
                        >
                            Save
                        </button>
                        <button
                            type="button"
                            class="btn btn-success submit-btn"
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
        props: ['data', 'errors', 'editMode'],
        data() {
            return {
                isOpen: false,
                photo: '',
                form: {
                    id: undefined,
                    name: null,
                    code: this.data.code,
                    hospital_id: '',
                    department_id: '',
                    doctors_specialities_id: '',
                    designation_id: '',
                    type: 'INHOUSE',
                    mobile: null,
                    email: null,
                    bmdc_id: null,
                    institute_name: null,
                    chamber_address: null,
                    status: 'ACTIVE',
                },
            }
        },
        created() {
            if (this.editMode) {
                this.form = this.data
            }
        },
        methods: {
            reset: function () {
                this.form = {
                    name: null,
                    hospital_id: null,
                    department_id: null,
                    doctors_specialities_id: null,
                    designation_id: null,
                    type: null,
                    mobile: null,
                    email: null,
                    bmdc_id: null,
                    institute_name: null,
                    chamber_address: null,
                    status: 'ACTIVE',
                }
            },
            onFileChange: function (e) {
                this.photo = e.target.files[0]
            },
            save: function (params) {
                var data = new FormData()
                data.append('name', params.name || '')
                data.append('code', params.code || '')
                data.append('hospital_id', params.hospital_id || '')
                data.append('department_id', params.department_id || '')
                data.append('doctors_specialities_id', params.doctors_specialities_id || '')
                data.append('designation_id', params.designation_id || '')
                data.append('type', params.type || '')
                data.append('mobile', params.mobile || '')
                data.append('email', params.email || '')
                data.append('bmdc_id', params.bmdc_id || '')
                data.append('institute_name', params.institute_name || '')
                data.append('chamber_address', params.chamber_address || '')
                data.append('status', params.status || '')
                data.append('photo', this.photo || '')
                this.$inertia.post('/doctors', data)
            },
            edit: function (data) {
                this.form = Object.assign({}, data);
                this.editMode = true;
                this.openModal();
            },
            update: function (params) {
                var data = new FormData()
                data.append('id', params.id || '')
                data.append('name', params.name || '')
                data.append('code', params.code || '')
                data.append('hospital_id', params.hospital_id || '')
                data.append('department_id', params.department_id || '')
                data.append('doctors_specialities_id', params.doctors_specialities_id || '')
                data.append('designation_id', params.designation_id || '')
                data.append('type', params.type || '')
                data.append('mobile', params.mobile || '')
                data.append('email', params.email || '')
                data.append('bmdc_id', params.bmdc_id || '')
                data.append('institute_name', params.institute_name || '')
                data.append('chamber_address', params.chamber_address || '')
                data.append('status', params.status || '')
                data.append('photo', this.photo || '')
                data.append('_method', 'PUT')
                this.$inertia.post('/doctors/' + params.id, data)
            }
        }
    }
</script>
