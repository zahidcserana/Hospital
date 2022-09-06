<template>
<div>
     <div class="row">
        <div v-if="$page.auth.user.menu_access.includes('s2h_requisitions')" class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
            <div class="dash-widget">
                <span class="dash-widget-bg1"><i class="fa fa-shopping-cart" aria-hidden="true"></i></span>
                <div class="dash-widget-info text-right">
                    <h3>{{ param.s2hPendingRequisitions }}</h3>
                    <jet-responsive-nav-link :href="route('requisitions.index')" :active="route().current('requisitions.index')">
                        <i class="fa fa-shopping-bag" aria-hidden="true"></i>
                        <span class="widget-title1">S2H Pending Requisitions <i class="fa fa-check" aria-hidden="true"></i></span>
                    </jet-responsive-nav-link>
                </div>
            </div>
        </div>
        <div v-if="$page.auth.user.menu_access.includes('s2h_requisitions')" class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
            <div class="dash-widget">
                <span class="dash-widget-bg2"><i class="fa fa-medkit"></i></span>
                <div class="dash-widget-info text-right">
                    <h3>{{ param.s2hApprovedRequisitions }}</h3>
                    <jet-responsive-nav-link :href="route('requisitions.index')" :active="route().current('requisitions.index')">
                        <i class="fa fa-shopping-bag" aria-hidden="true"></i>
                        <span class="widget-title2">S2H Approved Requisitions <i class="fa fa-check" aria-hidden="true"></i></span>
                    </jet-responsive-nav-link>
                </div>
            </div>
        </div>
        <div v-if="$page.auth.user.menu_access.includes('d2s_requisitions')" class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
            <div class="dash-widget">
                <span class="dash-widget-bg3"><i class="fa fa-shopping-cart" aria-hidden="true"></i></span>
                <div class="dash-widget-info text-right">
                    <h3>{{ param.d2sPendingRequisitions }}</h3>
                    <jet-responsive-nav-link :href="route('dept_requisitions')" :active="route().current('dept_requisitions')">
                        <i class="fa fa-shopping-bag" aria-hidden="true"></i>
                        <span class="widget-title3">D2S Pending Requisitions <i class="fa fa-check" aria-hidden="true"></i></span>
                    </jet-responsive-nav-link>
                </div>
            </div>
        </div>
        <div v-if="$page.auth.user.menu_access.includes('d2s_requisitions')" class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
            <div class="dash-widget">
                <span class="dash-widget-bg4"><i class="fa fa-medkit" aria-hidden="true"></i></span>
                <div class="dash-widget-info text-right">
                    <h3>{{ param.d2sApprovedRequisitions }}</h3>
                    <jet-responsive-nav-link :href="route('dept_requisitions')" :active="route().current('dept_requisitions')">
                        <i class="fa fa-shopping-bag" aria-hidden="true"></i>
                        <span class="widget-title4">D2S Approved Requisitions <i class="fa fa-check" aria-hidden="true"></i></span>
                    </jet-responsive-nav-link>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
    import JetApplicationLogo from '@/Jetstream/ApplicationLogo'
    import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink'

    export default {
        props: ['param'],
        components: {
            JetApplicationLogo,
            JetResponsiveNavLink
        },
        props: ['param']
    }
</script>
