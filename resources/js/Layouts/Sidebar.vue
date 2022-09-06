<template>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-inner "  v-slimscroll="options">
            <div id="sidebar-menu" class="sidebar-menu">
                <ul>
                    <li class="menu-title">Main</li>
                    <li>
                        <inertia-link href="/dashboard"><i class="fa fa-dashboard"></i> <span>Dashboard</span></inertia-link>
                    </li>
                    <li v-if="$page.auth.user.menu_access.includes('users')" class="menu-title">USERS</li>
                    <li class="submenu">
                        <a href="#"><i class="fa fa-user-md"></i> <span> User Management</span> <span class="menu-arrow"></span></a>
                        <ul class="child-menu" :style="getMenu('user')">
                            <li :class="getRoute('patients.index')"  v-if="$page.auth.user.menu_access.includes('patients')">
                                <a :href="route('patients.index')" :active="route().current('patients.index')">
                                    <i class="fa fa-medkit" aria-hidden="true"></i>
                                    <span> Patients </span>
                                </a>
                            </li>

                            <li :class="getRoute('doctors.index')"  v-if="$page.auth.user.menu_access.includes('doctors')">
                                <a :href="route('doctors.index')" :active="route().current('doctors.index')">
                                    <i class="fa fa-users" aria-hidden="true"></i>
                                    <span> Doctors </span>
                                </a>
                            </li>
                            <li :class="getRoute('users.index')"  v-if="$page.auth.user.menu_access.includes('system_user')">
                                <a :href="route('users.index')" :active="route().current('users.index')">
                                    <i class="fa fa-users" aria-hidden="true"></i>
                                    <span> User List </span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li v-if="$page.auth.user.menu_access.includes('accounts')" class="menu-title">ACCOUNTS</li>
                    <li v-if="$page.auth.user.menu_access.includes('accounts')" class="submenu">
                        <a href="#"><i class="fa fa-money"></i> <span> Billing</span> <span class="menu-arrow"></span></a>
                        <ul class="child-menu" :style="getMenu('account')">
                            <li :class="getRoute('opdBillings.index')"  v-if="$page.auth.user.menu_access.includes('opdBilling')">
                                <a :href="route('opdBillings.index')" :active="route().current('opdBillings.index')">
                                    <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                                    <span> OPD Billing </span>
                                </a>
                            </li>
                            <li  :class="getRoute('ipdBillings.index')"  v-if="$page.auth.user.menu_access.includes('ipdBilling')">
                                <a :href="route('ipdBillings.index')" :active="route().current('ipdBillings.index')">
                                    <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                                    <span> IPD Billing </span>
                                </a>
                            </li>

                            <li :class="getRoute('pending_refunds')" >
                                <a :href="route('pending_refunds')" :active="route().current('pending_refunds')"  v-if="$page.auth.user.menu_access.includes('pendingRefund')">
                                    <i class="fa fa-users"></i>
                                    <span>Pending Refund Bill </span>
                                </a>
                            </li>

                            <li>
                                <a :href="route('payments.index')" :active="route().current('payments.index')"  v-if="$page.auth.user.menu_access.includes('payment')">
                                    <i class="fa fa-money" aria-hidden="true"></i>
                                    <span> Payments </span>
                                </a>
                            </li>
                            <li :class="getRoute('patientAdmissions.index')">
                                <a :href="route('patientAdmissions.index')" :active="route().current('patientAdmissions.index')"  v-if="$page.auth.user.menu_access.includes('patientAdmission')">
                                    <i class="fa fa-medkit" aria-hidden="true"></i>
                                    <span> Patient Admission </span>
                                </a>
                            </li>
                            <li  :class="getRoute('my_summery')">
                                <a :href="route('my_summery')" :active="route().current('my_summery')"  v-if="$page.auth.user.menu_access.includes('mySummery')">
                                    <i class="fa fa-list-ul"></i>
                                    <span>Collection Summery </span>
                                </a>
                            </li>

                            <li  :class="getRoute('referralSetting.index')">
                                <a :href="route('referralSetting.index')" :active="route().current('referralSetting.index')"  v-if="$page.auth.user.menu_access.includes('referralSetting')">
                                    <i class="fa fa-list-ul"></i>
                                    <span>Referral Setting </span>
                                </a>
                            </li>
                            <li  :class="getRoute('referralStatement')">
                                <a :href="route('referralStatement')" :active="route().current('referralStatement')"  v-if="$page.auth.user.menu_access.includes('referralSetting')">
                                    <i class="fa fa-list-ul"></i>
                                    <span>Referral Statement </span>
                                </a>
                            </li>

                            <li>
                                <a :href="route('corporateClients.index')" :active="route().current('corporateClients.index')"  v-if="$page.auth.user.menu_access.includes('corporateClient')">
                                    <i class="fa fa-users"></i>
                                    <span>Corporate Partners </span>
                                </a>
                            </li>

                            <!--                            <li>-->
<!--                                <jet-responsive-nav-link :href="route('opdServices.index')" :active="route().current('opdServices.index')">-->
<!--                                    <i class="fa fa-users" aria-hidden="true"></i>-->
<!--                                    <span> OPD Service </span>-->
<!--                                </jet-responsive-nav-link>-->
<!--                            </li>-->
<!--                            <li>-->
<!--                                <jet-responsive-nav-link :href="route('ipdServices.index')" :active="route().current('ipdServices.index')">-->
<!--                                    <i class="fa fa-users" aria-hidden="true"></i>-->
<!--                                    <span> IPD Service </span>-->
<!--                                </jet-responsive-nav-link>-->
<!--                            </li>-->

<!--                            <li>-->
<!--                                <jet-responsive-nav-link :href="route('pathologies.index')" :active="route().current('pathologies.index')">-->
<!--                                    <i class="fa fa-users" aria-hidden="true"></i>-->
<!--                                    <span> Pathology </span>-->
<!--                                </jet-responsive-nav-link>-->
<!--                            </li>-->

                            <li><a href="#">Medicine List</a></li>
                            <li><a href="#">Hospital Bed Plan</a></li>
                        </ul>
                    </li>

                    <li v-if="$page.auth.user.menu_access.includes('requisitions')" class="menu-title">REQUISITIONS</li>
                    <li v-if="$page.auth.user.menu_access.includes('s2h_requisitions')" class="submenu">
                        <a href="#"><i class="fa fa-edit"></i> <span> S2H Requisitions</span> <span class="menu-arrow"></span></a>
                        <ul class="child-menu" :style="getMenu('requisition')">
                            <li :class="getRoute('requisitions.index')">
                                <jet-responsive-nav-link :href="route('requisitions.index')" :active="route().current('requisitions.index')">
                                    <i class="fa fa-shopping-bag" aria-hidden="true"></i>
                                    <span> Requisitions </span>
                                </jet-responsive-nav-link>
                            </li>
                            <li :class="getRoute('inventories.index')">
                                <jet-responsive-nav-link :href="route('inventories.index')" :active="route().current('inventories.index')">
                                    <i class="fa fa-database" aria-hidden="true"></i>
                                    <span> Central Store Inventory </span>
                                </jet-responsive-nav-link>
                            </li>
                        </ul>
                    </li>
                    <li v-if="$page.auth.user.menu_access.includes('d2s_requisitions')" class="submenu">
                        <a href="#"><i class="fa fa-edit"></i> <span> D2S Requisitions</span> <span class="menu-arrow"></span></a>
                        <ul class="child-menu" :style="getMenu('d2s_requisition')">
                            <li :class="getRoute('dept_requisitions')">
                                <jet-responsive-nav-link :href="route('dept_requisitions')" :active="route().current('dept_requisitions')">
                                    <i class="fa fa-shopping-bag" aria-hidden="true"></i>
                                    <span> Requisitions </span>
                                </jet-responsive-nav-link>
                            </li>
                            <li :class="getRoute('dept_inventories')">
                                <jet-responsive-nav-link :href="route('dept_inventories')" :active="route().current('dept_inventories')">
                                    <i class="fa fa-shopping-bag" aria-hidden="true"></i>
                                    <span> Department Inventory </span>
                                </jet-responsive-nav-link>
                            </li>
                        </ul>
                    </li>
                    <li v-if="$page.auth.user.menu_access.includes('consumptions')"  :class="getRoute('consumptions')">
                        <jet-responsive-nav-link :href="route('consumptions.index')" :active="route().current('consumptions.index')">
                            <i class="fa fa-edit" aria-hidden="true"></i>
                            <span> Dept. Consumptions </span>
                        </jet-responsive-nav-link>
                    </li>

                    <li v-if="$page.auth.user.menu_access.includes('medicine-consumption')"  :class="getRoute('medicine-consumption')">
                        <jet-responsive-nav-link :href="route('medicine-consumption.index')" :active="route().current('medicine-consumption.index')">
                            <i class="fa fa-edit" aria-hidden="true"></i>
                            <span> Medicine Consumptions </span>
                        </jet-responsive-nav-link>
                    </li>

                    <li v-if="$page.auth.user.menu_access.includes('reports')" class="menu-title">REPORTS</li>
                    <li v-if="$page.auth.user.menu_access.includes('reports')" class="submenu">
                        <a href="#"><i class="fa fa-table"></i> <span> All Reports</span> <span class="menu-arrow"></span></a>
                        <ul style="display: none;">
                            <li><a href="#">S2H Requisition Report</a></li>
                            <li><a href="#">D2S Requisition Report</a></li>
                            <li><a href="#">OPD Billing Report</a></li>
                            <li><a href="#">IPD Billing Report</a></li>
                            <li><a href="#">Inventory Report (Dept)</a></li>
                            <li><a href="#">Inventory Report (Store)</a></li>
                        </ul>
                    </li>

                    <li v-if="$page.auth.user.menu_access.includes('settings')" class="menu-title">SETTINGS</li>
                    <li v-if="$page.auth.user.menu_access.includes('settings')" class="submenu">
                        <a href="#"><i class="fa fa-cog"></i> <span> Common Settings</span> <span class="menu-arrow"></span></a>
                        <ul class="child-menu" :style="getMenu('settings')">

                            <li>
                                <jet-responsive-nav-link :href="route('hospitals.index')" :active="route().current('hospitals.index')">
                                    <i class="fa fa-hospital-o"></i>
                                    <span>Hospital </span>
                                </jet-responsive-nav-link>
                            </li>
                            <li>
                                <jet-responsive-nav-link :href="route('roles.index')" :active="route().current('roles.index')">
                                    <i class="fa fa-universal-access" aria-hidden="true"></i>
                                    <span> User Roles </span>
                                </jet-responsive-nav-link>
                            </li>

                            <li>
                                <jet-responsive-nav-link :href="route('departments.index')" :active="route().current('departments.index')">
                                    <i class="fa fa-users" aria-hidden="true"></i>
                                    <span> Departments </span>
                                </jet-responsive-nav-link>
                            </li>

                            <li>
                                <jet-responsive-nav-link :href="route('designations.index')" :active="route().current('designations.index')">
                                    <i class="fa fa-users" aria-hidden="true"></i>
                                    <span> Designations </span>
                                </jet-responsive-nav-link>
                            </li>

                            <li>
                                <jet-responsive-nav-link :href="route('doctor-specialities.index')" :active="route().current('doctor-specialities.index')">
                                    <i class="fa fa-users" aria-hidden="true"></i>
                                    <span> Doctor Specialities </span>
                                </jet-responsive-nav-link>
                            </li>

                            <li>
                                <jet-responsive-nav-link :href="route('pathologies.index')" :active="route().current('pathologies.index')">
                                    <i class="fa fa-users" aria-hidden="true"></i>
                                    <span> Pathology </span>
                                </jet-responsive-nav-link>
                            </li>

                            <li>
                                <jet-responsive-nav-link :href="route('suppliers.index')" :active="route().current('suppliers.index')">
                                    <i class="fa fa-users" aria-hidden="true"></i>
                                    <span> Supplier Management </span>
                                </jet-responsive-nav-link>
                            </li>

                            <li>
                                <jet-responsive-nav-link :href="route('stores.index')" :active="route().current('stores.index')">
                                    <i class="fa fa-users" aria-hidden="true"></i>
                                    <span> Stores </span>
                                </jet-responsive-nav-link>
                            </li>

                            <li>
                                <jet-responsive-nav-link :href="route('product-categories.index')" :active="route().current('product-categories.index')">
                                    <i class="fa fa-users" aria-hidden="true"></i>
                                    <span> Product Category </span>
                                </jet-responsive-nav-link>
                            </li>

                            <li>
                                <jet-responsive-nav-link :href="route('product-brands.index')" :active="route().current('product-brands.index')">
                                    <i class="fa fa-users" aria-hidden="true"></i>
                                    <span> Product Brand </span>
                                </jet-responsive-nav-link>
                            </li>

                            <li>
                                <jet-responsive-nav-link :href="route('product-models.index')" :active="route().current('product-models.index')">
                                    <i class="fa fa-users" aria-hidden="true"></i>
                                    <span> Product Model </span>
                                </jet-responsive-nav-link>
                            </li>

                            <li>
                                <jet-responsive-nav-link :href="route('products.index')" :active="route().current('products.index')">
                                    <i class="fa fa-users" aria-hidden="true"></i>
                                    <span> Product </span>
                                </jet-responsive-nav-link>
                            </li>

                            <li><a href="#">Madicine Types</a></li>
                            <li><a href="#">Medicne Manufecturer</a></li>
                            <li><a href="#">Medicines</a></li>
                            <li><a href="#">User Role Permissions</a></li>
                            <li><a href="#">Services</a></li>
                            <li><a href="#">Service Packages</a></li>
                        </ul>
                    </li>
                     <li v-if="$page.auth.user.menu_access.includes('settings')" class="submenu">
                        <a href="#"><i class="fa fa-cog"></i> <span> Service Settings</span> <span class="menu-arrow"></span></a>
                        <ul style="display: none;">
                            <li>
                                <jet-responsive-nav-link :href="route('opdServices.index')" :active="route().current('opdServices.index')">
                                    <i class="fa fa-users" aria-hidden="true"></i>
                                    <span> OPD Service </span>
                                </jet-responsive-nav-link>
                            </li>
                            <li>
                                <jet-responsive-nav-link :href="route('ipdServices.index')" :active="route().current('ipdServices.index')">
                                    <i class="fa fa-users" aria-hidden="true"></i>
                                    <span> IPD Service </span>
                                </jet-responsive-nav-link>
                            </li>
                            <li>
                                <jet-responsive-nav-link :href="route('opdServicePackages.index')" :active="route().current('opdServicePackages.index')">
                                    <i class="fa fa-users" aria-hidden="true"></i>
                                    <span> OPD Service Package </span>
                                </jet-responsive-nav-link>
                            </li>
                            <li>
                                <jet-responsive-nav-link :href="route('ipdServicePackages.index')" :active="route().current('ipdServicePackages.index')">
                                    <i class="fa fa-users" aria-hidden="true"></i>
                                    <span> IPD Service Package </span>
                                </jet-responsive-nav-link>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
import JetApplicationMark from '@/Jetstream/ApplicationMark'
import JetDropdown from '@/Jetstream/Dropdown'
import JetDropdownLink from '@/Jetstream/DropdownLink'
import JetNavLink from '@/Jetstream/NavLink'
import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink'

export default {
    props: ['croute'],
    data () {
        return {
            options:{
                height:'100%',
                size:2
            }
        }
    },
    components: {
        JetApplicationMark,
        JetDropdown,
        JetDropdownLink,
        JetNavLink,
        JetResponsiveNavLink,
    },
    methods: {
        getRoute(path) {
            if(this.croute === path) {
                return 'active'
            }
        },
        getAccess(user) {
            console.log(user)
        },
        getMenu(menu) {
            const d2s_requisition = ['dept_requisitions', 'dept_inventories']
            const user = ['patients.index', 'doctors.index', 'users.index']
            const account = ['opdBillings.index', 'my_summery', 'pending_refunds', 'ipdBillings.index', 'payments.index', 'patientAdmissions.index']
            const requisition = ['requisitions.index', 'inventories.index']
            const settings = [ 'hospitals.index', 'roles.index',
                            'departments.index', 'designations.index', 'doctor-specialities.index',
                            'pathologies.index', 'suppliers.index', 'product-categories.index',
                            'product-brands.index', 'product-models.index', 'requisition-categories.index',
                            'stores.index', 'products.index']
            if(menu === 'user' && user.includes(this.croute)) {
                return 'display: block;'
            } else if(menu === 'account' && account.includes(this.croute)) {
                return 'display: block;'
            } else if(menu === 'requisition' && requisition.includes(this.croute)) {
                return 'display: block;'
            } else if(menu === 'settings' && settings.includes(this.croute)) {
                return 'display: block;'
            } else if(menu === 'd2s_requisition' && d2s_requisition.includes(this.croute)) {
                return 'display: block;'
            }
            return 'display: none;'
        }
    }
}
</script>

<style>
.child-menu {
    padding-left: 10%!important;
}
</style>
